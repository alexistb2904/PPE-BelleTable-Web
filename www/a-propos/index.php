<?php
$relative_path = "../";
require_once $relative_path . "util/functions.php";
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>À Propos - TheQuiz</title>
    <meta name="description" content="Découvrez TheQuiz, la plateforme de quiz interactive créée par Alexis Thierry-Bellefond.">
    <link rel="stylesheet" href="<?php echo $relative_path ?>assets/css/base.css">
    <style>
        .legal-page {
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 1rem;
            line-height: 1.6;
            color: var(--onBackground);
        }

        .legal-header {
            text-align: center;
            margin-bottom: 3rem;
            padding: 2rem 0;
            border-bottom: 2px solid var(--primary);
        }

        .legal-header h1 {
            color: var(--primary);
            margin-bottom: 1rem;
            font-size: 2.5rem;
        }

        .legal-section {
            margin-bottom: 2rem;
            padding: 1.5rem;
            background: var(--backgroundColorLight);
            border-radius: var(--outRadius);
            border-left: 4px solid var(--primary);
        }

        .legal-section h2 {
            color: var(--primary);
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }

        .legal-section h3 {
            color: var(--secondary);
            margin: 1rem 0 0.5rem 0;
            font-size: 1.2rem;
        }

        .contact-info {
            background: var(--primary);
            color: white;
            padding: 1.5rem;
            border-radius: var(--outRadius);
            margin: 2rem 0;
        }

        .contact-info h3 {
            margin-top: 0;
            color: white;
        }

        .highlight {
            background: var(--secondary);
            color: white;
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-weight: bold;
        }

        .team-card {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 2rem;
            border-radius: var(--outRadius);
            text-align: center;
            margin: 2rem 0;
        }

        .team-card h3 {
            color: white;
            margin-bottom: 1rem;
        }

        .legal-main {
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 1rem;
            line-height: 1.6;
            color: var(--onBackground);
        }

        .legal-main h1 {
            color: var(--primary);
            margin-bottom: 1rem;
            font-size: 2.5rem;
            text-align: center;
        }

        .legal-main p {
            margin: 0.5rem 0;
        }
    </style>
</head>

<body>
    <?php include_once($relative_path . "util/components/header.php") ?>

    <main class="legal-page">
        <div class="legal-header">
            <h1>À Propos de TheQuiz</h1>
            <p>Découvrez qui se cache derrière votre plateforme de quiz préférée</p>
        </div>

        <div class="legal-section">
            <h2>🎯 Notre Mission</h2>
            <p>
                <span class="highlight">TheQuiz</span> est une plateforme de quiz interactive conçue pour rendre l'apprentissage
                amusant et accessible à tous. Notre mission est de créer une expérience éducative engageante qui permet
                aux utilisateurs de tester leurs connaissances, d'apprendre de nouvelles choses et de se divertir
                en même temps.
            </p>
            <p>
                Que vous soyez étudiant, enseignant, ou simplement curieux de nature, TheQuiz vous offre une
                variété de questionnaires dans de nombreux domaines : culture générale, sciences, histoire,
                géographie, et bien plus encore !
            </p>
        </div>

        <div class="team-card">
            <h3>👨‍💻 Créateur & Développeur</h3>
            <h2>Alexis Thierry-Bellefond</h2>
            <p>
                Passionné de développement web et d'éducation, Alexis a créé TheQuiz dans le cadre de son
                BTS SIO (Services Informatiques aux Organisations) pour combiner sa passion pour la technologie
                et son désir de rendre l'apprentissage plus interactif.
            </p>
        </div>

        <div class="legal-section">
            <h2>🚀 Fonctionnalités</h2>
            <h3>Pour les utilisateurs :</h3>
            <ul>
                <li>Interface moderne et intuitive</li>
                <li>Quiz variés et régulièrement mis à jour</li>
                <li>Système de scoring et de progression</li>
                <li>Profil personnalisé avec statistiques</li>
                <li>Expérience responsive sur tous les appareils</li>
            </ul>

            <h3>Pour les administrateurs :</h3>
            <ul>
                <li>Gestion complète des utilisateurs et groupes</li>
                <li>Tableaux de bord avec analytics détaillés</li>
                <li>Système de modération avancé</li>
            </ul>
        </div>

        <div class="legal-section">
            <h2>🛡️ Sécurité & Confidentialité</h2>
            <p>
                La protection de vos données personnelles est notre priorité. TheQuiz respecte le
                <span class="highlight">Règlement Général sur la Protection des Données (RGPD)</span>
                et met en œuvre les meilleures pratiques de sécurité pour protéger vos informations.
            </p>
            <p>
                Nous collectons uniquement les données nécessaires au fonctionnement de la plateforme
                et ne les partageons jamais avec des tiers à des fins commerciales.
            </p>
        </div>

        <div class="legal-section">
            <h2>🎓 Projet Éducatif</h2>
            <p>
                TheQuiz a été développé dans le cadre du <span class="highlight">PPE (Projet Professionnel Encadré)</span>
                du BTS SIO. Ce projet démontre la maîtrise de technologies modernes telles que :
            </p>
            <ul>
                <li><strong>Backend :</strong> PHP 8, MySQL, API REST</li>
                <li><strong>Frontend :</strong> HTML5, CSS3, JavaScript (ES6+)</li>
                <li><strong>Outils :</strong> Docker, Composer, Git</li>
                <li><strong>Sécurité :</strong> Authentification sécurisée, protection CSRF</li>
            </ul>
        </div>

        <div class="contact-info">
            <h3>📞 Nous Contacter</h3>
            <p><strong>Développeur :</strong> Alexis Thierry-Bellefond</p>
            <p><strong>Email :</strong> contact@thequiz.dev</p>
            <p><strong>Établissement :</strong> BTS SIO - Promotion 2025</p>
            <p><strong>Hébergement :</strong> OVH SAS</p>
        </div>

        <div class="legal-section">
            <h2>🙏 Remerciements</h2>
            <p>
                Un grand merci à tous les utilisateurs qui testent TheQuiz et contribuent à son amélioration
                par leurs retours constructifs. Merci également aux enseignants et encadrants qui ont
                accompagné ce projet.
            </p>
            <p>
                TheQuiz est développé avec <span style="color: #BA6573;">❤️</span> et une grande attention
                aux détails pour offrir la meilleure expérience possible.
            </p>
        </div>

        <div style="text-align: center; margin: 3rem 0; padding: 2rem; background: var(--backgroundColorLight); border-radius: var(--outRadius);">
            <p style="margin: 0; color: var(--onBackground); opacity: 0.8;">
                <strong>TheQuiz</strong> - Version 1.0 - 2025 © Tous droits réservés
            </p>
        </div>
    </main>

    <?php include_once($relative_path . "util/components/footer.php") ?>
</body>

</html>