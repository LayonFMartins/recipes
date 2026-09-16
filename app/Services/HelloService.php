<?php

namespace App\Services;

class HelloService
{
     
    public function __construct(
        private GreetingService $greetingService
    ) {}

    public function message()
    {
        return $this->greetingService->greeting();
    }
}