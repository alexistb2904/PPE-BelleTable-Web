<?php
require_once $relative_path . "util/functions.php";

?>

<header>
    <nav>
        <h1>TheQuiz</h1>
        <ul>
            <li><a class="CTA_nav" href="<?php echo $relative_path ?>index.php">Accueil</a></li>
            <li><a class="CTA_nav" href="<?php echo $relative_path ?>quiz/">Quiz</a></li>
            <?php if (isLogin()) : ?>
                <li><a class="CTA_nav" href="<?php echo $relative_path ?>profil/">Profil</a></li>
                <?php if (isAdmin()) : ?>
                    <li><a class="CTA_nav" href="<?php echo $relative_path ?>admin/">Admin</a></li>
                <?php endif; ?>
                <li><a class="CTA_nav" id="logoutBtn">Déconnexion</a></li>
            <?php else : ?>
                <li><a class="CTA_nav" id="loginBtn">Connexion</a></li>
                <li><a class="CTA_nav" id="registerBtn">Inscription</a></li>
            <?php endif; ?>
        </ul>
        <span id="relativePath" style="display: none;"><?php echo $relative_path ?></span>
    </nav>
</header>
<script src="<?php echo $relative_path ?>assets/js/util.js"></script>
<script src="<?php echo $relative_path ?>assets/js/header.js"></script>