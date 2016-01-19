<?php namespace Gnucash\Models\Replication;

use Gnucash\Models\Replication;

class Partnership extends Replication
{

    protected $primaryKey = 'name';

    public function members()
    {
        return $this->hasMany(PartnershipMember::class, 'name');
    }
}