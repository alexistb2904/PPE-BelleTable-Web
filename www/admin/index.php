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
        Nombre de réponses les 7 derniers jours :
        <div id="chartAnwser">

        </div>
    </main>
    <?php include_once($relative_path . "util/components/footer.php") ?>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="<?php echo $relative_path ?>assets/js/admin.js "></script>
</body>

</html>