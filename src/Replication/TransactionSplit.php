<?php

namespace GnuCash\Models\Replication;

use GnuCash\Models\Replication;
use Illuminate\Database\Eloquent\Builder;
use RuntimeException;

class TransactionSplit extends Replication
{
    protected $primaryKey = 'repl_id';

    protected $fillable = [
        'partner_id',
        'tx_guid',
        'split_guid',
        'hash',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'repl_id', 'repl_id');
    }

    public function opposite()
    {
        if (is_null($this->partner_id)) {
            throw new RuntimeException(self::class.' opposite relationship must be lazy eager loaded.');
        }

        return $this->hasOne(self::class, 'repl_id', 'repl_id')
            ->where('partner_id', !$this->partner_id ? 1 : 0);
    }

    public function scopeByPrimary($query, $replId, $partnerId)
    {
        $query->where('repl_id', $replId);
        $query->where('partner_id', $partnerId);

        return $query;
    }

    public function scopeExcludingSplits($query, array $excludeSplits)
    {
        $query->whereNotIn('split_guid', $excludeSplits);

        return $query;
    }

    public function scopeByPartnershipPartner($query, $partnershipName, $partnerId)
    {
        $query->join('transactions', 'transaction_splits.repl_id', '=', 'transactions.repl_id');
        $query->where('transactions.partnership_name', $partnershipName);
        $query->where('transaction_splits.partner_id', $partnerId);

        return $query;
    }

    protected function setKeysForSaveQuery(Builder $query)
    {
        parent::setKeysForSaveQuery($query);
        $query->where('partner_id', $this->partner_id);

        return $query;
    }
}
