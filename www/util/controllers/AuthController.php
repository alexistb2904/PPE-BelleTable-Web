<?php

namespace App\Controllers;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../functions.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AuthController
{
    public function register(Request $request, Response $response): Response
    {
        $param = $request->getParsedBody();
        $required = ['username', 'email', 'password'];

        if (\checkParam($required, $param)) {
            $result = register(
                $param['username'],
                $param['email'],
                $param['password'],
                $param['groupID'] ?? null,
                $param['creationAdmin'] ?? null
            );

            $response->getBody()->write($result);
        } else {
            $response->getBody()->write(json_encode(['error' => 'Paramètres manquants']));
            return $response->withStatus(400);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function login(Request $request, Response $response): Response
    {
        $param = $request->getParsedBody();
        $required = ['identifiant', 'password'];
        if (\checkParam($required, $param)) {
            $result = login($param['identifiant'], $param['password']);

            $response->getBody()->write($result);
        } else {
            $response->getBody()->write(json_encode(['error' => 'Paramètres manquants']));
            return $response->withStatus(400);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function logout(Request $request, Response $response): Response
    {
        $result = logout();
        $response->getBody()->write($result);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function resetPassword(Request $request, Response $response): Response
    {
        $param = $request->getParsedBody();
        $required = ['email'];

        if (\checkParam($required, $param)) {
            $result = resetPassword(htmlspecialchars($param['email']));

            $response->getBody()->write($result);
        } else {
            $response->getBody()->write(json_encode(['error' => 'Paramètres manquants']));
            return $response->withStatus(400);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function changePassword(Request $request, Response $response): Response
    {
        $param = $request->getParsedBody();
        $required = ['email', 'token', 'password', 'passwordConfirm'];

        if (\checkParam($required, $param)) {
            $result = \changePassword(
                htmlspecialchars($param['email']),
                htmlspecialchars($param['token']),
                htmlspecialchars($param['password']),
                htmlspecialchars($param['passwordConfirm'])
            );

            $response->getBody()->write($result);
        } else {
            $response->getBody()->write(json_encode(['error' => 'Paramètres manquants']));
            return $response->withStatus(400);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }
}
