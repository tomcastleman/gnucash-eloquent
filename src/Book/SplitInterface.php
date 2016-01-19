<?php

namespace Gnucash\Models\Book;

interface SplitInterface
{
    const RECONCILE_STATE_CLEARED = 'c';
    const RECONCILE_STATE_RECONCILED = 'y';
    const RECONCILE_STATE_NEW = 'n';

    const MEMO_REPL_IGNORE = 'REPL_IGNORE';

    const REPLICATED = 1;
    const NOT_REPLICATED = 2;
}
