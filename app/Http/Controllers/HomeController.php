<?php

namespace App\Http\Controllers;

use App\Services\HelloService;

class HomeController extends Controller
{
    public function index(HelloService $service)
    {
        return $service->message();
    }
}