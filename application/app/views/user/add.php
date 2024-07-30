<?php include 'app/views/cache.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Camagru - Inscription</title>
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
        <h1>S'inscrire</h1>
        <form class="form-group" id='userForm' method="POST" action="index.php?controller=user&action=add">
            <div class="form-line">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" >
            </div>
            <div class="form-line">
                <label for="mail">Email</label>
                <input type="email" id="mail" name="mail" >
            </div>
            <div class="form-line">
                <label for="pwd">Password</label>
                <input type="password" id="pwd" name="pwd" >
            </div>
            <button type="submit">Ajouter</button>
        </form>
        <a href="index.php?controller=user&action=index">Annuler</a>
    </div>
</body>
</html>
<script src="/app/script/pop.js"></script>
<script src="/app/script/pass.js"></script>