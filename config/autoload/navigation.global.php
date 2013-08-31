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
                        'route' => 'account',
                        'group' => 'right',
                        'params' => array('id' => null),
                        'resource'   => 'route/account',
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
                'label' => 'Аккаунт',
                'route' => 'account',
                'action' => 'index',
                'params' => array('id' => null),
                'resource'   => 'route/account',
            ),
            array(
                'label' => 'Создать аккаунт',
                'route' => 'account',
                'action' => 'addAccount',
                'params' => array('id' => null),
                'resource'   => 'route/account',
            ),
            array(
                'label' => 'Выбрать аккаунт и компанию',
                'route' => 'account',
                'action' => 'choiceOrgAndCompany',
                'params' => array('id' => null),
                'resource'   => 'route/account',
            ),
            array(
                'label' => 'Управление айтемами',
                'route' => 'notification',
                'action' => 'index',
                'params' => array('id' => null),
                'resource'   => 'route/notification',
            ),
            array(
                'label' => 'Доска оповещений',
                'route' => 'notification',
                'action' => 'my',
                'params' => array('id' => null),
                'resource'   => 'route/notification',
            ),
            array(
                'label' => 'Новые оповещения',
                'route' => 'notification',
                'action' => 'new',
                'params' => array('id' => null),
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
                'params' => array('id' => null),
                'resource'   => 'controller/Resource\Controller\Resource:index',
            ),
            array(
                'label' => 'Собственные',
                'route' => 'resource',
                'action' => 'my',
                'params' => array('id' => null),
                'resource'   => 'controller/Resource\Controller\Resource:my',
            ),
            array(
                'label' => 'Поиск',
                'route' => 'resource',
                'action' => 'add',
                'params' => array('id' => null,'type'=>'search'),
                'resource'   => 'controller/Resource\Controller\Resource:search',
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
                'params' => array('id' => null),
                'resource'   => 'controller/Resource\Controller\Vehicle:index',
            ),
            array(
                'label' => 'Собственные',
                'route' => 'vehicle',
                'action' => 'my',
                'params' => array('id' => null),
                'resource'   => 'controller/Resource\Controller\Vehicle:my',
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
                'params' => array('id' => null),
                'resource'   => 'controller/Ticket\Controller\Ticket:index',
            ),
            array(
                'label' => 'Собственные',
                'route' => 'ticket',
                'action' => 'my',
                'params' => array('id' => null),
                'resource'   => 'controller/Ticket\Controller\Ticket:my',
            ),
            array(
                'label' => 'Поиск',
                'route' => 'ticket',
                'action' => 'add',
                'params' => array('id' => null,'type'=>'search'),
                'resource'   => 'controller/Ticket\Controller\Ticket:search',
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
                'params' => array('id' => null),
                'resource'   => 'controller/Ticket\Controller\Cargo:index',
            ),
            array(
                'label' => 'Собственные',
                'route' => 'cargo',
                'action' => 'my',
                'params' => array('id' => null),
                'resource'   => 'controller/Ticket\Controller\Cargo:my',
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
                'params' => array('id' => null),
                'resource'   => 'route/interaction',
            ),
            array(
                'label' => 'Полученные',
                'route' => 'interaction',
                'action' => 'my',
                'params' => array('id' => null),
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
                'params' => array('id' => null),
                'resource'   => 'route/auction',
            ),
        ),
        'admin' => array(
            array(
                'label' => 'Управление юзерами',
                'type' => 'uri',
                'uri' => '',
                'class' => 'nav-header',
                'resource'   => 'route/company_user',
            ),
            array(
                'label' => 'Все юзеры',
                'route' => 'company_user',
                'action' => 'list',
                'params' => array('org_id' => 'all', 'param' =>'full'),

            ),
            array(
                'label' => 'Справочники',
                'type' => 'uri',
                'uri' => '',
                'class' => 'nav-header',
                'resource'   => 'route/addList',
            ),
            array(
                'label' => 'Мои справочники',
                'route' => 'addList',
                'action' => 'my',
                'params' => array('id' => null),
                'resource'   => 'route/addList',
            ),
            array(
                'label' => 'ТС Марка',
                'route' => 'addList',
                'action' => 'my-fields',
                'params' => array('id' => 'veh-marks'),
                'resource'   => 'route/addList',
            ),
            array(
                'label' => 'ТС Модель',
                'route' => 'addList',
                'action' => 'my-fields',
                'params' => array('id' => 'veh-models'),
                'resource'   => 'route/addList',
            ),
            array(
                'label' => 'ТС Статус',
                'route' => 'addList',
                'action' => 'my-fields',
                'params' => array('id' => 'veh-status'),
                'resource'   => 'route/addList',
            ),
            array(
                'label' => 'ТС Тип',
                'route' => 'addList',
                'action' => 'my-fields',
                'params' => array('id' => 'veh-type'),
                'resource'   => 'route/addList',
            ),
            array(
                'label' => 'Компания Формы собственности',
                'route' => 'addList',
                'action' => 'my-fields',
                'params' => array('id' => 'ownerships'),
                'resource'   => 'route/addList',
            ),
            array(
                'label' => 'Компания Реквизиты',
                'route' => 'addList',
                'action' => 'my-fields',
                'params' => array('id' => 'requisites'),
                'resource'   => 'route/addList',
            ),
            array(
                'label' => 'Заявка Груз',
                'route' => 'addList',
                'action' => 'my-fields',
                'params' => array('id' => 'prod-group'),
                'resource'   => 'route/addList',
            ),
            array(
                'label' => 'Заявка Вид документа',
                'route' => 'addList',
                'action' => 'my-fields',
                'params' => array('id' => 'doc-type'),
                'resource'   => 'route/addList',
            ),
            array(
                'label' => 'Заявка Вид загрузки',
                'route' => 'addList',
                'action' => 'my-fields',
                'params' => array('id' => 'load-type'),
                'resource'   => 'route/addList',
            ),
            array(
                'label' => 'Заявка температурный режим',
                'route' => 'addList',
                'action' => 'my-fields',
                'params' => array('id' => 'temp-cond'),
                'resource'   => 'route/addList',
            ),
            array(
                'label' => 'Статус предложения',
                'route' => 'addList',
                'action' => 'my-fields',
                'params' => array('id' => 'offer-status'),
                'resource'   => 'route/addList',
            ),
        ),
    ),
);
