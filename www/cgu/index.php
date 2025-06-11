<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="TheQuiz - Testez vos connaissances avec nos quiz interactifs.">
    <meta name="keywords" content="quiz, test, connaissances, interactif">
    <meta name="author" content="alexistb2904">
    <link rel="icon" href="assets/img/quizIcon.png" type="image/png">
    <title>Conditions générale d'utilisation</title>
    <link rel="stylesheet" href="../assets/css/base.css">
</head>

<body>
    <?php
    $relative_path = "../";
    require_once $relative_path . "util/components/header.php";
    ?>
    <main class="legal-main">
        <h1>Conditions Générales d'Utilisation</h1>
        <p>Dernière mise à jour : 12/06/2025</p>
        <h2>1. Objet</h2>
        <p>Les présentes CGU ont pour objet de définir les modalités d'utilisation du site TheQuiz.</p>
        <h2>2. Accès au site</h2>
        <p>L'accès au site est gratuit. L'inscription est nécessaire pour accéder à certaines fonctionnalités (participation aux quiz, statistiques, gestion de profil).</p>
        <h2>3. Création de compte</h2>
        <ul>
            <li>L'utilisateur s'engage à fournir des informations exactes lors de l'inscription.</li>
            <li>Le mot de passe est stocké de façon sécurisée (hachage).</li>
            <li>L'utilisateur peut modifier ou supprimer son compte à tout moment.</li>
        </ul>
        <h2>4. Propriété intellectuelle</h2>
        <p>Tous les contenus (textes, images, code) sont la propriété d'Alexis Thierry-Bellefond ou utilisés avec autorisation. Toute reproduction est interdite sans accord préalable.</p>
        <h2>5. Responsabilité</h2>
        <p>Le site met tout en œuvre pour assurer l'exactitude des informations, mais ne saurait être tenu responsable d'une mauvaise utilisation ou d'une indisponibilité temporaire.</p>
        <h2>6. Données personnelles</h2>
        <p>La gestion des données personnelles est détaillée dans la <a href="<?php echo $relative_path ?>pdc/">Politique de Confidentialité</a>.</p>
        <h2>7. Sécurité</h2>
        <p>L'utilisateur s'engage à préserver la confidentialité de ses identifiants et à signaler toute utilisation frauduleuse.</p>
        <h2>8. Modification des CGU</h2>
        <p>Le site se réserve le droit de modifier les présentes CGU à tout moment. Les utilisateurs seront informés en cas de modification majeure.</p>
        <h2>9. Loi applicable</h2>
        <p>Les présentes CGU sont soumises au droit français.</p>
        <h2>10. Contact</h2>
        <p>Pour toute question, contactez : contact@thequiz.fr</p>
    </main>
    <?php require_once $relative_path . "util/components/footer.php"; ?>
</body>

</html>