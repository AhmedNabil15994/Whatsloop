<?php


return [
    'general' => [
        'controller' => 'DashboardControllers',
        'title' => 'الرئيسية',
        'permissions' => [
            'list-dashboard' => ['Dashboard','changeLang','changeTheme','changeThemeToDefault'],
        ],
    ],
    'msgsArchive' => [
        'controller' => 'ApiModsControllers',
        'title' => 'سجل الحالات',
        'permissions' => [
            'list-messages-archive' => ['msgsArchive',],
        ],
    ],
    'groupNumberRepors' => [
        'controller' => 'ApiModsControllers',
        'title' => 'سجل الحالات',
        'permissions' => [
            'list-groupNumberRepors' => ['report',],
        ],
    ],
    'statuses' => [
        'controller' => 'ApiModsControllers',
        'title' => 'سجل الحالات',
        'permissions' => [
            'list-statuses' => ['index',],
        ],
    ],
    'groupMsgs' => [
        'controller' => 'GroupMsgsControllers',
        'title' => 'الرسائل الجماعية',
        'permissions' => [
            'list-group-messages' => ['index',],
            'add-group-message' => ['add','create'],
            'view-group-message' => ['add','view'],
            'charts-group-message' => ['charts'],
            'uploadImage-group-message' => ['uploadImage'],
        ],
    ],
    'contacts' => [
        'controller' => 'ContactsControllers',
        'title' => 'جهات الارسال',
        'permissions' => [
            'list-contacts' => ['index',],
            'edit-contact' => ['edit','update','fastEdit'],
            'add-contact' => ['add','create'],
            'delete-contact' => ['delete'],
            'sort-contact' => ['sort','arrange'],
            'charts-contact' => ['charts'],
            'export-contacts' => ['downloadContacts'],
        ],
    ],
    'groupNumbers' => [
        'controller' => 'GroupNumbersControllers',
        'title' => 'مجموعات الارقام',
        'permissions' => [
            'list-group-numbers' => ['index'],
            'edit-group-number' => ['edit','update','fastEdit'],
            'add-group-number' => ['add','create'],
            'delete-group-number' => ['delete'],
            'sort-group-number' => ['sort','arrange'],
            'charts-group-number' => ['charts'],
            'add-number-to-group' => ['addGroupNumbers','postAddGroupNumbers'],
        ],
    ],
    'categories' => [
        'controller' => 'CategoryControllers',
        'title' => 'التصنيفات',
        'permissions' => [
            'list-categories' => ['index'],
            'edit-category' => ['edit','update','fastEdit'],
            'add-category' => ['add','create'],
            'delete-category' => ['delete'],
            'sort-category' => ['sort','arrange'],
            'charts-category' => ['charts'],
        ],
    ],
    'replies' => [
        'controller' => 'RepliesControllers',
        'title' => 'الردود الجاهزة',
        'permissions' => [
            'list-replies' => ['index'],
            'edit-reply' => ['edit','update','fastEdit'],
            'add-reply' => ['add','create'],
            'delete-reply' => ['delete'],
            'sort-reply' => ['sort','arrange'],
            'charts-reply' => ['charts'],
        ],
    ],
    'templates' => [
        'controller' => 'TemplatesControllers',
        'title' => 'قوالب الرسائل',
        'permissions' => [
            'list-templates' => ['index'],
            'edit-template' => ['edit','update','fastEdit'],
            'add-template' => ['add','create'],
            'delete-template' => ['delete'],
            'sort-template' => ['sort','arrange'],
            'charts-template' => ['charts'],
        ],
    ],
    'bots' => [
        'controller' => 'BotControllers',
        'title' => 'واتس لوب بوت',
        'permissions' => [
            'list-bots' => ['index'],
            'edit-bot' => ['edit','update','fastEdit'],
            'add-bot' => ['add','create'],
            'copy-bot' => ['add','copy'],
            'delete-bot' => ['delete'],
            'sort-bot' => ['sort','arrange'],
            'charts-bot' => ['charts'],
            'uploadImage-bot' => ['uploadImage'],
            'deleteImage-bot' => ['deleteImage'],
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
