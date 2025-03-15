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
    <title>Les Questionnaire</title>
    <link rel="stylesheet" href="<?php echo $relative_path ?>assets/css/base.css">
</head>

<body>
    <?php include_once($relative_path . "util/components/header.php") ?>
    <main id="quiz">
        <h2>Liste des questionnaires</h2>
        <select id="theme-select">
            <option value="all">Tous</option>
        </select>
        <div class="listeQuestionnaire">

        </div>
    </main>
    <?php include_once($relative_path . "util/components/footer.php") ?>
    <script src="<?php echo $relative_path ?>assets/js/viewQuiz.js"></script>
</body>

</html>