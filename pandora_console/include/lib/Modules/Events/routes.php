<?php

use PandoraFMS\Modules\Events\Comments\Controllers\CreateEventCommentController;
use PandoraFMS\Modules\Events\Comments\Controllers\DeleteEventCommentController;
use PandoraFMS\Modules\Events\Comments\Controllers\GetEventCommentController;
use PandoraFMS\Modules\Events\Comments\Controllers\ListEventCommentController;
use PandoraFMS\Modules\Events\Comments\Controllers\UpdateEventCommentController;
use PandoraFMS\Modules\Events\Controllers\CreateEventController;
use PandoraFMS\Modules\Events\Controllers\DeleteEventController;
use PandoraFMS\Modules\Events\Controllers\GetEventController;
use PandoraFMS\Modules\Events\Controllers\ListEventController;
use PandoraFMS\Modules\Events\Controllers\UpdateEventController;
use Slim\App;

return function (App $app) {
    $app->map(['GET', 'POST'], '/event/list', ListEventController::class);
    $app->get('/event/{idEvent}', GetEventController::class);
    $app->post('/event', CreateEventController::class);
    $app->put('/event/{idEvent}', UpdateEventController::class);
    $app->delete('/event/{idEvent}', DeleteEventController::class);
    $app->map(['GET', 'POST'], '/event/{idEvent}/comment/list', ListEventCommentController::class);
    $app->get('/event/{idEvent}/comment/{idComment}', GetEventCommentController::class);
    $app->post('/event/{idEvent}/comment', CreateEventCommentController::class);
    $app->put('/event/{idEvent}/comment/{idComment}', UpdateEventCommentController::class);
    $app->delete('/event/{idEvent}/comment/{idComment}', DeleteEventCommentController::class);
};
