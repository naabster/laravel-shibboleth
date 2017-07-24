<?php

return array(

    /*
    |--------------------------------------------------------------------------
    | Views / Endpoints
    |--------------------------------------------------------------------------
    |
    | Set your login page, or login routes, here. If you provide a view,
    | that will be rendered. Otherwise, it will redirect to a route.
    |
     */

    'idp_login'     => '/Shibboleth.sso/Login',
    'idp_logout'    => '/Shibboleth.sso/Logout',
    'authenticated' => '/home',

    /*
    |--------------------------------------------------------------------------
    | Emulate an IdP
    |--------------------------------------------------------------------------
    |
    | In case you do not have access to your Shibboleth environment on
    | homestead or your own Vagrant box, you can emulate a Shibboleth
    | environment with the help of Shibalike.
    |
    | Do not use this in production for literally any reason.
    |
     */

    'emulate_idp'       => false,
    'emulate_idp_users' => array(
        'admin' => array(
            'uid'         => 'admin',
            'displayName' => 'Admin User',
            'givenName'   => 'Admin',
            'sn'          => 'User',
            'mail'        => 'admin@uwm.edu',
        ),
        'staff' => array(
            'uid'         => 'staff',
            'displayName' => 'Staff User',
            'givenName'   => 'Staff',
            'sn'          => 'User',
            'mail'        => 'staff@uwm.edu',
        ),
        'user'  => array(
            'uid'         => 'user',
            'displayName' => 'User User',
            'givenName'   => 'User',
            'sn'          => 'User',
            'mail'        => 'user@uwm.edu',
        ),
    ),

    /*
    |--------------------------------------------------------------------------
    | Server Variable Mapping
    |--------------------------------------------------------------------------
    |
    | Change these to the proper values for your IdP.
    |
     */

    'entitlement' => 'entitlement',

    'user' => [
        // fillable user model attribute => server variable
        'email'       => 'mail',
        'name'        => 'displayName',
        'first_name'  => 'givenName',
        'last_name'   => 'sn',
        'student_id'  => 'employeeNumber',
    ],

    /*
    |--------------------------------------------------------------------------
    | Auth Settings
    |--------------------------------------------------------------------------
    |
    | Allows you to change the auth method
    |
     */

    'use_simple_session' => false, // Just add Shibboleth Response to Laravel Session instead of Laravel Auth with User Model

    /*
    |--------------------------------------------------------------------------
    | User Creation and Groups Settings
    |--------------------------------------------------------------------------
    |
    | Allows you to change if / how new users are added
    |
     */

    'add_new_users' => true, // Should new users be added automatically if they do not exist?

    /*
    |--------------------------------------------------------------------------
    | JWT Auth
    |--------------------------------------------------------------------------
    |
    | JWTs are for the front end to know it's logged in
    |
    | https://github.com/tymondesigns/jwt-auth
    | https://github.com/StudentAffairsUWM/Laravel-Shibboleth-Service-Provider/issues/24
    |
     */

    'jwtauth' => env('JWTAUTH', false),
);
