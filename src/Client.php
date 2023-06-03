<?php

namespace Openpix\PhpSdk;

use Openpix\PhpSdk\Resources\Costumers;

class Client
{
    public function costumers()
    {
        return new Costumers();
    }
}
