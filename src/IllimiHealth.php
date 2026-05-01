<?php

namespace Illimi\Health;

use Illimi\Health\Managers\HealthModuleManager;

class IllimiHealth
{
    public function ping(): string
    {
        return 'illimi-health installed';
    }

    public function moduleManager(): HealthModuleManager
    {
        return new HealthModuleManager();
    }

    public function menu(): array
    {
        return $this->moduleManager()->sideMenu();
    }
}
