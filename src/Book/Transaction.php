<?php

namespace GnuCash\Models\Book;

use Carbon\Carbon;
use GnuCash\Models\Book;

abstract class Transaction extends Book
{
    protected $table = 'transactions';
    protected $primaryKey = 'guid';
    protected $dates = ['post_date', 'enter_date'];

    protected $replicationStatus = [];

    protected $fillable = [
        'currency_guid',
        'post_date',
        'description',
    ];

    public function accounts()
    {
        $relation = $this->belongsToMany(
            $this->namespaceForBook(Account::class),
            'splits',
            'tx_guid',
            'account_guid'
        )->withPivot(
            'guid',
            'memo',
            'action',
            'reconcile_state',
            'reconcile_date',
            'value_num',
            'value_denom',
            'quantity_num',
            'quantity_denom'
        );

        return $relation;
    }

    public function splits()
    {
        return $this->hasMany($this->namespaceForBook(Split::class), 'tx_guid');
    }

    public function scopeForAccount($query, $accountGuid, $maxDaysPast = 0)
    {
        $query->with('accounts', 'splits');
        $query->select('transactions.*');
        $query->join('splits', 'transactions.guid', '=', 'splits.tx_guid');
        $query->where('splits.account_guid', $accountGuid);

        if ($maxDaysPast > 0) {
            $minDate = Carbon::now()->subDays($maxDaysPast);
            $query->where('post_date', '>=', $minDate);
        }

        $query->orderBy('post_date', 'DESC');
        $query->orderBy('enter_date', 'DESC');

        return $query;
    }

    public function scopeById($query, $txGuid)
    {
        $query->with('accounts');
        $query->where('guid', $txGuid);

        return $query;
    }

    public function getAccountsExcept($accountGuid)
    {
        return $this->accounts->filter(function ($account) use ($accountGuid) {
            return $account->guid !== $accountGuid;
        });
    }

    public function getAccount($accountGuid)
    {
        return $this->accounts->where('guid', $accountGuid)->first();
    }

    public function getSplitForAccount($accountGuid)
    {
        return $this->getAccount($accountGuid)->pivot;
    }

    public function setReplicationStatus($replicationStatus)
    {
        $this->replicationStatus = $replicationStatus;
    }

    public function getBalanceAttribute()
    {
        return round($this->splits->sum('amount'), 2);
    }

    public function getInBalanceAttribute()
    {
        return $this->balance === 0.00;
    }

    public function getIsReplicatedAttribute()
    {
        return (bool) $this->replicationStatus;
    }

    public function getReplicationIsAgreedAttribute()
    {
        return $this->is_replicated && array_get(
            $this->replicationStatus, 'reconcile_state'
        ) !== Split::RECONCILE_STATE_NEW;
    }

    public function getReplicationIsConflictedAttribute()
    {
        return $this->is_replicated && array_get($this->replicationStatus, 'conflict');
    }

    public function getAllSplitsAreNewAttribute()
    {
        $splits = $this->splits;
        $newSplits = $this->splits->filter(function (Split $split) {
            return $split->is_new;
        });

        return $splits->count() === $newSplits->count();
    }
}
