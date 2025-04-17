<?php
$relative_path = "";
require_once $relative_path . "util/functions.php";
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="TheQuiz - Testez vos connaissances avec nos quiz interactifs.">
    <meta name="keywords" content="quiz, test, connaissances, interactif">
    <meta name="author" content="alexistb2904">
    <link rel="icon" href="assets/img/quizIcon.png" type="image/png">
    <title>Quiz</title>
    <link rel="stylesheet" href="assets/css/base.css">
</head>

<body>
    <?php include_once($relative_path . "util/components/header.php") ?>
    <main id="home">
        <h1>Bienvenue sur TheQuiz</h1>
        <p>Testez vos connaissances en répondant à nos quiz</p>
        <?php if (isLogin()) : ?>
            <a href="<?php $relative_path ?>quiz" class="CTA_btn" id="loginBtnAccueil">Commencer</a>
        <?php else : ?>
            <a id="loginHome" class="CTA_btn" id="loginBtnAccueil">Se connecter</a>
        <?php endif ?>
    </main>
    <?php include_once($relative_path . "util/components/footer.php") ?>
    <script>
        const loginBtnA = document.querySelector('#loginHome');
        loginBtnA?.addEventListener('click', () => {
            createModal('login');
        });
    </script>
</body>

</html>