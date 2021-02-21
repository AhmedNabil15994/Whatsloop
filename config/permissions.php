<?php


return [
    'general' => [
        'controller' => 'DashboardControllers',
        'title' => 'الرئيسية',
        'permissions' => [
            'list-dashboard' => ['Dashboard'],
        ],
    ],
    'users' => [
        'controller' => 'UsersControllers',
        'title' => 'المستخدمين',
        'permissions' => [
            'list-users' => ['index'],
            'edit-user' => ['edit','update','fastEdit'],
            'add-user' => ['add','create'],
            'delete-user' => ['delete'],
            'sort-user' => ['sort','arrange'],
            'charts-user' => ['charts'],
            'uploadImage-user' => ['uploadImage'],
            'deleteImage-user' => ['deleteImage'],
        ],
    ],
    'groups' => [
        'controller' => 'GroupsControllers',
        'title' => 'مجموعات المستخدمين',
        'permissions' => [
            'list-groups' => ['index'],
            'edit-group' => ['edit','update','fastEdit'],
            'add-group' => ['add','create'],
            'delete-group' => ['delete'],
            'sort-group' => ['sort','arrange'],
            'charts-group' => ['charts'],
        ],
    ],
    'variables' => [
        'controller' => 'VariablesControllers',
        'title' => 'اعدادات عامة',
        'permissions' => [
            'list-variables' => ['index'],
            'edit-variable' => ['update'],
        ],
    ],
    'variables2' => [
        'controller' => 'VariablesControllers',
        'title' => 'اعدادات لوحة التحكم',
        'permissions' => [
            'list-variables2' => ['panel'],
            'edit-variable2' => ['uploadImage','deleteImage'],
        ],
    ],
  
];
