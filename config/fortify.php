<?php

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserProfile;
use App\Actions\Fortify\UpdateUserPassword;
use App\Models\User;
use Laravel\Fortify\Features;

return [

    'default' => ['web'],

    'guard' => env('FORTIFY_GUARD', 'web'),

    'view' => env('FORTIFY_VIEW', true),

    'prefix' => 'auth',

    'domain' => null,

    'middleware' => ['web'],

    'limiters' => [
        'login' => 'login',
        'two-factor' => 'two-factor',
    ],

    'views' => true,

    'features' => [
        Features::registration(),
        Features::resetPasswords(),
        Features::emailVerification(),
        Features::updateProfileInformation(),
        Features::updatePasswords(),
        Features::twoFactorAuthentication([
            'confirm' => true,
            'confirmPassword' => true,
        ]),
    ],

    'home' => '/',

    'username' => 'email',

    'email' => 'email',

    'lowercase_usernames' => true,

];
