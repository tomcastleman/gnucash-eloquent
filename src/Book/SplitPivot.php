<?php

namespace GnuCash\Models\Book;

use Illuminate\Database\Eloquent\Relations\Pivot;

class SplitPivot extends Pivot implements SplitInterface
{
    use SplitTrait;

    protected $appends = [
        'amount'
    ];
}
