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

<body> <?php include_once($relative_path . "util/components/header.php") ?>
    <main id="profil" class="profile-main">
        <?php if ($user->success) : ?>
            <!-- Hero Section -->
            <section class="profile-hero">
                <div class="container">
                    <div class="profile-hero-content">
                        <div class="profile-avatar">
                            <div class="avatar-circle">
                                <img src="https://ui-avatars.com/api/?rounded=true&name=<?php echo urlencode($user->success->username) ?>&background=f1356d&color=fffff"
                                    alt="Avatar de <?php echo htmlspecialchars($user->success->username) ?>" />
                            </div>
                            <div class="avatar-status online"></div>
                        </div>
                        <div class="profile-info">
                            <h1 class="profile-name">Bonjour, <?php echo htmlspecialchars($user->success->username) ?></h1>
                            <p class="profile-subtitle">Gérez votre profil et vos informations personnelles</p>
                            <div class="profile-badge">
                                👥
                                <span><?php echo htmlspecialchars($user->success->groupe_name) ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Profile Content -->
            <section class="profile-content">
                <div class="container">
                    <div class="profile-grid">
                        <!-- Personal Information Card -->
                        <div class="profile-card personal-info-card">
                            <div class="card-header">
                                <h2>📋 Informations personnelles</h2>
                                <p>Modifiez vos données de profil</p>
                            </div>
                            <div class="card-body">
                                <form class="profile-form">
                                    <div class="form-group">
                                        <label for="username">

                                            <span>Nom d'utilisateur</span>
                                        </label>
                                        <input
                                            id="username"
                                            name="username"
                                            type="text"
                                            value="<?php echo htmlspecialchars($user->success->username) ?>"
                                            placeholder="Votre nom d'utilisateur" />
                                    </div>
                                    <div class="form-group">
                                        <label for="email">
                                            <span>Adresse email</span>
                                        </label>
                                        <input
                                            id="email"
                                            name="email"
                                            type="email"
                                            value="<?php echo htmlspecialchars($user->success->email) ?>"
                                            placeholder="votre@email.com" />
                                    </div>
                                    <div class="form-group">
                                        <label for="groupe">

                                            <span>Mon groupe</span>
                                        </label>
                                        <input
                                            id="groupe"
                                            name="groupe"
                                            type="text"
                                            disabled
                                            readonly
                                            value="<?php echo htmlspecialchars($user->success->groupe_name) ?>" />
                                    </div>

                                    <input type="hidden" name="idUser" value="<?php echo $user->success->id ?>" />
                                </form>
                            </div>
                            <div class="card-footer">
                                <button class="btn btn-primary" id="editAccount">
                                    💾
                                    Sauvegarder les modifications
                                </button>
                            </div>
                        </div>

                        <!-- Account Actions Card -->
                        <div class="profile-card actions-card">
                            <div class="card-header">
                                <h2>Actions du compte</h2>
                            </div>
                            <div class="card-body">
                                <div class="action-item">
                                    <div class="action-info">
                                        <h3>Mot de passe</h3>
                                        <p>Changez votre mot de passe pour sécuriser votre compte</p>
                                    </div> <button class="btn btn-outline" id="changePassword">

                                        Changer
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </section>
        <?php else : ?> <section class="error-section">
                <div class="container">
                    <div class="error-content">
                        ⚠️
                        <h2>Erreur de chargement</h2>
                        <p>Impossible de récupérer vos informations de profil</p>
                        <a href="<?php echo $relative_path ?>" class="btn btn-primary">
                            Retour à l'accueil
                        </a>
                    </div>
                </div>
            </section>
        <?php endif; ?>
    </main>
    <?php include_once($relative_path . "util/components/footer.php") ?>
    <script src="<?php echo $relative_path ?>assets/js/profilPage.js"></script>
</body>

</html>