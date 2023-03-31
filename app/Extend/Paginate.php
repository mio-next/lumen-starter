<?php

namespace App\Extend;

use Illuminate\Pagination\LengthAwarePaginator;

class Paginate extends LengthAwarePaginator
{
    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'page' => $this->currentPage(),
            'size' => $this->perPage(),
            'data' => $this->items->toArray(),
            'total' => $this->total(),
            'from' => $this->firstItem(),
            'to' => $this->lastItem(),
            'more' => $this->hasMorePages(),
            'last_page' => $this->lastPage(),
        ];
    }
}
