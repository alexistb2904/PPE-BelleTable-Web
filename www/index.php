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

<body> <?php include_once($relative_path . "util/components/header.php") ?>

    <!-- Hero Section -->
    <main id="home">
        <div class="hero-section">
            <div class="hero-content">
                <div class="hero-badge">
                    <span class="badge-icon">🧠</span>
                    <span>Nouveau: Classement en temps réel</span>
                </div>
                <h1 class="hero-title">
                    Testez vos <span class="gradient-text">connaissances</span>
                    <br>avec TheQuiz
                </h1>
                <p class="hero-description">
                    Découvrez une nouvelle façon d'apprendre et de vous amuser avec nos quiz interactifs.
                    Défis personnalisés, scores en temps réel et compétition entre amis.
                </p>

                <div class="hero-actions">
                    <?php if (isLogin()) : ?>
                        <a href="<?php echo $relative_path ?>quiz" class="CTA_btn primary-btn">
                            <span class="btn-icon">🚀</span>
                            Commencer maintenant
                        </a>
                        <a href="<?php echo $relative_path ?>profil" class="CTA_btn secondary-btn">
                            <span class="btn-icon">👤</span>
                            Mon profil
                        </a>
                    <?php else : ?>
                        <a id="loginHome" class="CTA_btn primary-btn">
                            <span class="btn-icon">🔑</span>
                            Se connecter
                        </a>
                        <a id="registerHome" class="CTA_btn secondary-btn">
                            <span class="btn-icon">📝</span>
                            S'inscrire
                        </a>
                    <?php endif ?>
                </div>
            </div>

            <div class="hero-visual">
                <div class="quiz-card-demo">
                    <div class="card-header">
                        <div class="card-dots">
                            <span class="dot red"></span>
                            <span class="dot yellow"></span>
                            <span class="dot green"></span>
                        </div>
                        <span class="card-title">Quiz Géographie</span>
                    </div>
                    <div class="card-content">
                        <h3>Quelle est la capitale de la France ?</h3>
                        <div class="quiz-options">
                            <div class="option correct">Paris</div>
                            <div class="option">Lyon</div>
                            <div class="option">Marseille</div>
                            <div class="option">Nice</div>
                        </div>
                        <div class="quiz-progress">
                            <div class="progress-bar">
                                <div class="progress-fill"></div>
                            </div>
                            <span class="progress-text">Question 3/10</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <section class="features-section">
            <div class="features-header">
                <h2>Pourquoi choisir TheQuiz ?</h2>
                <p>Une expérience de quiz unique et engageante</p>
            </div>

            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">🏫</div>
                    <h3>Gestions de Groupe</h3>
                    <p>Gestions de groupe pour les administrateur</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">⚡</div>
                    <h3>Temps Réel</h3>
                    <p>Résultats instantanés et classements en direct</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">🏆</div>
                    <h3>Compétition</h3>
                    <p>Défiez vos amis et grimpez dans les classements</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">📊</div>
                    <h3>Statistiques</h3>
                    <p>Suivez vos progrès avec des analyses détaillées</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">🎨</div>
                    <h3>Thèmes Variés</h3>
                    <p>Culture, science, sport, histoire et bien plus encore</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">📱</div>
                    <h3>Multi-plateforme</h3>
                    <p>Jouez sur ordinateur, tablette ou smartphone</p>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="stats-section">
            <div class="stats-container">
                <div class="stat-item">
                    <div class="stat-number">1000+</div>
                    <div class="stat-label">Questions</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">500+</div>
                    <div class="stat-label">Utilisateurs</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">50+</div>
                    <div class="stat-label">Quiz</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">10k+</div>
                    <div class="stat-label">Parties jouées</div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="cta-section">
            <div class="cta-content">
                <h2>Prêt à défier votre cerveau ?</h2>
                <p>Rejoignez des milliers d'utilisateurs qui s'amusent tout en apprenant</p>
                <?php if (!isLogin()) : ?>
                    <a id="registerCTA" class="CTA_btn primary-btn large-btn">
                        <span class="btn-icon">🎮</span>
                        Commencer gratuitement
                    </a>
                <?php endif ?>
            </div>
        </section>
    </main>
    <?php include_once($relative_path . "util/components/footer.php") ?> <script>
        const loginBtnA = document.querySelector('#loginHome');
        const registerBtnA = document.querySelector('#registerHome');
        const registerCTA = document.querySelector('#registerCTA');

        loginBtnA?.addEventListener('click', () => {
            createModal('login');
        });

        registerBtnA?.addEventListener('click', () => {
            createModal('register');
        });

        registerCTA?.addEventListener('click', () => {
            createModal('register');
        });

        // Animation pour les statistiques
        const observerOptions = {
            threshold: 0.7,
            rootMargin: '0px 0px -100px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const statNumbers = entry.target.querySelectorAll('.stat-number');
                    statNumbers.forEach(stat => {
                        const finalValue = stat.textContent;
                        const numericValue = parseInt(finalValue.replace(/\D/g, ''));
                        const suffix = finalValue.replace(/[\d,]/g, '');

                        let currentValue = 0;
                        const increment = numericValue / 50;
                        const timer = setInterval(() => {
                            currentValue += increment;
                            if (currentValue >= numericValue) {
                                currentValue = numericValue;
                                clearInterval(timer);
                            }
                            stat.textContent = Math.floor(currentValue).toLocaleString() + suffix;
                        }, 30);
                    });
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        const statsSection = document.querySelector('.stats-section');
        if (statsSection) {
            observer.observe(statsSection);
        }

        // Animation pour les cartes de fonctionnalités
        const featureCards = document.querySelectorAll('.feature-card');
        featureCards.forEach((card, index) => {
            card.style.animationDelay = `${index * 0.1}s`;
        });
    </script>
</body>

</html>