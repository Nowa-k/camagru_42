<?php include 'app/views/cache.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Camagru - Verifier</title>
    <link rel="stylesheet" href="/app/public/style/style.css">
    <link rel="stylesheet" href="/app/public/style/form.css">
    <link rel="stylesheet" href="/app/public/style/pop.css">
</head>
<body>
    <?php include 'app/views/navbar.php'; ?>
    <?php if (isset($mess) && !empty($mess)): ?>
    <div id="popup" class="popup">
        <div class="popup-content">
            <span class="close-btn" onclick="closePopup()">&times;</span>
            <?php if (isset($mess) && !empty($mess)): ?>
                <p><?php echo $mess; ?></p>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
    <div class="content">
        <h1>Vérifier votre email</h1>
        <?php if (isset($_SESSION['valide']) && $_SESSION['valide'] == '0'): ?>
            <form method="post" action="?controller=user&action=verify">
                <button type="submit">Envoyer un mail</button>
            </form>
        <?php elseif (isset($_SESSION['valide']) && $_SESSION['valide'] == '1'): ?>
            <p>Votre email est bien valide.</p>
        <?php endif; ?>
    </div>
</body>
</html>
<script src="/app/script/pop.js"></script>