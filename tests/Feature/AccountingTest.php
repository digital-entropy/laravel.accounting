<?php


namespace DigitalEntropy\Accounting\Tests\Feature;


use Carbon\Carbon;
use DigitalEntropy\Accounting\Contracts\Account;
use DigitalEntropy\Accounting\Contracts\EntryAuthor;
use DigitalEntropy\Accounting\Contracts\Journal\Entry;
use DigitalEntropy\Accounting\Contracts\Recordable;
use DigitalEntropy\Accounting\Ledger\Poster;
use DigitalEntropy\Accounting\Ledger\Recorder;
use DigitalEntropy\Accounting\Ledger\Report;
use DigitalEntropy\Accounting\Tests\TestCase;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;

class AccountingTest extends TestCase
{
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        // import the CreatePostsTable class from the migration
        include_once __DIR__ . './../../database/migrations/2020_10_30_000001_create_accounts_table.php';
        include_once __DIR__ . './../../database/migrations/2020_10_30_000002_create_journals_table.php';
        include_once __DIR__ . './../../database/migrations/2020_10_30_000003_create_journal_entries_table.php';
        include_once __DIR__ . './../../database/migrations/2020_11_27_000001_create_general_ledgers_table.php';

        // run the up() method of that migration class
        (new \CreateAccountsTable())->up();
        (new \CreateJournalsTable())->up();
        (new \CreateJournalEntriesTable())->up();
        (new \CreateGeneralLedgersTable())->up();
    }

    public function testCreateAccount()
    {
        $accountClass = config('accounting.models.account');

        $data = $this->sampleAccount(1);

        /** @var Account|Model $account */
        $account = new $accountClass();
        $account->fill($data);
        $account->save();

        $this->assertDatabaseCount('accounts', 1);
        $this->assertDatabaseHas('accounts', $data);
    }

    public function testUpdateAccount()
    {
        $accountClass = config('accounting.models.account');

        /** @var Account|Model $account */
        $account = new $accountClass();
        $account->fill($this->sampleAccount(1));
        $account->save();

        $changes = [
            'code' => 1101
        ];

        $account->fill($changes);
        $account->save();

        $this->assertDatabaseHas('accounts', $changes);
    }

    public function testDeleteAccount()
    {
        $accountClass = config('accounting.models.account');

        $data = $this->sampleAccount(1);

        /** @var Account|Model $account */
        $account = new $accountClass();
        $account->fill($data);
        $account->save();

        $account->delete();

        $this->assertSoftDeleted('accounts', $data);
    }

    private function sampleAccount(int $type, $cash = true)
    {
        $accountType = config('accounting.account_types.'.$type);

        return [
            'code' => $this->faker->randomNumber(4),
            'type_code' => $accountType,
            'is_cash' => $cash,
        ];
    }

    /**
     * @throws \DigitalEntropy\Accounting\Exceptions\NotBalanceJournalEntryException
     * @throws \DigitalEntropy\Accounting\Exceptions\StatementNotFoundException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function testBalancedEntry()
    {
        $accountClass = config('accounting.models.account');
        $accountTypes = config('accounting.account_types');

        // Create accounts
        foreach (array_flip($accountTypes) as $accountType) {
            /** @var Account|Model $account */
            $account = new $accountClass();
            $account->fill($this->sampleAccount(intval($accountType)));
            $account->save();
        }

        // Init
        /** @var Recorder $recorder */
        $recorder = $this->app->make(Recorder::class);

        /** @var Builder $leftAccountQuery */
        $leftAccountQuery = $accountClass::query();

        /** @var Account $left */
        $left = $leftAccountQuery
            ->where('type_code', $accountTypes[config('accounting.left')[0]])
            ->first();

        /** @var Builder $rightAccountQuery */
        $rightAccountQuery = $accountClass::query();

        /** @var Account $right */
        $right = $rightAccountQuery
            ->where('type_code', $accountTypes[config('accounting.right')[0]])
            ->first();

        $entryAuthor = [
            'author_id' => 1,
            'author_type' => EntryAuthor::class
        ];

        $recordable = [
            'recordable_id' => 1,
            'recordable_type' => Recordable::class
        ];

        $amount = 10000;

        // Perform double entry
        $recorder->debit($left, $amount, $entryAuthor)
            ->credit($right, $amount, $entryAuthor)
            ->record($recordable);

        // Make sure it's balanced
        /** @var Report $report */
        $report = $this->app->make(Report::class);

        $balanceSheet = $report->getStatement("balance_sheet");

        $this->assertEquals(0, $balanceSheet['total']);

        // Post into ledger.

        /** @var Poster $poster */
        $poster = $this->app->make(Poster::class);
        $poster->post();

        // Assert the account only two, since we just modify 2 account
        $this->assertDatabaseCount('general_ledgers', 2);

        /** @var Poster $poster */
        $poster = $this->app->make(Poster::class);
        $summary = $poster->summary()->get();

        $this->assertEquals(2, $summary->count());

        /** @var Poster $poster */
        $poster = $this->app->make(Poster::class);
        $summaryByType = $poster->summaryByAccountType();

        $this->assertEquals(2, $summaryByType->count());
    }
}
