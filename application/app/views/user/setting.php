<?php include 'app/views/cache.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Camagru - Compte</title>
    <link rel="stylesheet" href="/app/public/style/style.css">
    <link rel="stylesheet" href="/app/public/style/form.css">
    <link rel="stylesheet" href="/app/public/style/pop.css">
</head>
<body>
    <?php include 'app/views/navbar.php';
    if (isset($mess) && !empty($mess)): ?>
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
        <h1>Paramètre</h1>
        <h2 class="page-title">Changer les paramètre du compte</h2>
        <form class="form-group" id="userForm" action="index.php?controller=user&action=setting" method="post">
            <div class="form-line">
                <label>Username</label>
                <input type="text" id="username" name="username" value=<?php echo $user['username'];?>>
            </div>
            <div class="form-line">
                <label>Email</label>
                <input type="email" id="mail" name="mail" value=<?php echo $user['email'];?>>
            </div>
            <div class="form-line">
                <label>Mot de passe</label>
                <input type="password" id="newpwd" name="newpwd">
            </div>
            <p>Pour valider les changements mettre le mot de passe actuel</p>
            <div class="form-line">
                <label>Mot de passe actuel</label>
                <input type="password" id="pwd" name="pwd" required="required">
            </div>
            <input class="validate" type="submit" value="Valider">
        </form> 
        <h2 class="page-title">Accepter de recevoir les notifications par mail</h2>
        <form class="form-group" action="index.php?controller=user&action=setting" method="post">
        <div class="form-line">
            <input type="hidden" name="notification" value="0">
            <input type="checkbox" id="notification" name="notification" value="1"
                <?php echo ($user['notif'] == '1') ? 'checked="checked"' : ''; ?>>
        </div>
        <input class="validate" type="submit" value="Valider" />
        </form>
    </div>
</body>
</html>
<script src="/app/script/pop.js"></script>
<script src="/app/script/pass.js"></script>
