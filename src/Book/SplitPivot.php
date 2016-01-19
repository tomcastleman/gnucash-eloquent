<?php namespace Gnucash\Models\Book;

use Illuminate\Database\Eloquent\Relations\Pivot;

class SplitPivot extends Pivot implements SplitInterface
{

    use SplitTrait;

}