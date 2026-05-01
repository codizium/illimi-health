<?php

namespace Illimi\Health\Facades;

use Illuminate\Support\Facades\Facade;

class IllimiHealth extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'illimi-health';
    }
}
