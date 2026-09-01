<?php
declare(strict_types=1);

return [
    'app' => [
        'title' => 'AdminServ',
        'subtitle' => 'TrackMania & ManiaPlanet Server Manager',
        'default_theme' => 'dark',
        'cookie_expire_days' => 90,
        'session_lifetime' => 86400,
        'enable_cors' => false,
    ],

    'master_config' => [
        'enabled' => true,
        // Default master password hash for: adminserv
        // If you forgot your custom master password, you can reset it by copying this hash.
        'password' => '$2y$12$R/eyMnxKZL4Igqk.dqiFQeKTCu5JhPutOCALcEY1eETgg9AMvidtC',
    ],

    // Dedicated servers configuration
    // You can add dedicated servers below or configure them dynamically in the AdminServ web interface.
    'servers' => [
        /*
        'Example Server' => [
            'address' => '127.0.0.1',
            'port' => 5000,
            'timeout' => 3,
            'maps_base_path' => '/servers/ExampleServer/Maps/',
            'matchsettings_path' => 'MatchSettings/',
            'admin_passwords' => [
                'SuperAdmin' => 'SuperAdmin',
                'Admin' => 'Admin',
                'User' => 'User',
            ],
        ],
        */
    ],

    'admin_levels' => [
        'SuperAdmin' => [
            'name' => 'SuperAdmin',
            'description' => 'Full administrative access',
            'permissions' => [
                'speed_admin',
                'switch_server',
                'player_kick',
                'player_ban',
                'player_guest',
                'player_ignore',
                'player_forcetospectator',
                'player_forcetoplayer',
                'player_changeteam',
                'force_scores',
                'cancel_vote',
                'srvopts_general_name',
                'srvopts_general_comment',
                'srvopts_general_passwords',
                'srvopts_general_limits',
                'srvopts_advanced',
                'gameinfos_gamemode',
                'gameinfos_options',
                'gameinfos_warmup',
                'gameinfos_teams',
                'chat_sendmessage',
                'maps_manage',
                'maps_upload',
                'maps_matchsettings',
                'manage_servers',
                'manage_adminlevels',
            ],
        ],
        'Admin' => [
            'name' => 'Admin',
            'description' => 'Standard server moderator',
            'permissions' => [
                'speed_admin',
                'switch_server',
                'player_kick',
                'player_ban',
                'player_guest',
                'player_ignore',
                'player_forcetospectator',
                'player_forcetoplayer',
                'player_changeteam',
                'cancel_vote',
                'srvopts_general_name',
                'srvopts_general_comment',
                'gameinfos_gamemode',
                'gameinfos_warmup',
                'chat_sendmessage',
                'maps_manage',
                'maps_upload',
                'maps_matchsettings',
            ],
        ],
        'User' => [
            'name' => 'User',
            'description' => 'Read-only viewer / player access',
            'permissions' => [
                'chat_sendmessage',
            ],
        ],
    ],

    'maps' => [
        'allowed_extensions' => ['gbx', 'map.gbx', 'challenge.gbx'],
        'hidden_folders' => ['Campaigns', 'Replays'],
        'max_upload_size_mb' => 20,
    ],

    'logs' => [
        'enabled' => true,
        'path' => __DIR__ . '/../logs/adminserv.log',
    ],
];
