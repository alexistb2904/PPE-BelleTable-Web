<?php

use App\Controllers\GroupController;

$groupController = new GroupController();

$app->group('/group', function ($group) use ($groupController) {
    $group->get('', [$groupController, 'getAll']);
    $group->post('', [$groupController, 'create']);
    $group->put('/{id:[0-9]+}', [$groupController, 'edit']);
    $group->delete('/{id:[0-9]+}', [$groupController, 'delete']);
    $group->post('/{id:[0-9]+}/users', [$groupController, 'addUser']);
    $group->delete('/{id:[0-9]+}/users/{userId}', [$groupController, 'removeUser']);
    $group->get('/{id:[0-9]+}/participants', [$groupController, 'getParticipants']);
});
