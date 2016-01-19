<?php

namespace GnuCash\Models\Book;

use GnuCash\Models\Book;

abstract class Lock extends Book
{
    protected $table = 'gnclock';
    protected $primaryKey = 'PID';

    protected $fillable = ['PID', 'Hostname'];
}
