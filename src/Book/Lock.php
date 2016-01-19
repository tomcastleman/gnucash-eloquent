<?php namespace Gnucash\Models\Book;

use Gnucash\Models\Book;

abstract class Lock extends Book
{

    protected $table = 'gnclock';
    protected $primaryKey = 'PID';

    protected $fillable = ['PID', 'Hostname'];

}