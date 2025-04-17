<?php
$relative_path = "../";
require_once $relative_path . "util/functions.php";
if (!isLogin()) {
    header("Location: " . $relative_path);
}
$user = json_decode(getSelfUser());
$userGroupName = $user->success->groupe_name ?? 'Aucun'; // Récupère le nom du groupe
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Les Questionnaire</title>
    <link rel="stylesheet" href="<?php echo $relative_path ?>assets/css/base.css">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <style>
        .graph-modal {
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.6);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .graph-modal-content {
            background-color: var(--backgroundColorLight);
            color: var(--onBackground);
            padding: 20px;
            border-radius: var(--outRadius);
            width: 80%;
            max-width: 800px;
            border: 1px solid var(--primary);
        }

        .graph-modal-close {
            color: var(--onBackground);
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        #progressionChart {
            color: black;
        }
    </style>
</head>

<body>
    <?php include_once($relative_path . "util/components/header.php") ?>
    <main id="quiz">
        <h2>Liste des questionnaires</h2>
        <select id="theme-select">
            <option value="all">Tous</option>
        </select>
        <div class="listeQuestionnaire">
            <!-- Les questionnaires seront chargés ici -->
        </div>
    </main>
    <?php include_once($relative_path . "util/components/footer.php") ?>
    <script>
        // Passe le nom du groupe au script JS
        const userGroupName = '<?php echo $userGroupName; ?>';
    </script>
    <script src="<?php echo $relative_path ?>assets/js/viewQuiz.js"></script>
</body>

</html>