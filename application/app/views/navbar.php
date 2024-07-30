<nav class="navbar">
    <div class="logo">Camagru</div>
    <div class="nav-links">
        <a href="index.php">Accueil</a>
        <?php if (isset($_SESSION['username'])): ?>
            <a href="?controller=user&action=setting">Compte</a>
            <a href="?controller=user&action=logout">Logout</a>
        <?php else: ?>
            <a href="?controller=user">Compte</a>
        <?php endif; ?>
    </div>
</nav>