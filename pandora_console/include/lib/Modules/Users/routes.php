<?php

use PandoraFMS\Modules\Users\Controllers\CreateUserController;
use PandoraFMS\Modules\Users\Controllers\DeleteUserController;
use PandoraFMS\Modules\Users\Controllers\GetUserController;
use PandoraFMS\Modules\Users\Controllers\ListUserController;
use PandoraFMS\Modules\Users\Controllers\UpdateUserController;
use PandoraFMS\Modules\Users\UserProfiles\Controllers\CreateUserProfileController;
use PandoraFMS\Modules\Users\UserProfiles\Controllers\DeleteUserProfileController;
use PandoraFMS\Modules\Users\UserProfiles\Controllers\GetUserProfileController;
use PandoraFMS\Modules\Users\UserProfiles\Controllers\ListUserProfileController;
use Slim\App;

return function (App $app) {
    $app->map(['GET', 'POST'], '/user/list', ListUserController::class);
    $app->get('/user/{id}', GetUserController::class);
    $app->post('/user', CreateUserController::class);
    $app->put('/user/{id}', UpdateUserController::class);
    $app->delete('/user/{id}', DeleteUserController::class);

    $app->map(['GET', 'POST'], '/user/{id}/profiles', ListUserProfileController::class);
    $app->get('/user/{id}/profile/{idProfile}', GetUserProfileController::class);
    $app->post('/user/{id}/profile/{idProfile}', CreateUserProfileController::class);
    $app->delete('/user/{id}/profile/{idProfile}', DeleteUserProfileController::class);
};
