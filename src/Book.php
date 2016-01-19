<?php

namespace Gnucash\Models;

use Gnucash\Models\Book\Account;
use Gnucash\Models\Book\SplitPivot;
use Gnucash\Models\Book\Transaction;
use Illuminate\Database\Eloquent\Model;

abstract class Book extends Model
{
    const CONNECTION_PREFIX = 'gnucash_book_';

    public $incrementing = false;
    public $timestamps = false;

    protected $namespace;

    public function __construct(array $attributes = [])
    {
        $this->namespace = $this->bookNamespace();

        $this->connection = $this->bookConnection();

        parent::__construct($attributes);
    }

    public function newPivot(Model $parent, array $attributes, $table, $exists)
    {
        if ($this instanceof Transaction && $parent instanceof Account) {
            return new SplitPivot($parent, $attributes, $table, $exists);
        }

        if ($this instanceof Account && $parent instanceof Transaction) {
            return new SplitPivot($parent, $attributes, $table, $exists);
        }

        return parent::newPivot($parent, $attributes, $table, $exists);
    }

    protected function namespaceForBook($abstractClassPath)
    {
        return $this->namespace.'\\'.class_basename($abstractClassPath);
    }

    private function bookConnection()
    {
        return self::CONNECTION_PREFIX.strtolower(class_basename($this->namespace));
    }

    private function bookNamespace()
    {
        return str_replace('\\'.class_basename(static::class), '', static::class);
    }

    public static function factory()
    {
        return new static();
    }
}
