<?php
$relative_path = "../";
require_once $relative_path . "util/functions.php";

if (!isLogin()) {
    header("Location: " . $relative_path);
}

$user = json_decode(getSelfUser());
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil</title>
    <link rel="stylesheet" href="<?php echo $relative_path ?>assets/css/base.css">
</head>

<body>
    <?php include_once($relative_path . "util/components/header.php") ?>
    <main id="profil">
        <?php if ($user->success) : ?>
            <div class="profilContainer">
                <h1>Profil</h1>
                <p>Consultez vos informations personnelles</p>
                <label>
                    <span>Nom d'utilisateur</span>
                    <input name="username" type="text" value="<?php echo $user->success->username ?>" />
                </label>
                <label>
                    <span>Email</span>
                    <input name="email" type="email" value="<?php echo $user->success->email ?>" />
                </label>
                <label>
                    <span>Mot de passe</span>
                    <input name="password" type="password" />
                </label>
                <label>
                    Mon Groupe
                    <input name="groupe" type="text" disabled readonly value="<?php echo $user->success->groupe_name ?>" />
                </label>
                <input type="hidden" name="idUser" value="<?php echo $user->success->id ?>" />
                <a class="CTA_btn" id="changePassword">Changer de mot de passe</a>
                <a class="CTA_btn" id="editAccount">Modifier mon compte</a>
            </div>
        <?php else : ?>
            <p>Erreur lors de la récupération de vos informations</p>
        <?php endif; ?>
    </main>
    <?php include_once($relative_path . "util/components/footer.php") ?>
    <script src="<?php echo $relative_path ?>assets/js/profilPage.js"></script>
</body>

</html>