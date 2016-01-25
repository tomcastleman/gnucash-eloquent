<?php

namespace GnuCash\Models\Tests;

use GnuCash\Models\Tests\Book\Test\Account;

class AccountTestCase extends EloquentTestCase
{
    protected $commodityGuid = 'a9eb70bbf388240b0fd9c0600a6bf28c';

    public function testGetActiveAccounts()
    {
        Account::create([
            'guid'           => '273e8599a0f2b682817d8afc70172a90',
            'name'           => 'Test Account 1',
            'account_type'   => 'EXPENSE',
            'commodity_guid' => $this->commodityGuid,
            'placeholder'    => 0,
            'hidden'         => 0,
        ]);
        Account::create([
            'guid'           => '1dd040f3bf05f83c55ad610fe4ca0525',
            'name'           => 'Test Account 2',
            'account_type'   => 'ASSET',
            'commodity_guid' => $this->commodityGuid,
            'placeholder'    => 0,
            'hidden'         => 0,
        ]);

        $accounts = Account::active($this->commodityGuid)->get();

        $this->assertEquals($accounts->count(), 2);
    }

    public function testGetAccountByIds()
    {
        $guid = '273e8599a0f2b682817d8afc70172a90';
        $accountName = 'Test Account';
        $accountType = 'EXPENSE';

        Account::create([
            'guid'           => $guid,
            'name'           => $accountName,
            'account_type'   => $accountType,
            'commodity_guid' => $this->commodityGuid,
            'placeholder'    => 0,
            'hidden'         => 0,
        ]);

        $account = Account::byIds([$guid], $this->commodityGuid)->first();

        $this->assertEquals($guid, $account->guid);
        $this->assertEquals($accountName, $account->name);
        $this->assertEquals($accountType, $account->account_type);
        $this->assertEquals($this->commodityGuid, $account->commodity_guid);
    }

    protected function table()
    {
        return 'accounts';
    }

    protected function columns()
    {
        return [
            'guid'           => 'string',
            'name'           => 'string',
            'account_type'   => 'string',
            'commodity_guid' => 'string',
            'placeholder'    => 'boolean',
            'hidden'         => 'boolean',
        ];
    }
}
