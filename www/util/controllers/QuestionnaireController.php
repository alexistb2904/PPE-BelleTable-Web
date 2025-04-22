<?php

namespace App\Controllers;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../functions.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class QuestionnaireController
{
    public function getAll(Request $request, Response $response): Response
    {
        $result = \getAllQuestionnaires();
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function get(Request $request, Response $response, array $args): Response
    {
        $idQuestionnaire = $args['id'] ?? null;

        if ($idQuestionnaire !== null) {
            $result = \getQuestionnaire($idQuestionnaire);
            $response->getBody()->write($result);
        } else {
            $response->getBody()->write(json_encode(['error' => 'Paramètres manquants']));
            return $response->withStatus(400);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getScore(Request $request, Response $response, array $args): Response
    {
        $idQuestionnaire = $args['id'] ?? null;

        if ($idQuestionnaire !== null) {
            $result = \getScoreForQuestionnaire($idQuestionnaire);
            $response->getBody()->write($result);
        } else {
            $response->getBody()->write(json_encode(['error' => 'Paramètres manquants']));
            return $response->withStatus(400);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function registerScore(Request $request, Response $response, array $args): Response
    {
        $idQuestionnaire = $args['id'] ?? null;
        $param = $request->getParsedBody();
        $required = ['score', 'score_on', 'id_user', 'reponses'];

        if ($idQuestionnaire !== null && \checkParam($required, $param)) {
            $result = \registerScore($idQuestionnaire, $param['score'], $param['score_on'], $param['id_user'], $param['reponses']);
            $response->getBody()->write($result);
        } else {
            $response->getBody()->write(json_encode(['error' => 'Paramètres manquants']));
            return $response->withStatus(400);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getAllScoresForQuestionnaire(Request $request, Response $response, array $args): Response
    {
        $idQuestionnaire = $args['id'] ?? null;
        if ($idQuestionnaire !== null) {
            $result = \getAllScoresForQuestionnaire($idQuestionnaire);
            $response->getBody()->write($result);
        } else {
            $response->getBody()->write(json_encode(['error' => 'Paramètres manquants']));
            return $response->withStatus(400);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getAllScoreFromAll(Request $request, Response $response, array $args): Response
    {
        $result = \getAllScoreForAllQuestionnaire();
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getAllTypes(Request $request, Response $response): Response
    {
        $result = \getAllTypeLibelle();
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function registerFeedback(Request $request, Response $response, array $args): Response
    {
        $idQuestionnaire = $args['id'] ?? null;
        $param = $request->getParsedBody();
        $required = ['rating'];

        if ($idQuestionnaire !== null && \checkParam($required, $param)) {
            $result = \registerFeedback($idQuestionnaire, $param['rating'], $param['comment'] ?? null);
            $response->getBody()->write($result);
        } else {
            $response->getBody()->write(json_encode(['error' => 'Paramètres manquants']));
            return $response->withStatus(400);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }
}
