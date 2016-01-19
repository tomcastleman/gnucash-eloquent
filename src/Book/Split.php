<?php namespace Gnucash\Models\Book;

use Gnucash\Models\Book;
use Illuminate\Database\Capsule\Manager as Capsule;

abstract class Split extends Book implements SplitInterface
{

    use SplitTrait;

    protected $table = 'splits';
    protected $primaryKey = 'guid';

    protected $fillable = [
        'reconcile_state',
        'reconcile_date',
    ];

    public function account()
    {
        return $this->belongsTo($this->namespaceForBook(Account::class), 'account_guid');
    }

    public function transaction()
    {
        return $this->belongsTo($this->namespaceForBook(Transaction::class), 'tx_guid');
    }

    public function scopeForAccount($query, $accountGuid, $reconcileStates = null)
    {
        $query->where('account_guid', $accountGuid);

        if (is_array($reconcileStates)) {
            $query->whereIn('reconcile_state', $reconcileStates);
        }

        return $query;
    }

    public function scopeForTransaction($query, $txGuid)
    {
        $query->where('tx_guid', $txGuid);

        return $query;
    }

    public function scopeDuplicateMemos($query, $accountGuid)
    {
        static::scopeForAccount($query, $accountGuid);

        $query->with('transaction');

        $query->whereIn('memo', function ($query) {
            $query->select('memo')
                ->from('splits')
                ->where('memo', 'like', GNUCASH_XERO_MEMO_PREFIX . '%')
                ->groupBy('memo')
                ->havingRaw('COUNT(guid) > 1');
        });

        return $query;
    }

    public static function scopeOrphans($query, $relation)
    {
        $query->whereNotIn(
            $relation->splits()->getForeignKey(),
            $relation->all()->pluck($relation->getKeyName())
        );
    }

    public static function getBalanceForAccount($accountGuid, $reconcileStates = null)
    {
        $account = $query = static::forAccount($accountGuid, $reconcileStates);

        return $account->sum($query->getQuery()->raw('(value_num/value_denom)'));
    }

    public static function getAccountTransactions(array $accountGuids, $txGuid)
    {
        $query = static::forTransaction($txGuid);
        $query->whereIn('account_guid', $accountGuids);

        return $query->get();
    }

    public static function reconcileStateLabels($mode)
    {
        $labels = [
            static::REPLICATED     => [
                static::RECONCILE_STATE_NEW        => 'New',
                static::RECONCILE_STATE_CLEARED    => 'Agreed',
                static::RECONCILE_STATE_RECONCILED => 'Final',
            ],
            static::NOT_REPLICATED => [
                static::RECONCILE_STATE_NEW        => 'New',
                static::RECONCILE_STATE_CLEARED    => 'Cleared',
                static::RECONCILE_STATE_RECONCILED => 'Reconciled',
            ],
        ];

        return $labels[$mode];
    }

}