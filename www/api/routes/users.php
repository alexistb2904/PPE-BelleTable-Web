<?php

use App\Controllers\UsersController;
use App\Controllers\AuthController;

$usersController = new UsersController();
$authController = new AuthController();

$app->group('/users', function ($group) use ($usersController) {
    $group->get('', [$usersController, 'getAll']);
    $group->get('/me', [$usersController, 'get']);
    $group->get('/{id:[0-9]+}', [$usersController, 'get']);
    $group->put('/{id:[0-9]+}', [$usersController, 'edit']);
    $group->delete('/{id:[0-9]+}', [$usersController, 'delete']);
    $group->put('/{id:[0-9]+}/role', [$usersController, 'changeRole']);
    $group->get('/{id:[0-9]+}/scores', [$usersController, 'getAllScoresForUser']);
});

$app->group('/auth', function ($group) use ($authController) {
    $group->post('/register', [$authController, 'register']);
    $group->post('/login', [$authController, 'login']);
    $group->post('/logout', [$authController, 'logout']);
    $group->post('/reset-password', [$authController, 'resetPassword']);
    $group->post('/change-password', [$authController, 'changePassword']);
});
