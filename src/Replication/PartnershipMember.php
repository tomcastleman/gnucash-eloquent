<?php namespace Gnucash\Models\Replication;

use Gnucash\Models\Replication;
use Illuminate\Database\Eloquent\Builder;

class PartnershipMember extends Replication
{

    protected $primaryKey = 'name';

    public function scopeForBookAccount($query, $book, $accountGuid)
    {
        $query->with('transactions');

        // partner_id was aliased as p
        //get_partner_account_guid_in_partnerships
        $table = $this->getTable();
        $query->join("{$table} as ppm", function ($join) use ($table) {
            $join->on("{$table}.name", '=', 'ppm.name');
            $join->on("{$table}.partner_id", '!=', 'ppm.partner_id');
        });
        $query->where("{$table}.book", $book);
        $query->where("{$table}.account_guid", $accountGuid);

        return $query;
    }

    public function scopeForBook($query, $book)
    {
        $table = $this->getTable();

        $query->where('book', $book);
        $query->join('partnerships as p', "{$table}.name", '=', 'p.name');
        $query->where('p.amount_invert', 1);

        return $query;
    }

    protected function setKeysForSaveQuery(Builder $query)
    {
        parent::setKeysForSaveQuery($query);
        $query->where('partner_id', $this->partner_id);

        return $query;
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'partnership_name', 'name');
    }
}