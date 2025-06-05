<?php
ob_start();
require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Eviter de redéclarer les fonctions si elles existent déjà
if (!function_exists('startSession')) {
    function startSession()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    function isLogin()
    {
        startSession();
        return isset($_SESSION['username']);
    }

    function setCookieFunc($variableToSet)
    {
        if (!isset($_COOKIE[$variableToSet]) || $_COOKIE[$variableToSet] !== $_SESSION[$variableToSet]) {
            $cookieVar = $_SESSION[$variableToSet] ?? null;
            if ($cookieVar !== null) {
                setcookie($variableToSet, $cookieVar, time() + 3600, '/');
            }
        }
    }
    function isAdmin()
    {
        startSession();
        return ($_SESSION['role'] ?? '0') == '1' ? true : false;
    }

    // PARTIE QUIZ

    // get.php
    function getQuestionnaire($id)
    {
        require_once 'db.php';
        global $cnx;
        startSession();

        if (!isLogin()) {
            return json_encode(['error' => 'Vous devez être connecté pour accéder à cette page']);
        }

        try {
            // Récupérer le questionnaire
            $sql = "
            SELECT 
                q.id,
                q.nom,
                t.nom AS theme,
                u.id AS cree_par,
                q.creation_date,
                COUNT(DISTINCT s.id_user) AS nb_participants
            FROM questionnaire q
            JOIN theme t ON q.theme = t.id_theme
            JOIN users u ON q.created_by = u.id
            LEFT JOIN scores s ON s.id_questionnaire = q.id
            WHERE q.id = :id
            GROUP BY q.id
        ";
            $stmt = $cnx->prepare($sql);
            $stmt->execute([':id' => $id]);
            $questionnaire = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$questionnaire) {
                return json_encode(['error' => 'Questionnaire introuvable']);
            }

            // Récupérer les questions + leurs choix
            $sql = "
            SELECT 
                q.id_question,
                q.question,
                t.nom AS type,
                q.id_creator
            FROM questions q
            JOIN types t ON q.type = t.id_type
            WHERE q.id_questionnaire = :id
            ";
            $stmt = $cnx->prepare($sql);
            $stmt->execute([':id' => $id]);
            $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Pour chaque question récupérer les choix
            foreach ($questions as &$question) {
                $sql = "
                SELECT 
                    c.id,
                    c.texte,
                    c.est_reponse,
                    c.points
                FROM choix c
                WHERE c.id_question = :id_question
            ";
                $stmt = $cnx->prepare($sql);
                $stmt->execute([':id_question' => $question['id_question']]);
                $question['choix'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }

            // Regrouper les données
            $questionnaire['questions'] = $questions;

            return json_encode(['success' => $questionnaire]);
        } catch (PDOException $e) {
            return json_encode(['error' => $e->getMessage()]);
        }
        /* Exemple de retour 
        {
        "success": {
            "id": 1,
            "nom": "Quiz PHP",
            "theme": "Programmation",
            "cree_par": 1,
            "creation_date": "2025-03-01 12:00:00",
            "nb_participants": 5,
            "questions": [
            {
                "id_question": 10,
                "question": "Qu'est-ce que PDO ?",
                "type": "Choix Multiples",
                "id_creator": 2,
                "choix": [
                {
                    "id": 20,
                    "texte": "Une extension PHP pour gérer la base de données",
                    "est_reponse": 1,
                    "points": 1
                },
                ...
                ]
            },
            ...
            ]
        }
        }
        */
    }


    // getAll.php
    function getAllQuestionnaires()
    {
        require_once 'db.php';
        global $cnx;
        startSession();
        if (isLogin()) {
            try {
                $sql = "SELECT 
                        q.id,
                        q.nom,
                        t.nom AS theme,
                        u.id AS cree_par,
                        q.creation_date,
                        COUNT(DISTINCT ques.id_question) AS nb_questions,
                        COUNT(DISTINCT s.id_user) AS nb_participants
                        FROM questionnaire q
                        JOIN theme t ON q.theme = t.id_theme
                        JOIN users u ON q.created_by = u.id
                        LEFT JOIN questions ques ON ques.id_questionnaire = q.id
                        LEFT JOIN scores s ON s.id_questionnaire = q.id
                        GROUP BY q.id
                        ";
                $stmt = $cnx->prepare($sql);
                $stmt->execute();
                $questionnaires = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return json_encode(['success' => $questionnaires]);
            } catch (PDOException $e) {
                return json_encode(['error' => $e->getMessage()]);
            }
        } else {
            return json_encode(['error' => 'Vous devez être connecté pour accéder à cette page']);
        }
        /* Exemple de retour 
        {
        "success": [
            {
            "id": 1,
            "nom": "Quiz PHP",
            "theme": "Programmation",
            "cree_par": 1,
            "creation_date": "2025-03-01 12:00:00",
            "nb_questions": 5,
            "nb_participants": 5
            },
            ...
        ]
        }
        */
    }

    // getAllScores.php
    function getAllScoresForQuestionnaire($id)
    {
        require_once 'db.php';
        global $cnx;
        startSession();

        if (!isLogin()) {
            return json_encode(['error' => 'Vous devez être connecté pour accéder à cette page']);
        }

        try {
            // 1. Récupérer tous les scores du questionnaire avec utilisateur et groupe
            $sql = "SELECT scores.id as score_id, score, score_on, date, username, groupes.nom AS groupe_name, users.id as user_id
                FROM scores
                INNER JOIN users ON users.id = scores.id_user
                INNER JOIN groupes ON users.groupe_id = groupes.id
                WHERE id_questionnaire = :id_questionnaire";
            $stmt = $cnx->prepare($sql);
            $stmt->execute([':id_questionnaire' => $id]);
            $scores = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // 2. Pour chaque score, récupérer les réponses données par l'utilisateur
            $sqlReponses = "SELECT 
                            q.id_question, 
                            q.question, 
                            c.id AS id_choix, 
                            c.texte AS choix, 
                            c.est_reponse
                        FROM reponses_utilisateur ru
                        INNER JOIN choix c ON c.id = ru.id_choix
                        INNER JOIN questions q ON q.id_question = ru.id_question
                        WHERE ru.id_score = :id_score";
            $stmtReponses = $cnx->prepare($sqlReponses);

            foreach ($scores as &$score) {
                $stmtReponses->execute([':id_score' => $score['score_id']]);
                $score['reponses'] = $stmtReponses->fetchAll(PDO::FETCH_ASSOC);
            }

            return json_encode(['success' => $scores]);
        } catch (PDOException $e) {
            return json_encode(['error' => $e->getMessage()]);
        }
        /* Exemple de retour 
        {
        "success": [
            {
            "score_id": 10,
            "score": 7,
            "score_on": 10,
            "date": "2025-03-15 14:00:00",
            "username": "Alexis",
            "groupe_name": "BTS SIO2",
            "user_id": 3,
            "reponses": [
                {
                "id_question": 1,
                "question": "Quelle est la capitale de la France ?",
                "id_choix": 5,
                "choix": "Paris",
                "est_reponse": 1
                },
                {
                "id_question": 2,
                "question": "Combien font 2 + 2 ?",
                "id_choix": 8,
                "choix": "4",
                "est_reponse": 1
                }
            ]
            }
        ]
        }
        */
    }


    function getAllScoreForAllQuestionnaire()
    {
        require_once 'db.php';
        global $cnx;
        startSession();

        if (!isLogin()) {
            return json_encode(['error' => 'Vous devez être connecté pour accéder à cette page']);
        }

        try {
            // 1. Récupération des scores + infos utilisateur/groupe/questionnaire
            $sql = "SELECT scores.id as score_id, score, score_on, date, username, groupes.nom AS groupe_name, users.id as user_id, id_questionnaire, questionnaire.nom AS questionnaire_name
                FROM scores
                INNER JOIN users ON users.id = scores.id_user
                INNER JOIN questionnaire ON questionnaire.id = scores.id_questionnaire
                INNER JOIN groupes ON users.groupe_id = groupes.id";
            $stmt = $cnx->prepare($sql);
            $stmt->execute();
            $scores = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // 2. Récupération des réponses liées à chaque score avec question et choix en clair
            $sqlReponses = "SELECT 
                            q.id_question, 
                            q.question, 
                            c.id AS id_choix, 
                            c.texte AS choix, 
                            c.est_reponse
                        FROM reponses_utilisateur ru
                        INNER JOIN choix c ON c.id = ru.id_choix
                        INNER JOIN questions q ON q.id_question = ru.id_question
                        WHERE ru.id_score = :id_score";
            $stmtReponses = $cnx->prepare($sqlReponses);

            foreach ($scores as &$score) {
                $stmtReponses->execute([':id_score' => $score['score_id']]);
                $score['reponses'] = $stmtReponses->fetchAll(PDO::FETCH_ASSOC);
            }

            return json_encode(['success' => $scores]);
        } catch (PDOException $e) {
            return json_encode(['error' => $e->getMessage()]);
        }

        /*
        Exemple de retour :
        {
        "success": [
            {
            "score_id": 10,
            "score": 8,
            "score_on": 10,
            "date": "2025-03-15 13:45:00",
            "username": "Alexis",
            "groupe_name": "BTS SIO2",
            "user_id": 5,
            "id_questionnaire": 3,
            "reponses": [
                {
                "id_question": 1,
                "question": "Combien font 6 x 7 ?",
                "id_choix": 7,
                "choix": "42",
                "est_reponse": 1
                },
                ...
            ]
            }
        ]
        }
    */
    }



    // getScore.php
    function getScoreForQuestionnaire($id)
    {
        require_once 'db.php';
        global $cnx;
        startSession();

        if (!isLogin()) {
            return json_encode(['error' => 'Vous devez être connecté pour accéder à cette page']);
        }

        try {
            // Récupérer le score de l'utilisateur pour ce questionnaire
            $sql = "
        SELECT 
            s.id AS score_id,
            s.score,
            s.score_on,
            s.date,
            u.username,
            u.id AS user_id,
            g.nom AS groupe_name
        FROM scores s
        INNER JOIN users u ON u.id = s.id_user
        INNER JOIN groupes g ON u.groupe_id = g.id
        WHERE s.id_questionnaire = :id_questionnaire
        AND s.id_user = :id_user
        ";
            $stmt = $cnx->prepare($sql);
            $stmt->execute([
                ':id_questionnaire' => $id,
                ':id_user' => $_SESSION['id']
            ]);
            $scoreData = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!$scoreData) {
                return json_encode(['success' => []]);
            }

            // Récupérer les réponses utilisateur associées avec les questions et les choix
            foreach ($scoreData as &$score) {
                $sql = "
                SELECT 
                    q.id_question,
                    q.question,
                    c.id AS id_choix,
                    c.texte AS choix,
                    c.est_reponse
                FROM reponses_utilisateur ru
                INNER JOIN choix c ON c.id = ru.id_choix
                INNER JOIN questions q ON q.id_question = ru.id_question
                WHERE ru.id_score = :score_id
                ";
                $stmt = $cnx->prepare($sql);
                $stmt->execute([':score_id' => $score['score_id']]);
                $reponses = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $score['reponses'] = $reponses;
            }

            return json_encode(['success' => $scoreData]);
        } catch (PDOException $e) {
            return json_encode(['error' => $e->getMessage()]);
        }
        /* Exemple de retour 
        {
        "success": {
            "score_id": 10,
            "score": 7,
            "score_on": 10,
            "date": "2025-03-15 14:00:00",
            "username": "mathieu",
            "user_id": 3,
            "groupe_name": "BTS SIO2",
            "reponses": [
                {
                    "id_question": 1,
                    "question": "Quelle est la capitale de la France ?",
                    "id_choix": 5,
                    "choix": "Paris",
                    "est_reponse": 1
                },
                ...
                ]
        }
        } */
    }



    // registerScore.php
    function registerScore($id_questionnaire, $score, $score_on, $id_user, $reponses)
    {
        /* PAYLOAD ATTENDU :
        {
        "id_user": 3,
        "score": 7,
        "score_on": 10,
        "reponses": [
            { "id_question": 12, "id_choix": 45 },
            { "id_question": 13, "id_choix": 48 }
        ]
        }
        */
        require_once 'db.php';
        global $cnx;
        startSession();
        if (!isLogin()) {
            return json_encode(['error' => 'Vous devez être connecté pour accéder à cette page']);
        }

        if ($_SESSION['id'] != $id_user) {
            return json_encode(['error' => 'Vous n\'avez pas les droits pour effectuer cette action']);
        }

        try {
            // 1. Insérer le score
            $sql = "INSERT INTO scores (id_user, id_questionnaire, score, score_on) 
                VALUES (:id_user, :id_questionnaire, :score, :score_on)";
            $stmt = $cnx->prepare($sql);
            $stmt->execute([
                ':id_user' => $_SESSION['id'],
                ':id_questionnaire' => $id_questionnaire,
                ':score' => $score,
                ':score_on' => $score_on
            ]);

            $id_score = $cnx->lastInsertId();

            // 2. Insérer les réponses dans la table reponses_utilisateur
            $sql = "INSERT INTO reponses_utilisateur (id_score, id_question, id_choix) 
                VALUES (:id_score, :id_question, :id_choix)";
            $stmt = $cnx->prepare($sql);
            foreach ($reponses as $reponse) {
                $stmt->execute([
                    ':id_score' => $id_score,
                    ':id_question' => $reponse['id_question'],
                    ':id_choix' => $reponse['id_choix']
                ]);
            }

            return json_encode(['success' => 'Score et réponses enregistrés avec succès']);
        } catch (PDOException $e) {
            return json_encode(['error' => $e->getMessage()]);
        }
    }





    // PARTIE USER

    // changeRole.php
    function changeRole($id, $role, $devMode = false)
    {
        require_once 'db.php';
        global $cnx;
        startSession();
        if (isLogin() && ($_SESSION['role'] == '1')) {
            if ($devMode) {
                logout();
            }
            if ($_SESSION['id'] == $id) {
                return json_encode(['error' => 'Vous ne pouvez pas changer votre propre rôle']);
            }
            try {
                $sql = "UPDATE users SET role = :role WHERE id = :id";
                $stmt = $cnx->prepare($sql);
                $stmt->execute([':role' => $role, ':id' => $id]);
                return json_encode(['success' => 'Role modifié avec succès']);
            } catch (PDOException $e) {
                return json_encode(['error' => $e->getMessage()]);
            }
        } else {
            return json_encode(['error' => 'Vous devez être connecté pour accéder à cette page ou être administrateur']);
        }
    }

    // checkPassword.php
    function checkPassword($password)
    {
        require_once 'db.php';
        global $cnx;
        startSession();
        if (isLogin()) {
            try {
                $sql = "SELECT password FROM users WHERE username = :username";
                $stmt = $cnx->prepare($sql);
                $stmt->execute([':username' => $_SESSION['username']]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                if (hash('sha256', $password) == $user['password']) {
                    return json_encode(['success' => 'Mot de passe correct']);
                } else {
                    return json_encode(['error' => 'Mot de passe incorrect']);
                }
            } catch (PDOException $e) {
                return json_encode(['error' => $e->getMessage()]);
            }
        } else {
            return json_encode(['error' => 'Vous devez être connecté pour accéder à cette page']);
        }
    }

    // edit.php
    function editUser($id, $username, $email, $password = null)
    {
        require_once 'db.php';
        global $cnx;
        startSession();
        if (isLogin() && ($_SESSION['role'] == '1' || $_SESSION['id'] == $id)) {
            try {
                $sql = "UPDATE users SET username = :username, email = :email WHERE id = :id";
                if (isset($password)) {
                    $sql = "UPDATE users SET username = :username, email = :email WHERE id = :id AND password = :password";
                }
                $stmt = $cnx->prepare($sql);
                if (isset($password)) {
                    $stmt->execute([':username' => $username, ':email' => $email, ':password' => hash('sha256', $password), ':id' => $id]);
                } else {
                    $stmt->execute([':username' => $username, ':email' => $email, ':id' => $id]);
                }
                return json_encode(['success' => 'Utilisateur modifié avec succès']);
            } catch (PDOException $e) {
                return json_encode(['error' => $e->getMessage()]);
            }
        } else {
            return json_encode(['error' => 'Vous devez être connecté pour accéder à cette page']);
        }
    }

    // get.php
    function getUserById($id)
    {
        require_once 'db.php';
        global $cnx;
        startSession();
        if ($_SESSION['username'] && ($_SESSION['role'] == '1' || $_SESSION['id'] == $id)) {
            try {
                $sql = "SELECT id, username, email, role, groupe_id FROM users WHERE id = :id";
                $stmt = $cnx->prepare($sql);
                $stmt->execute([':id' => $id]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                return json_encode(['success' => $user]);
            } catch (PDOException $e) {
                return json_encode(['error' => $e->getMessage()]);
            }
        } else {
            return json_encode(['error' => 'Vous devez être connecté pour accéder à cette page']);
        }
    }

    // getAll.php
    function getAllUsers()
    {
        require_once 'db.php';
        global $cnx;
        startSession();
        if (isset($_SESSION['username']) && isset($_SESSION['role']) && $_SESSION['role'] == '1') {
            try {
                $sql = "SELECT users.id, username, email, role, groupe_id, groupes.nom AS groupe_name FROM users LEFT JOIN groupes ON users.groupe_id = groupes.id";
                $stmt = $cnx->prepare($sql);
                $stmt->execute();
                $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return json_encode(['success' => $users]);
            } catch (PDOException $e) {
                return json_encode(['error' => $e->getMessage()]);
            }
        } else {
            return json_encode(['error' => 'Vous devez être connecté pour accéder à cette page']);
        }
    }

    // get.php (Without Arguments)
    function getSelfUser()
    {
        require_once 'db.php';
        global $cnx;
        startSession();
        if (isset($_SESSION['username']) && isset($_SESSION['id'])) {
            try {
                $sql = "SELECT users.id, username, email, role, groupe_id, groupes.nom AS groupe_name FROM users LEFT JOIN groupes ON users.groupe_id = groupes.id WHERE username = :username AND users.id = :id";
                $stmt = $cnx->prepare($sql);
                $stmt->execute([
                    ':username' => $_SESSION['username'],
                    ':id' => $_SESSION['id']
                ]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                return json_encode(['success' => $user]);
            } catch (PDOException $e) {
                return json_encode(['error' => $e->getMessage()]);
            }
        } else {
            return json_encode(['error' => 'Vous devez être connecté pour accéder à cette page']);
        }
    }

    // login.php
    function login($identifier, $password)
    {
        require_once 'db.php';
        global $cnx;
        try {
            $sql = "SELECT id, username, email, role, groupe_id FROM users WHERE (username = :identifier OR email = :identifier) AND password = :password";
            $stmt = $cnx->prepare($sql);
            $stmt->execute([':identifier' => $identifier, ':password' => hash('sha256', $password)]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                startSession();
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['id'] = $user['id'];
                $_SESSION['group_id'] = $user['groupe_id'];
                return json_encode(['success' => 'Connecté avec succès']);
            } else {
                return json_encode(['error' => 'Utilisateur non trouvé']);
            }
        } catch (PDOException $e) {
            return json_encode(['error' => $e->getMessage()]);
        }
    }

    // logout.php
    function logout()
    {
        startSession();
        session_destroy();
        return json_encode(['success' => 'Déconnecté avec succès']);
    }

    // register.php
    function register($username, $email, $password, $groupId = null)
    {
        require_once 'db.php';
        global $cnx;
        $regexCheck = "/^(?=.*\d)(?=.*[A-Z])(?=.*[a-z])(?=.*[^\w\d\s:])([^\s]){8,16}$/";
        try {
            $sql = "SELECT * FROM users WHERE email = :email";
            $stmt = $cnx->prepare($sql);
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$user) {
                /*if (!preg_match($regexCheck, $password)) {
                    return json_encode(['error' => 'Le mot de passe doit contenir au moins 8 caractères et 16 maximum, une lettre, un chiffre et un caractère spécial']);
                }*/
                $sqlRegister = "INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, '0')";
                if (isset($groupId) && $groupId != null && $groupId != '') {
                    $sqlRegister = "INSERT INTO users (username, email, password, role, groupe_id) VALUES (:username, :email, :password, '0', :group_id)";
                }
                $stmtRegister = $cnx->prepare($sqlRegister);
                if (isset($groupId) && $groupId != null && $groupId != '') {
                    $stmtRegister->execute([
                        ':username' => $username,
                        ':email' => $email,
                        ':password' => hash('sha256', $password),
                        ':group_id' => $groupId
                    ]);
                } else {
                    $stmtRegister->execute([
                        ':username' => $username,
                        ':email' => $email,
                        ':password' => hash('sha256', $password)
                    ]);
                }
                startSession();
                $_SESSION['username'] = $username;
                $_SESSION['role'] = 0;
                $_SESSION['group_id'] = $groupId ?? null;
                $_SESSION['id'] = $cnx->lastInsertId();
                return json_encode(['success' => 'Crée et connecté avec succès']);
            } else {
                return json_encode(['error' => 'Utilisateur déjà existant']);
            }
        } catch (PDOException $e) {
            return json_encode(['error' => $e->getMessage()]);
        }
    }

    // resetPassword.php
    function resetPassword($email)
    {
        require_once 'db.php';
        global $cnx;
        $token = bin2hex(random_bytes(32));
        $tokenExpire = date('Y-m-d H:i:s', strtotime('+1 day'));
        try {
            if (isset($_SESSION['email'])) {
                $email = $_SESSION['email'];
            }
            $sql = "UPDATE users SET token = :token, token_expire = :token_expire WHERE email = :email";
            $stmt = $cnx->prepare($sql);
            $stmt->execute([
                ':token' => $token,
                ':token_expire' => $tokenExpire,
                ':email' => $email
            ]);
            // Envoi du token par email
            $to = $email;
            $subject = 'Changement de mot de passe';
            $message = 'Cliquez sur le lien suivant pour changer votre mot de passe: http://' . $_SERVER['HTTP_HOST'] . '/?email=' . $email . '&token=' . $token;
            $headers = 'From: TheQuiz';
            mail($to, $subject, $message, $headers);



            // RETURN DEBUG
            return json_encode(['success' => 'Token envoyé avec succès']);

            // RETURN PROD
            // return json_encode(['success' => 'Token envoyé avec succès']);
        } catch (PDOException $e) {
            return json_encode(['error' => $e->getMessage()]);
        }
    }

    // deleteUser.php

    function deleteUser($id)
    {
        require_once 'db.php';
        global $cnx;
        startSession();
        if (isLogin() && ($_SESSION['role'] == '1')) {
            try {
                $sql = "DELETE FROM users WHERE id = :id";
                $stmt = $cnx->prepare($sql);
                $stmt->execute([':id' => $id]);
                return json_encode(['success' => 'Utilisateur supprimé avec succès']);
            } catch (PDOException $e) {
                return json_encode(['error' => $e->getMessage()]);
            }
        } else {
            return json_encode(['error' => 'Vous devez être connecté pour accéder à cette page']);
        }
    }

    // checkToken.php
    function checkTokenValidity($email, $token)
    {
        require_once 'db.php';
        global $cnx;
        if (!empty($email) && !empty($token)) {
            $sql = "SELECT * FROM users WHERE email = :email AND token = :token";
            $stmt = $cnx->prepare($sql);
            $stmt->execute([
                ':email' => $email,
                ':token' => $token
            ]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$user) {
                return json_encode(['error' => 'Utilisateur non trouvé']);
            } else {
                $userToken = $user['token'] ?? null;
                $userTokenExpire = $user['token_expire'] ?? null;
                $now = date('Y-m-d H:i:s');
                if ($userToken == $token && $userTokenExpire > $now) {
                    return true;
                } else {
                    return false;
                }
                return false;
            }
            return false;
        } else {
            return json_encode(['error' => 'Email et token non trouvés']);
        }
    }

    function changePassword($email, $token, $password, $passwordConfirm)
    {
        require_once 'db.php';
        global $cnx;
        if ($password == $passwordConfirm) {
            $tokenIsValid = checkTokenValidity($email, $token);
            if ($tokenIsValid === true) {
                try {
                    $sql = "UPDATE users SET password = :password, token = NULL, token_expire = NULL WHERE email = :email";
                    $stmt = $cnx->prepare($sql);
                    $stmt->execute([
                        ':password' => hash('sha256', $password),
                        ':email' => $email
                    ]);
                    return json_encode(['success' => 'Mot de passe changé avec succès']);
                } catch (PDOException $e) {
                    return json_encode(['error' => $e->getMessage()]);
                }
            } else {
                return json_encode(['error' => 'Token invalide ou expiré']);
            }
        } else {
            return json_encode(['error' => 'Les mots de passe ne correspondent pas']);
        }
    }

    // PARTIE GROUPE

    // addUser.php

    function addUserToGroup($user_id, $group_id)
    {
        require_once 'db.php';
        global $cnx;
        startSession();
        if (isset($_SESSION['username']) && isset($_SESSION['role']) && $_SESSION['role'] == '1') {
            try {
                $sql = "UPDATE users SET groupe_id = :group_id WHERE id = :user_id";
                $stmt = $cnx->prepare($sql);
                $stmt->execute([':group_id' => $group_id, ':user_id' => $user_id]);
                return json_encode(['success' => 'Utilisateur ajouté au groupe avec succès']);
            } catch (PDOException $e) {
                return json_encode(['error' => $e->getMessage()]);
            }
        } else {
            return json_encode(['error' => 'Vous devez être connecté pour accéder à cette page']);
        }
    }

    // removeUser.php
    function removeUserFromGroup($user_id, $group_id)
    {
        require_once 'db.php';
        global $cnx;
        startSession();
        if (isset($_SESSION['username']) && isset($_SESSION['role']) && $_SESSION['role'] == '1') {
            try {
                $sql = "UPDATE users SET groupe_id = 0 WHERE id = :user_id AND groupe_id = :group_id";
                $stmt = $cnx->prepare($sql);
                $stmt->execute([':user_id' => $user_id, ':group_id' => $group_id]);
                return json_encode(['success' => 'Utilisateur retiré du groupe avec succès']);
            } catch (PDOException $e) {
                return json_encode(['error' => $e->getMessage()]);
            }
        } else {
            return json_encode(['error' => 'Vous devez être connecté pour accéder à cette page']);
        }
    }

    // create.php 
    function createGroup($name)
    {
        require_once 'db.php';
        global $cnx;
        startSession();
        if (isset($_SESSION['username']) && isset($_SESSION['role']) && $_SESSION['role'] == '1') {
            try {
                $sql = "INSERT INTO groupes (nom) values (:nom)";
                $stmt = $cnx->prepare($sql);
                $stmt->execute([':nom' => $name]);
                return json_encode(['success' => 'Groupe crée avec succès']);
            } catch (PDOException $e) {
                return json_encode(['error' => $e->getMessage()]);
            }
        } else {
            return json_encode(['error' => 'Vous devez être connecté pour accéder à cette page']);
        }
    }

    // delete.php
    function deleteGroup($id)
    {
        require_once 'db.php';
        global $cnx;
        startSession();
        if (isset($_SESSION['username']) && isset($_SESSION['role']) && $_SESSION['role'] == '1') {
            try {
                $sql = "DELETE FROM groupes WHERE id = :id";
                $stmt = $cnx->prepare($sql);
                $stmt->execute([':id' => $id]);
                return json_encode(['success' => 'Groupe supprimé avec succès']);
            } catch (PDOException $e) {
                return json_encode(['error' => $e->getMessage()]);
            }
        } else {
            return json_encode(['error' => 'Vous devez être connecté pour accéder à cette page']);
        }
    }

    // edit.php
    function editGroup($id, $name)
    {
        require_once 'db.php';
        global $cnx;
        startSession();
        if (isset($_SESSION['username']) && isset($_SESSION['role']) && $_SESSION['role'] == '1') {
            try {
                $sql = "UPDATE groupes SET nom = :nom WHERE id = :id";
                $stmt = $cnx->prepare($sql);
                $stmt->execute([':nom' => $name, ':id' => $id]);
                return json_encode(['success' => 'Groupe modifié avec succès']);
            } catch (PDOException $e) {
                return json_encode(['error' => $e->getMessage()]);
            }
        } else {
            return json_encode(['error' => 'Vous devez être connecté pour accéder à cette page']);
        }
    }

    // getParticipants.pgp
    function getParticipantsOfGroup($id)
    {
        require_once 'db.php';
        global $cnx;
        startSession();
        if (isset($_SESSION['username']) && isset($_SESSION['role']) && $_SESSION['role'] == '1') {
            try {
                $sql = "SELECT id, username, email FROM users WHERE groupe_id = :id";
                $stmt = $cnx->prepare($sql);
                $stmt->execute([':id' => $id]);
                $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return json_encode(['success' => $users]);
            } catch (PDOException $e) {
                return json_encode(['error' => $e->getMessage()]);
            }
        } else {
            return json_encode(['error' => 'Vous devez être connecté pour accéder à cette page']);
        }
    }

    function getAllGroups()
    {
        require_once 'db.php';
        global $cnx;
        $sql = "SELECT * FROM groupes";
        $stmt = $cnx->prepare($sql);
        $stmt->execute();
        $groupes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!$groupes) {
            return json_encode(['error' => 'Groupes non trouvé']);
        } else {
            return json_encode(['success' => $groupes]);
        }
    }

    function associateGroupIDToName($id)
    {
        require_once 'db.php';
        global $cnx;
        $sql = "SELECT nom FROM groupes WHERE id = :id";
        $stmt = $cnx->prepare($sql);
        $stmt->execute([':id' => $id]);
        $group = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$group) {
            return json_encode(['error' => 'Groupe non trouvé']);
        } else {
            return json_encode(['success' => $group]);
        }
    }

    // Utilitaire
    function getAllTypeLibelle()
    {
        require_once 'db.php';
        global $cnx;
        startSession();
        if (isLogin()) {
            try {
                $sql = "SELECT id_type, nom FROM types";
                $stmt = $cnx->prepare($sql);
                $stmt->execute();
                $types = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return json_encode(['success' => $types]);
            } catch (PDOException $e) {
                return json_encode(['error' => $e->getMessage()]);
            }
        } else {
            return json_encode(['error' => 'Vous devez être connecté pour accéder à cette page']);
        }
    }

    function checkParam($toCheck, $array)
    {
        foreach ($toCheck as $param) {
            if (!isset($array[$param])) {
                return false;
            }
        }
        return true;
    }
}
