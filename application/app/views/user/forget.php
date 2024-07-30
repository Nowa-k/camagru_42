<?php include 'app/views/cache.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Camagru - Mot de passe oublié</title>
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
            <?php if (isset($mess['username']) && !empty($mess['username'])): ?>
                <p><?php echo $mess['username']; ?></p>
            <?php endif; ?>
            <?php if (isset($mess['mail']) && !empty($mess['mail'])): ?>
                <p><?php echo $mess['mail']; ?></p>
            <?php endif; ?>
            <?php if (isset($mess['pwd']) && !empty($mess['pwd'])): ?>
                <p><?php echo $mess['pwd']; ?></p>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
    <div class="content">
        <?php if (isset($user)): ?>
        <h1>Réinitialiser votre mot de passe</h1>
        <form class="form-group" method="POST" action="index.php?controller=user&action=forget">
            <div class="form-line">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value=<?php echo $user['username'] ?>>
            </div>
            <div class="form-line">
                <label for="code">Code</label>
                <input type="text" id="code" name="code">
            </div>
            <div class="form-line">
                <label for="pwd">Mot de passe</label>
                <input type="password" id="pwd" name="pwd">
            </div>
            <button type="submit">Valider</button>
        </form>
    <?php else: ?>
        <h1>Mot de passe oublié</h1>
        <form method="post" action="?controller=user&action=forget">
            <div class="form-line">
                <label for="mail">Email</label>
                <input type="email" id="mail" name="mail">
            </div>
            <button type="submit">Envoyer un mail</button>
        </form>
    <?php endif; ?>
    </div>
</body>
</html>
<script src="/app/script/pop.js"></script>
