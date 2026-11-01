<?php

namespace App\Middleware;

use App\Helpers\Session;
use App\Helpers\Response;

class GuestMiddleware
{
    public function handle(): void
    {
        Session::start();
        if (Session::has('user')) {
            Response::redirect('/');
        }
    }
}
