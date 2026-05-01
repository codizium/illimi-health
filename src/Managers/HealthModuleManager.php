<?php

namespace Illimi\Health\Managers;

class HealthModuleManager
{
    public function sideMenu(): array
    {
        return [
            [
                'label' => 'Health',
                'icon' => 'ri-heart-pulse-line',
                'route' => 'javascript:void(0)',
                'roles' => ['admin'],
                'children' => [
                    ['label' => 'Medical Profiles', 'route' => 'health.profiles'],
                    ['label' => 'Visits Log', 'route' => 'health.visits'],
                    ['label' => 'Incidents', 'route' => 'health.incidents'],
                    ['label' => 'Immunizations', 'route' => 'health.immunizations'],
                ],
            ],
        ];
    }
}
