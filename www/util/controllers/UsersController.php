<?php

namespace App\Controllers;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../functions.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UsersController
{
    public function getAll(Request $request, Response $response): Response
    {
        $result = \getAllUsers();
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function get(Request $request, Response $response, array $args): Response
    {
        $idUser = $args['id'] ?? null;

        if ($idUser !== null) {
            $result = \getUserById($idUser);
            $response->getBody()->write($result);
        } else {
            $result = \getSelfUser();
            $response->getBody()->write($result);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function edit(Request $request, Response $response, array $args): Response
    {
        $idUser = $args['id'] ?? null;
        $param = $request->getParsedBody();
        $required = ['username', 'email'];

        if ($idUser !== null && \checkParam($required, $param)) {
            $result = \editUser($idUser, $param['username'], $param['email'], $param['password'] ?? null);
            $response->getBody()->write($result);
        } else {
            $response->getBody()->write(json_encode(['error' => 'Paramètres manquants']));
            return $response->withStatus(400);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function changeRole(Request $request, Response $response, array $args): Response
    {
        $idUser = $args['id'] ?? null;
        $param = $request->getParsedBody();
        $required = ['role'];

        if ($idUser !== null && \checkParam($required, $param)) {
            $result = \changeRole($idUser, $param['role'], $param['devMode'] ?? false);
            $response->getBody()->write($result);
        } else {
            $response->getBody()->write(json_encode(['error' => 'Paramètres manquants']));
            return $response->withStatus(400);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }
}
