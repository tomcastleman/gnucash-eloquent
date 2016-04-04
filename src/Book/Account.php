<?php

namespace GnuCash\Models\Book;

use GnuCash\Models\Book;

abstract class Account extends Book
{
    protected $table = 'accounts';
    protected $primaryKey = 'guid';

    public function transactions()
    {
        $relation = $this->belongsToMany(
            $this->namespaceForBook(Transaction::class),
            'splits',
            'account_guid',
            'tx_guid'
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
        return $this->hasMany($this->namespaceForBook(Split::class), 'account_guid');
    }

    public static function scopeByCurrency($query, $currencyGuid)
    {
        return $query->where('commodity_guid', $currencyGuid);
    }

    public static function scopeActive($query, $currencyGuid)
    {
        return $query->where('commodity_guid', $currencyGuid)
            ->where('placeholder', 0)
            ->where('hidden', 0);
    }

    public function scopeByType($query, array $accountTypes, $currencyGuid)
    {
        static::scopeActive($query, $currencyGuid)
            ->whereIn('account_type', $accountTypes)
            ->orderBy('name', 'ASC');

        return $query;
    }

    public function scopeByIds($query, array $accountGuids, $currencyGuid)
    {
        static::scopeActive($query, $currencyGuid)
            ->whereIn('guid', $accountGuids)
            ->orderBy('name', 'ASC');

        return $query;
    }

    // Used for balance values (?)

    public function getInvert1Attribute()
    {
        if (in_array($this->account_type, [
            'CREDIT',
            'LIABILITY',
            'PAYABLE',
            'INCOME',
        ])) {
            return -1;
        }

        return 1;
    }

    // Used for transaction values (?)

    public function getInvert0Attribute()
    {
        if (in_array($this->account_type, [
            'CREDIT',
            'LIABILITY',
            'PAYABLE',
            'INCOME',
        ])) {
            return 1;
        }

        return -1;
    }

    public function getBalanceAttribute()
    {
        return $this->splits->sum(function (Split $split) {
            return $split->value_num / $split->value_denom;
        });
    }

    public function getBalanceNewAttribute()
    {
        return $this->splits->where('reconcile_state', 'n')->sum(function (Split $split) {
            return $split->value_num / $split->value_denom;
        });
    }

    public function getBalanceClearedAttribute()
    {
        return $this->splits->whereIn('reconcile_state', ['c', 'y'])->sum(function (Split $split) {
            return $split->value_num / $split->value_denom;
        });
    }

    public function getBalance0Attribute()
    {
        return $this->invert0 * $this->balance;
    }

    public function getBalance1Attribute()
    {
        return $this->invert1 * $this->balance;
    }

    public function getBalanceNew0Attribute()
    {
        return $this->invert0 * $this->balance_new;
    }

    public function getBalanceNew1Attribute()
    {
        return $this->invert1 * $this->balance_new;
    }

    public function getBalanceCleared0Attribute()
    {
        return $this->invert0 * $this->balance_cleared;
    }

    public function getBalanceCleared1Attribute()
    {
        return $this->invert1 * $this->balance_cleared;
    }
}
