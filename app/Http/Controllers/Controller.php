<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    /**
     * @param Request|null $request
     * @return int
     */
    public function getPageSize(Request $request = null): int
    {
        $request = $request ?: \Illuminate\Support\Facades\Request::instance();

        return (int) $request->input('size', 10);
    }
}
