<?php

use App\Controllers\QuestionnaireController;

$questionnaireController = new QuestionnaireController();

$app->group('/questionnaires', function ($group) use ($questionnaireController) {
    $group->get('', [$questionnaireController, 'getAll']);
    $group->get('/types', [$questionnaireController, 'getAllTypes']);
    $group->get('/{id:[0-9]+}', [$questionnaireController, 'get']);
    $group->get('/{id:[0-9]+}/score/me', [$questionnaireController, 'getScore']);
    $group->post('/{id:[0-9]+}/score', [$questionnaireController, 'registerScore']);
    $group->get('/{id:[0-9]+}/score', [$questionnaireController, 'getAllScores']);
    $group->get('/scores', [$questionnaireController, 'getAllScoreFromAll']);
});
