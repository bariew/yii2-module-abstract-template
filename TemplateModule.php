<?php

namespace bariew\templateAbstractAbstractModule;

use bariew\abstractAbstractModule\AbstractModule;

class TemplateModule extends AbstractModule
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
