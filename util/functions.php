<?php
// Eviter de redéclarer les fonctions si elles existent déjà
if (!function_exists('startSession')) {
    function startSession()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    function isLogin()
    {
        startSession();
        return isset($_SESSION['username']);
    }

    function isAdmin()
    {
        startSession();
        return ($_SESSION['role'] ?? '0') == '1' ? true : false;
    }

    // PARTIE QUIZ

    // get.php
    function getQuestionForQuestionnaire($id)
    {
        require_once 'db.php';
        global $cnx;
        startSession();
        if (isLogin()) {
            try {
                $sql = "SELECT id_question, question, type, choix, reponses, id_creator, points FROM questions WHERE id_questionnaire = :id_questionnaire";
                $stmt = $cnx->prepare($sql);
                $stmt->execute([':id_questionnaire' => $id]);
                $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return json_encode(['success' => $questions]);
            } catch (PDOException $e) {
                return json_encode(['error' => $e->getMessage()]);
            }
        } else {
            return json_encode(['error' => 'Vous devez être connecté pour accéder à cette page']);
        }
    }

    // getAll.php
    function getAllQuestionnaires()
    {
        require_once 'db.php';
        global $cnx;
        startSession();
        if (isLogin()) {
            try {
                $sql = "SELECT q.id AS questionnaire_id, q.nom AS questionnaire_nom, t.nom AS theme_nom, " .
                    "COUNT(qs.id_question) AS nombre_de_questions FROM questionnaire q " .
                    "LEFT JOIN theme t ON q.theme = t.id_theme " .
                    "LEFT JOIN questions qs ON q.id = qs.id_questionnaire " .
                    "LEFT JOIN users u ON q.created_by = u.id " .
                    "GROUP BY q.id, q.nom, t.nom";
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
    }

    // getAllScores.php
    function getAllPreviousScoreForQuestionnaire($id)
    {
        require_once 'db.php';
        global $cnx;
        startSession();
        if (isLogin()) {
            try {
                $sql = "SELECT score, score_on, date, username, reponses, groupes.nom AS groupe_name, users.id as user_id, reponses FROM scores INNER JOIN users on users.id = scores.id_user INNER JOIN groupes on users.groupe_id = groupes.id WHERE id_questionnaire = :id_questionnaire";
                $stmt = $cnx->prepare($sql);
                $stmt->execute([':id_questionnaire' => $id]);
                $scores = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return json_encode(['success' => $scores]);
            } catch (PDOException $e) {
                return json_encode(['error' => $e->getMessage()]);
            }
        } else {
            return json_encode(['error' => 'Vous devez être connecté pour accéder à cette page']);
        }
    }

    function getAllScoreForAllQuestionnaire()
    {
        require_once 'db.php';
        global $cnx;
        startSession();
        if (isLogin()) {
            try {
                $sql = "SELECT score, score_on, date, username, reponses, groupes.nom AS groupe_name, users.id as user_id, reponses, id_questionnaire FROM scores INNER JOIN users on users.id = scores.id_user INNER JOIN groupes on users.groupe_id = groupes.id";
                $stmt = $cnx->prepare($sql);
                $stmt->execute();
                $scores = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return json_encode(['success' => $scores]);
            } catch (PDOException $e) {
                return json_encode(['error' => $e->getMessage()]);
            }
        } else {
            return json_encode(['error' => 'Vous devez être connecté pour accéder à cette page']);
        }
    }

    // getScore.php
    function getScoreForQuestionnaire($id)
    {
        require_once 'db.php';
        global $cnx;
        startSession();
        if (isLogin()) {
            try {
                $sql = "SELECT score, score_on, date, username, reponses, groupes.nom AS groupe_name, users.id as user_id, reponses FROM scores INNER JOIN users on users.id = scores.id_user INNER JOIN groupes on users.groupe_id = groupes.id WHERE id_questionnaire = :id_questionnaire AND id_user = :id_user";
                $stmt = $cnx->prepare($sql);
                $stmt->execute([':id_questionnaire' => $id, ':id_user' => $_SESSION['id']]);
                $scores = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return json_encode(['success' => $scores]);
            } catch (PDOException $e) {
                return json_encode(['error' => $e->getMessage()]);
            }
        } else {
            return json_encode(['error' => 'Vous devez être connecté pour accéder à cette page']);
        }
    }

    // registerScore.php
    function registerScore($id_questionnaire, $score, $score_on, $id_user, $reponses)
    {
        require_once 'db.php';
        global $cnx;
        startSession();
        if (isLogin()) {
            if ($_SESSION['id'] != $id_user) {
                echo json_encode(['error' => 'Vous n\'avez pas les droits pour effectuer cette action']);
            }
            try {
                $sql = "INSERT INTO scores (id_user, id_questionnaire, score, score_on, reponses) VALUES (:id_user, :id_questionnaire, :score, :score_on, :reponses)";
                $stmt = $cnx->prepare($sql);
                $stmt->execute([
                    ':id_user' => $_SESSION['id'],
                    ':id_questionnaire' => $id_questionnaire,
                    ':score' => $score,
                    ':score_on' => $score_on,
                    ':reponses' => $reponses
                ]);
                echo json_encode(['success' => 'Score enregistré avec succès']);
            } catch (PDOException $e) {
                echo json_encode(['error' => $e->getMessage()]);
            }
        } else {
            return json_encode(['error' => 'Vous devez être connecté pour accéder à cette page']);
        }
    }




    // PARTIE USER

    // changeRole.php
    function changeRole($id, $role)
    {
        require_once 'db.php';
        global $cnx;
        startSession();
        if (isLogin() && $_SESSION['role'] == '1') {
            try {
                $sql = "UPDATE users SET role = :role WHERE id = :id";
                $stmt = $cnx->prepare($sql);
                $stmt->execute([':role' => $role, ':id' => $id]);
                return json_encode(['success' => 'Role modifié avec succès']);
            } catch (PDOException $e) {
                return json_encode(['error' => $e->getMessage()]);
            }
        } else {
            return json_encode(['error' => 'Vous devez être connecté pour accéder à cette page']);
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
            $sql = "SELECT id, username, email, role, groupe_id FROM users WHERE username = :identifier OR email = :identifier AND password = :password";
            $stmt = $cnx->prepare($sql);
            $stmt->execute([':identifier' => $identifier, ':password' => hash('sha256', $password)]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                startSession();
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['id'] = $user['id'];
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
                if (!preg_match($regexCheck, $password)) {
                    return json_encode(['error' => 'Le mot de passe doit contenir au moins 8 caractères et 16 maximum, une lettre, un chiffre et un caractère spécial']);
                }
                $sqlRegister = "INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, '0')";
                if (isset($groupId)) {
                    $sqlRegister = "INSERT INTO users (username, email, password, role, groupe_id) VALUES (:username, :email, :password, '0', :group_id)";
                }
                $stmtRegister = $cnx->prepare($sqlRegister);
                if (isset($groupId)) {
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
            $message = 'Cliquez sur le lien suivant pour changer votre mot de passe: https://' . $_SERVER['HTTP_HOST'] . '/?email=' . $email . '&token=' . $token;
            $headers = 'From: TheQuiz';
            //mail($to, $subject, $message, $headers);

            // RETURN DEBUG
            return json_encode(['success' => 'Token envoyé avec succès', 'token' => $token, 'email' => $email]);

            // RETURN PROD
            // return json_encode(['success' => 'Token envoyé avec succès']);
        } catch (PDOException $e) {
            return json_encode(['error' => $e->getMessage()]);
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
                $userToken = $user['token'];
                $userTokenExpire = $user['token_expire'];
                $now = date('Y-m-d H:i:s');
                if ($userToken == $token && $userTokenExpire > $now) {
                    return true;
                } else {
                    return false;
                }
            }
        } else {
            return json_encode(['error' => 'Email et token non trouvés']);
        }
    }

    function changePassword($email, $token, $password, $passwordConfirm)
    {
        require_once 'db.php';
        global $cnx;
        if ($password == $passwordConfirm) {
            if (checkTokenValidity($email, $token)) {
                try {
                    $sql = "UPDATE users SET password = :password WHERE email = :email";
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
                $sql = "UPDATE users SET groupe_id = NULL WHERE id = :user_id AND groupe_id = :group_id";
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
                $sql = "SELECT * FROM types";
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
