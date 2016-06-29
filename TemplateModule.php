<?php

namespace bariew\templateModule;

use bariew\abstractModule\AbstractModule;

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
