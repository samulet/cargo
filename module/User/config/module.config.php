<?php
return array(
    'view_manager' => array(
        'template_path_stack' => array(
            'user' => __DIR__ . '/../view',
        ),
    ),
    'service_manager' => array(
        'invokables'  => array(
            'BjyAuthorize\View\RedirectionStrategy' => 'User\View\RedirectionStrategy',
        ),
    ),
);