<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    public function per_page()
    {
        $request = request();
        $per_page = 10;

        if (isset($request->per_page))
            $per_page = $request->per_page;

        if (isset($request->results))
            $per_page = $request->results;

        return $per_page;
    }
}
