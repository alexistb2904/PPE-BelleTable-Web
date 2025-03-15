<?php

use App\Controllers\QuestionnaireController;

$questionnaireController = new QuestionnaireController();

$app->group('/questionnaires', function ($group) use ($questionnaireController) {
    $group->get('', [$questionnaireController, 'getAll']); // Tous les questionnaires avec leur nom, theme, nombre de questions, nombre de participants, date de création, par qui il a été créé
    $group->get('/types', [$questionnaireController, 'getAllTypes']); // Tous les types de questionnaires en string
    $group->get('/{id:[0-9]+}', [$questionnaireController, 'get']); // Un questionnaire avec son nom, theme, ses questions (avec les choix possibles), nombre de participants, date de création, par qui il a été créé
    $group->get('/{id:[0-9]+}/score/me', [$questionnaireController, 'getScore']); // Le score d'un utilisateur pour un questionnaire et les réponses qu'il a donné
    $group->post('/{id:[0-9]+}/score', [$questionnaireController, 'registerScore']);  // Enregistrer le score d'un utilisateur pour un questionnaire
    $group->get('/{id:[0-9]+}/score', [$questionnaireController, 'getAllScoresForQuestionnaire']); // Tous les scores d'un questionnaire avec le nom de l'utilisateur et leur groupe et le score qu'il a obtenu pour ce questionnaire et les réponses qu'il a donné
    $group->get('/scores', [$questionnaireController, 'getAllScoreFromAll']); // Tous les scores de tous les questionnaires avec le nom de l'utilisateur et leur groupe et le score qu'il a obtenu pour ce questionnaire
});
