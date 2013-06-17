<?php
return array(
    'navigation' => array(
        'top-menu' => array(
            array(
                'label' => 'Главная',
                'route' => 'dashboard',
                'group' => 'left',
                'resource'   => 'route/dashboard',
            ),
            array(
                'label' => 'Админка',
                'route' => 'zfcadmin',
                'group' => 'left',
                'resource'   => 'route/zfcadmin',
            ),
            array(
                'label' => 'Регистрация',
                'route' => 'zfcuser/register',
                'group' => 'right',
                'resource'   => 'route/zfcuser/register',
            ),
            array(
                'label' => '##USERNAME##',
                'route' => 'zfcuser',
                'group' => 'right',
                'resource'   => 'route/zfcuser',
                'pages' => array(
                    array(
                        'label' => 'Профиль',
                        'route' => 'zfcuser',
                        'group' => 'right',
                        'resource'   => 'route/zfcuser',
                    ),
                    array(
                        'label' => 'Аккаунты',
                        'route' => 'organization',
                        'group' => 'right',
                        'resource'   => 'route/organization',
                    ),
                    array(
                        'label' => '',
                        'type' => 'uri',
                        'uri' => '',
                        'class' => 'divider',
                        'resource'   => 'route/zfcuser/logout',
                    ),
                    array(
                        'label' => 'Выйти',
                        'route' => 'zfcuser/logout',
                        'resource'   => 'route/zfcuser/logout',
                    ),
                ),
            ),
            array(
                'label' => 'Войти',
                'route' => 'zfcuser/login',
                'group' => 'right',
                'class' => 'js',
                'data-event' => "click",
                'data-handler' => "App.Login.dialog",
                'data-content' => "#login-form",
                'icon' => 'icon-off',
                'resource'   => 'route/zfcuser/login',
            ),
        ),
        'sidebar' => array(
            array(
                'label' => 'Организаиця',
                'route' => 'organization',
                'action' => 'my',
                'resource'   => 'route/organization',

            ),
            array(
                'label' => 'Доска оповещений',
                'route' => 'notification',
                'action' => 'my',
                'resource'   => 'route/notification',

            ),
            array(
                'label' => 'Новые оповещения',
                'route' => 'notification',
                'action' => 'new',
                'resource'   => 'route/notification',

            ),
            array(
                'label' => 'Рерусы',
                'type' => 'uri',
                'uri' => '',
                'class' => 'nav-header',
                'resource'   => 'route/resource',
            ),
            array(
                'label' => 'Все',
                'route' => 'resource',
                'action' => 'index',
                'resource'   => 'route/resource',

            ),
            array(
                'label' => 'Собственные',
                'route' => 'resource',
                'action' => 'my',
                'resource'   => 'route/resource',
            ),
            array(
                'label' => 'Транспорт',
                'type' => 'uri',
                'uri' => '',
                'class' => 'nav-header',
                'resource'   => 'route/vehicle',
            ),
            array(
                'label' => 'Все',
                'route' => 'vehicle',
                'action' => 'index',
                'resource'   => 'route/vehicle',
            ),
            array(
                'label' => 'Собственные',
                'route' => 'vehicle',
                'action' => 'my',
                'resource'   => 'route/vehicle',
            ),
            array(
                'label' => 'Заявки',
                'type' => 'uri',
                'uri' => '',
                'class' => 'nav-header',
                'resource'   => 'route/ticket',
            ),
            array(
                'label' => 'Все',
                'route' => 'ticket',
                'action' => 'index',
                'resource'   => 'route/ticket',

            ),
            array(
                'label' => 'Собственные',
                'route' => 'ticket',
                'action' => 'my',
                'resource'   => 'route/ticket',
            ),
            array(
                'label' => 'Грузы',
                'type' => 'uri',
                'uri' => '',
                'class' => 'nav-header',
                'resource'   => 'route/cargo',
            ),
            array(
                'label' => 'Все',
                'route' => 'cargo',
                'action' => 'index',
                'resource'   => 'route/cargo',
            ),
            array(
                'label' => 'Собственные',
                'route' => 'cargo',
                'action' => 'my',
                'resource'   => 'route/cargo',
            ),
            array(
                'label' => 'Предложения',
                'type' => 'uri',
                'uri' => '',
                'class' => 'nav-header',
                'resource'   => 'route/interaction',
            ),
            array(
                'label' => 'Отправленные',
                'route' => 'interaction',
                'action' => 'index',
                'resource'   => 'route/interaction',
            ),
            array(
                'label' => 'Полученные',
                'route' => 'interaction',
                'action' => 'my',
                'resource'   => 'route/interaction',
            ),
            array(
                'label' => 'Аукцион',
                'type' => 'uri',
                'uri' => '',
                'class' => 'nav-header',
                'resource'   => 'route/auction',
            ),
            array(
                'label' => 'Аукционы',
                'route' => 'auction',
                'action' => 'index',
                'resource'   => 'route/auction',
            ),
            array(
                'label' => 'Справочники',
                'type' => 'uri',
                'uri' => '',
                'class' => 'nav-header',
                'resource'   => 'route/addList',
            ),
            array(
                'label' => 'Мои списки',
                'route' => 'addList',
                'action' => 'my',
                'resource'   => 'route/addList',
            ),
        ),
    ),
);
