<?php
$relative_path = "../";
require_once $relative_path . "util/functions.php";

if (!isAdmin()) {
    header("Location: " . $relative_path);
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page d'administration</title>
    <link rel="stylesheet" href="<?php echo $relative_path ?>assets/css/base.css">
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/apexcharts" as="script">
</head>

<body>
    <?php include_once($relative_path . "util/components/header.php") ?> <main id="adminPage">
        <h1>Tableau de bord administrateur</h1>

        <div class="chartContainer">
            <div class="chart1">
                <h3>Analyse des réponses</h3>
                <span>Nombre de réponses les 7 derniers jours de <span id="quizName">tous les quiz</span></span>
                <label>
                    Sélectionner un quiz:
                    <select id="quizSelect">
                        <option value="all">Chargement...</option>
                    </select>
                </label>
                <div id="chartAnwser">
                    <div class="chart-loading"></div>
                </div>
            </div>
            <div class="chart2">
                <h3>Activité des utilisateurs</h3>
                <span>Participations d'un utilisateur les 7 derniers jours</span>
                <label>
                    Sélectionner un utilisateur:
                    <select id="userSelect">
                        <option value="all">Chargement...</option>
                    </select>
                </label>
                <div id="chartScore">
                    <div class="chart-loading"></div>
                </div>
            </div>
        </div>

        <div class="userList">
            <h2>Gestion des utilisateurs</h2>
            <table>
                <thead>
                    <tr>
                        <th style="width: 25%">Nom d'utilisateur</th>
                        <th style="width: 30%">Email</th>
                        <th style="width: 20%">Groupe</th>
                        <th style="width: 15%">Statut</th>
                        <th style="width: 10%">Actions</th>
                    </tr>
                </thead>
                <tbody id="userList">
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 2rem;">
                            Chargement des utilisateurs...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="groupList">
            <h2>Gestion des groupes</h2>
            <div id="addGroup">
                <label>
                    Nom du groupe:
                    <input type="text" id="groupName" placeholder="Nom du groupe" maxlength="50" required>
                </label>
                <button id="addGroupBtn" class="actionButton" type="button">Ajouter un groupe</button>
            </div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 80%">Nom du groupe</th>
                        <th style="width: 20%">Actions</th>
                    </tr>
                </thead>
                <tbody id="groupList">
                    <tr>
                        <td colspan="2" style="text-align: center; padding: 2rem;">
                            Chargement des groupes...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </main> <?php include_once($relative_path . "util/components/footer.php") ?>

    <!-- Scripts optimisés -->
    <script>
        // Configuration globale pour éviter les erreurs
        window.relativePath = '<?php echo $relative_path; ?>';
    </script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts" async></script>
    <script src="<?php echo $relative_path ?>assets/js/util.js"></script>
    <script src="<?php echo $relative_path ?>assets/js/admin.js" defer></script>
</body>

</html>