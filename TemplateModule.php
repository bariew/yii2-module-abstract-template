<?php

namespace bariew\templateAbstractModule;

use bariew\abstractModule\AbstractModule;

class TemplateModule extends AbstractModule
{
    public $params = [
        'menu'  => [
            'label' => 'Settings',
            'items' => [[
                'label'    => 'Templates',
                'url' => ['/template/config/index']
            ]]
        ]
    ];
}
