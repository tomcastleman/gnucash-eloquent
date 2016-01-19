<?php

namespace GnuCash\Models\Replication;

use GnuCash\Models\Replication;

class Transaction extends Replication
{
    protected $primaryKey = 'repl_id';
    public $incrementing = true;

    protected $fillable = [
        'partnership_name',
    ];

    public function split0()
    {
        return $this->hasOne(TransactionSplit::class, 'repl_id', 'repl_id')
            ->where('transaction_splits.partner_id', 0);
    }

    public function split1()
    {
        return $this->hasOne(TransactionSplit::class, 'repl_id', 'repl_id')
            ->where('transaction_splits.partner_id', 1);
    }
}
