<?php

namespace App\Controllers;

use App\Models\Role;
use Core\Http\Response;
use Throwable;

class HomeController
{
    /**
     * Show the home page.
     *
     * @return Response
     * @throws Throwable
     */
    public function show(): Response
    {
        return view('home');
    }
}