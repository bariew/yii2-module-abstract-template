<?php

namespace bariew\templateAbstractModule;

use bariew\abstractModule\AbstractModule;

class templateAbstractModule extends AbstractModule
{
    public $params = [
        'menu'  => [
            'label' => 'Settings',
            'items' => [[
                'label'    => 'Notifications',
                'url' => ['/template/config/index']
            ]]
        ]
    ];
}
