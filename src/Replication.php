<?php

namespace GnuCash\Models;

use Illuminate\Database\Eloquent\Model;

abstract class Replication extends Model
{
    protected $connection = 'gnucash_tools';

    public $incrementing = false;
    public $timestamps = false;
}
