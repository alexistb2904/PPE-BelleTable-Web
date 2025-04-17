<?php

namespace App\Controllers;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../functions.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GroupController
{
    public function getAll(Request $request, Response $response): Response
    {
        $result = \getAllGroups();

        $response->getBody()->write($result);

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function create(Request $request, Response $response): Response
    {
        $param = $request->getParsedBody();
        $required = ['name'];

        if (\checkParam($required, $param)) {
            $result = \createGroup($param['name']);

            $response->getBody()->write($result);
        } else {
            $response->getBody()->write(json_encode(['error' => 'Paramètres manquants']));
            return $response->withStatus(400);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function edit(Request $request, Response $response, array $args): Response
    {
        $idGroup = $args['id'] ?? null;
        $param = $request->getParsedBody();
        $required = ['nom'];

        if ($idGroup !== null && \checkParam($required, $param)) {
            $result = \editGroup($idGroup, $param['nom']);
            $response->getBody()->write($result);
        } else {
            $response->getBody()->write(json_encode(['error' => 'Paramètres manquants']));
            return $response->withStatus(400);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }


    public function delete(Request $request, Response $response, array $args): Response
    {
        $idGroup = $args['id'] ?? null;

        if ($idGroup !== null) {
            $result = \deleteGroup($idGroup);
            $response->getBody()->write($result);
        } else {
            $response->getBody()->write(json_encode(['error' => 'Paramètre id manquant']));
            return $response->withStatus(400);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function addUser(Request $request, Response $response, array $args): Response
    {

        $idGroup = $args['id'] ?? null;
        $param = $request->getParsedBody();
        $required = ['idUser'];

        if ($idGroup !== null && \checkParam($required, $param)) {
            $result = \addUserToGroup($param['idUser'], $idGroup);
            $response->getBody()->write($result);
        } else {
            $response->getBody()->write(json_encode(['error' => 'Paramètres manquants']));
            return $response->withStatus(400);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function removeUser(Request $request, Response $response, array $args): Response
    {
        $idGroup = $args['id'] ?? null;
        $userId = $args['userId'] ?? null;

        if ($idGroup !== null && $userId !== null) {
            $result = \removeUserFromGroup($userId, $idGroup);
            $response->getBody()->write($result);
        } else {
            $response->getBody()->write(json_encode(['error' => 'Paramètres manquants']));
            return $response->withStatus(400);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getParticipants(Request $request, Response $response, array $args): Response
    {
        $idGroup = $args['id'] ?? null;

        if ($idGroup !== null) {
            $result = \getParticipantsOfGroup($idGroup);
            $response->getBody()->write($result);
        } else {
            $response->getBody()->write(json_encode(['error' => 'Paramètres manquants']));
            return $response->withStatus(400);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }
}
