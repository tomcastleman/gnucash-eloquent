<?php

namespace GnuCash\Models\Replication;

use GnuCash\Models\Replication;

class Partnership extends Replication
{
    protected $primaryKey = 'name';

    public function members()
    {
        return $this->hasMany(PartnershipMember::class, 'name');
    }
}
