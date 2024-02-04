<?php


return [


    /*
   |--------------------------------------------------------------------------
   | Доменное имя для переадресаций
   |--------------------------------------------------------------------------
   */
    'domain' => 'https://banlist.dygers.fun/',

    /*
   |--------------------------------------------------------------------------
   | Все порты сервера
   |--------------------------------------------------------------------------
   |
   | Порты сервера для динамичной работы фильтров и авторизаций на стороне Laravel
   |
   */
    'ports' => [
        19133 => [
            'prefix' => 'Сервер #1',
            'rcon' => '',
            'ip' => 'dygers.fun'
        ],
        19134 => [
            'prefix' => 'Сервер #2',
            'rcon' => '',
            'ip' => 'dygers.fun'
        ],
        10001 => [
            'prefix' => 'Сервер TEST',
            'rcon' => '',
            'ip' => 'dygers.fun'
        ],
    ],

    /*
   |--------------------------------------------------------------------------
   | Облако сервера
   |--------------------------------------------------------------------------
   | upload - POST загрузки файлов
   | remove - POST удаление файлов
   */
    'upload' => 'http://cloud.dygers.fun/upload.php',
    'remove' => 'http://cloud.dygers.fun/remove.php',

    /*
   |--------------------------------------------------------------------------
   | Типы блокировок => Название таблицы
   |--------------------------------------------------------------------------
   */
    'typeLocks' => [
        'lock_nick' => 'table_bans',
        'lock_os' => 'table_bans_os',
        'kick' => 'table_kicks',
        'lock_chat' => 'table_mutes'
    ],

    /*
   |--------------------------------------------------------------------------
   | Ключ который дает право делать POST запросы к '.../remove_post' на стороне Laravel
   |--------------------------------------------------------------------------
   */
    'key-remove-post' => '',


    /*
   |--------------------------------------------------------------------------
   | Данные для Телеграм Логов
   |--------------------------------------------------------------------------
   | token - TOKEN бота который отправляет сообщения
   | chatId - ID чата в который будут отправляться логи
   */
    'telegram-log' => [
        'token' => '',
        'chatId' => 0,
    ],
];
