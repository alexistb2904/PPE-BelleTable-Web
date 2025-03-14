<?php
$relative_path = '../../';

require_once $relative_path . 'util/functions.php';


if (!isLogin()) {
    header('Location: ' . $relative_path);
    exit();
}



if ($_GET['id']) {
    $questions = getQuestionForQuestionnaire(htmlspecialchars($_GET['id']));
    $previousScores = getScoreForQuestionnaire(htmlspecialchars($_GET['id']));
    $types = getAllTypeLibelle();
} else {
    header('Location: ' . $relative_path);
    exit();
}

function getLibelleType($id, $types)
{
    foreach ($types->success as $typeGet) {
        if ($typeGet->id_type == $id) {
            return $typeGet->nom;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Quiz n°<?php echo $_GET['id'] ?></title>
    <link rel="stylesheet" href="<?php echo $relative_path ?>assets/css/base.css" />
    <link rel="stylesheet" href="<?php echo $relative_path ?>assets/css/styleJeu.css" />
</head>

<body>
    <main>
        <div id="app">

        </div>
    </main>
    <script>
        const questionnaireIDJSON = `<?= base64_encode($_GET['id']) ?>`;
        const userIDJSON = `<?= base64_encode($_SESSION['id']) ?>`;
        const questionJSON = `<?= base64_encode($questions) ?>`;
        const typesJSON = `<?= base64_encode($types) ?>`;
        const previousScoresJSON = `<?= base64_encode($previousScores) ?>`;
    </script>
    <script src="<?php echo $relative_path ?>assets/js/play.js"></script>
    <?php include_once($relative_path . "util/components/footer.php") ?>
</body>

</html>