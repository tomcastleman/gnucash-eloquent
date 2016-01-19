<?php namespace Gnucash\Models\Book;

use Gnucash\Models\Book;

abstract class Commodity extends Book
{

    protected $table = 'commodities';
    protected $primaryKey = 'guid';

    public function scopeByCode($query, $code)
    {
        return $query->where('mnemonic', $code);
    }
}