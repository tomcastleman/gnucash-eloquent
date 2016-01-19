<?php

namespace GnuCash\Models\Book;

use GnuCash\Models\Book;

abstract class Commodity extends Book
{
    protected $table = 'commodities';
    protected $primaryKey = 'guid';

    public function scopeByCode($query, $code)
    {
        return $query->where('mnemonic', $code);
    }
}
