<?php

namespace App\Services;

class GreetingService
{
    /**
     * Create a new class instance.
     */

    public function __construct(
        private string $name
    ) {
    }

    //  public function id()
    // {
    //     return spl_object_id($this);
    // }

    public function greeting()
    {
        return "Olá, {$this->name}!";
    }
}
