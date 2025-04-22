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
</head>

<body>
    <?php include_once($relative_path . "util/components/header.php") ?>
    <main id="adminPage">
        <div class="chartContainer">
            <div class="chart1">
                <span>Nombre de réponses les 7 derniers jours de <span id="quizName">tout les quiz</span> :</span>
                <label>
                    Séléctionne un quiz:
                    <select id="quizSelect">
                        <option value="0">Choisissez un quiz</option>
                    </select>
                </label>
                <div id="chartAnwser">

                </div>
            </div>
            <div class="chart2">
                <span>Réponses d'un utilisateurs les 7 derniers jours :</span>
                <label>
                    Séléctionne un utilisateur:
                    <select id="userSelect">
                        <option value="0">Choisissez un utilisateur</option>
                    </select>
                </label>
                <div id="chartScore">

                </div>
            </div>
        </div>

        <div class="userList">
            <table>
                <thead>
                    <tr>
                        <th>Nom d'utilisateur</th>
                        <th>Email</th>
                        <th>Groupe</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="userList">
                    <!-- Les utilisateurs seront ajoutés ici par JavaScript -->
                </tbody>
            </table>
            <button id="addUserBtn" class="actionButton">Ajouter un utilisateur</button>
        </div>

        <div class="groupList">
            <table>
                <thead>
                    <tr>
                        <th>Nom du groupe</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="groupList">
                    <!-- Les groupes seront ajoutés ici par JavaScript -->
                </tbody>
            </table>
            <div id="addGroup">
                <label>
                    Nom du groupe:
                    <input type="text" id="groupName" placeholder="Nom du groupe">
                </label>
                <button id="addGroupBtn" class="actionButton">Ajouter un groupe</button>
            </div>
        </div>
    </main>
    <?php include_once($relative_path . "util/components/footer.php") ?>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="<?php echo $relative_path ?>assets/js/admin.js "></script>
</body>

</html>