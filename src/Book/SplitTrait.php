<?php

namespace Gnucash\Models\Book;

trait SplitTrait
{
    public function amountFormatted($invert = 1)
    {
        return number_format($this->amount($invert), 2);
    }

    public function amount($invert = 1)
    {
        return round($this->value_num / $this->value_denom * $invert, 2);
    }

    public function getAmountAttribute()
    {
        return round($this->value_num / $this->value_denom, 2);
    }

    public function getXeroReceiptIdAttribute()
    {
        return str_replace(GNUCASH_XERO_MEMO_PREFIX, '', $this->memo);
    }

    public function getIsReconciledAttribute()
    {
        return $this->reconcile_state === Split::RECONCILE_STATE_RECONCILED;
    }

    public function getIsClearedAttribute()
    {
        return $this->reconcile_state === Split::RECONCILE_STATE_CLEARED;
    }

    public function getIsNewAttribute()
    {
        return $this->reconcile_state === Split::RECONCILE_STATE_NEW;
    }

    public function getMemoIgnoreAttribute()
    {
        return trim(strtoupper($this->memo)) === Split::MEMO_REPL_IGNORE;
    }
}
