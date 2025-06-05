<?php
require_once $relative_path . "util/functions.php";

?>


<header>
    <nav class="modern-nav">
        <div class="nav-brand">
            <div class="brand-icon">🧠</div>
            <h1>TheQuiz</h1>
        </div>

        <ul class="nav-menu">
            <li><a class="nav-link" href="<?php echo $relative_path ?>index.php" data-text="Accueil">
                    <span class="nav-icon">🏠</span>
                    <span class="nav-text">Accueil</span>
                </a></li>
            <li><a class="nav-link" href="<?php echo $relative_path ?>quiz/" data-text="Quiz">
                    <span class="nav-icon">🎯</span>
                    <span class="nav-text">Quiz</span>
                </a></li>
            <?php if (isLogin()) : ?>
                <li><a class="nav-link" href="<?php echo $relative_path ?>profil/" data-text="Profil">
                        <span class="nav-icon">👤</span>
                        <span class="nav-text">Profil</span>
                    </a></li>
                <?php if (isAdmin()) : ?>
                    <li><a class="nav-link" href="<?php echo $relative_path ?>admin/" data-text="Admin">
                            <span class="nav-icon">⚙️</span>
                            <span class="nav-text">Admin</span>
                        </a></li>
                <?php endif; ?>
            <?php endif; ?>
        </ul>

        <div class="nav-actions">
            <?php if (isLogin()) : ?>
                <div class="user-menu">
                    <button class="user-avatar" id="userMenuBtn">
                        <span class="avatar-icon">👤</span>
                        <span class="user-name"><?php echo $_SESSION['username'] ?? 'User'; ?></span>
                        <span class="dropdown-arrow">▼</span>
                    </button>
                    <div class="user-dropdown" id="userDropdown">
                        <a href="<?php echo $relative_path ?>profil/" class="dropdown-item">
                            <span class="dropdown-icon">👤</span>
                            Mon Profil
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item logout-btn" id="logoutBtn">
                            <span class="dropdown-icon">🚪</span>
                            Déconnexion
                        </a>
                    </div>
                </div>
            <?php else : ?>
                <button class="auth-btn login-btn" id="loginBtn">
                    <span class="btn-icon">🔑</span>
                    <span>Connexion</span>
                </button>
                <button class="auth-btn register-btn" id="registerBtn">
                    <span class="btn-icon">📝</span>
                    <span>Inscription</span>
                </button>
            <?php endif; ?>
        </div>

        <!-- Mobile menu button -->
        <button class="mobile-menu-btn" id="mobileMenuBtn">
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
        </button>

        <span id="relativePath" style="display: none;"><?php echo $relative_path ?></span>
    </nav>

    <!-- Mobile menu overlay -->
    <div class="mobile-menu-overlay" id="mobileMenuOverlay">
        <div class="mobile-menu">
            <div class="mobile-menu-header">
                <div class="nav-brand">
                    <div class="brand-icon">🧠</div>
                    <h1>TheQuiz</h1>
                </div>
                <button class="mobile-close-btn" id="mobileCloseBtn">✕</button>
            </div>

            <div class="mobile-menu-content">
                <a class="mobile-nav-link" href="<?php echo $relative_path ?>index.php">
                    <span class="nav-icon">🏠</span>
                    <span>Accueil</span>
                </a>
                <a class="mobile-nav-link" href="<?php echo $relative_path ?>quiz/">
                    <span class="nav-icon">🎯</span>
                    <span>Quiz</span>
                </a>
                <?php if (isLogin()) : ?>
                    <a class="mobile-nav-link" href="<?php echo $relative_path ?>profil/">
                        <span class="nav-icon">👤</span>
                        <span>Profil</span>
                    </a>
                    <?php if (isAdmin()) : ?>
                        <a class="mobile-nav-link" href="<?php echo $relative_path ?>admin/">
                            <span class="nav-icon">⚙️</span>
                            <span>Admin</span>
                        </a>
                    <?php endif; ?>

                    <div class="mobile-menu-divider"></div>
                    <button class="mobile-nav-link logout-btn" id="mobileLogoutBtn">
                        <span class="nav-icon">🚪</span>
                        <span>Déconnexion</span>
                    </button>
                <?php else : ?>
                    <div class="mobile-menu-divider"></div>
                    <button class="mobile-nav-link login-btn" id="mobileLoginBtn">
                        <span class="nav-icon">🔑</span>
                        <span>Connexion</span>
                    </button>
                    <button class="mobile-nav-link register-btn" id="mobileRegisterBtn">
                        <span class="nav-icon">📝</span>
                        <span>Inscription</span>
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>
<script src="<?php echo $relative_path ?>assets/js/util.js"></script>
<script src="<?php echo $relative_path ?>assets/js/header.js"></script>