<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Server Availability Check Timeout
    |--------------------------------------------------------------------------
    |
    |
    */

    'server_availability_check_timeout' => env('OUTLINE_SERVER_AVAILABILITY_CHECK_TIMEOUT', 5),

    /*
    |--------------------------------------------------------------------------
    | Setup Script
    |--------------------------------------------------------------------------
    |
    | This script will be displayed to users to setting up a new server for Outline VPN.
    |
    */

    'setup_script' => 'sudo bash -c "$(wget -qO- https://raw.githubusercontent.com/Jigsaw-Code/outline-server/master/src/server_manager/install_scripts/install_server.sh)"',

    /*
    |--------------------------------------------------------------------------
    | Setup Script Output Example
    |--------------------------------------------------------------------------
    |
    | This script will be displayed to users as an example for setup script output.
    |
    */

    'setup_script_output_example' => '{"apiUrl":"https://xxx.xxx.xxx.xxx:xxxxx/xxxxxxxxxxxxxxxxxxxxxx","certSha256":"xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"}',
];
