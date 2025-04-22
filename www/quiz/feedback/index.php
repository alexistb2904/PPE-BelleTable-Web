<?php
$relative_path = '../../';

require_once $relative_path . 'util/functions.php';


if (!isLogin()) {
    header('Location: ' . $relative_path);
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback</title>
    <link rel="stylesheet" href="<?php echo $relative_path ?>assets/css/base.css">
</head>

<body>
    <?php include_once($relative_path . 'util/components/header.php'); ?>
    <main id="feedback">
        <h1>Donnez votre avis</h1>
        <p>Comment évaluez-vous ce questionnaire ?</p>
        <form id="feedbackForm">
            <div class="stars">
                <input type="radio" id="star5" name="rating" value="5">
                <label for="star5">&#9733;</label>
                <input type="radio" id="star4" name="rating" value="4">
                <label for="star4">&#9733;</label>
                <input type="radio" id="star3" name="rating" value="3">
                <label for="star3">&#9733;</label>
                <input type="radio" id="star2" name="rating" value="2">
                <label for="star2">&#9733;</label>
                <input type="radio" id="star1" name="rating" value="1">
                <label for="star1">&#9733;</label>
            </div>
            <textarea name="comment" id="comment" rows="4" placeholder="Laissez un commentaire (facultatif)"></textarea>
            <div class="feedback-message" id="feedbackMessage"></div>
            <button type="submit" style="margin-top: 20px;">Envoyer</button>
        </form>

    </main>
    <?php include_once($relative_path . "util/components/footer.php") ?>
    <script src="<?php echo $relative_path ?>assets/js/feedback.js"></script>
</body>

</html>

<?php
include_once '../../util/components/footer.php';
?>