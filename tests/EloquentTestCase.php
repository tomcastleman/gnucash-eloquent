<?php

namespace GnuCash\Models\Tests;

use PHPUnit_Framework_TestCase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Eloquent\Model as Eloquent;

abstract class EloquentTestCase extends PHPUnit_Framework_TestCase
{

    protected $connection = 'gnucash_book_test';

    public function setUp()
    {
        $this->configureDatabase();
        $this->migrateTable();
    }

    protected function configureDatabase()
    {
        $db = new DB;
        $db->addConnection([
            'driver'    => 'sqlite',
            'database'  => ':memory:',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ], $this->connection);
        $db->bootEloquent();
        $db->setAsGlobal();
    }

    public function migrateTable()
    {
        $columns = $this->columns();
        DB::schema($this->connection)->create($this->table(), function (Blueprint $table) use ($columns) {
            foreach ($columns as $name => $type) {
                $table->$type($name);
                if (in_array($name, ['id', 'guid'])) {
                    $table->primary($name);
                }
            }
        });
        Eloquent::unguard();
    }

    protected abstract function table();

    protected abstract function columns();

}