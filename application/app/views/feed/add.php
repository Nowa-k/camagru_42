<?php include 'app/views/cache.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Camagru - Feed</title>
    <link rel="stylesheet" href="/app/public/style/style.css">
    <link rel="stylesheet" href="/app/public/style/cam.css">
    <link rel="stylesheet" href="/app/public/style/pop.css">
</head>
<body>
    <?php include 'app/views/navbar.php'; ?>
    <div class="content">
        <h1>Cree ton cama</h1>
        <h2>Image Superposables</h2>
        <div class="camera">
            <video id="video" autoplay></video>
            <canvas id="canvas"></canvas>
            <div class="ctn-btn">
                <button class='btn-action' id="start-camera">Start Camera</button>
                <button class='btn-action disabled' id="click-photo">Prendre une photo</button>  
            </div> 
        </div>
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
        <form id="imageForm" method="post" action="index.php?controller=feed&action=add" enctype="multipart/form-data">
            <label for="userImage">Télécharger une image:</label>
            <input class='btn-action' type="file" id="userImage" name="userImage" accept="image/png, image/jpg, image/jpeg" required>

            <h3>Sélectionner une image superposable :</h3>
            <input type="radio" id="overlay1" name="overlayImage" value="overlay/cat.png" required>
            <label for="overlay1"><img class="overlayImage" src="overlay/cat.png" alt="Overlay 1"></label>

            <input type="radio"  id="overlay2" name="overlayImage" value="overlay/ange.png" required>
            <label for="overlay2"><img class="overlayImage" src="overlay/ange.png" alt="Overlay 2"></label>
            
            <input type="radio"  id="overlay3" name="overlayImage" value="overlay/demon.png" required>
            <label for="overlay3"><img class="overlayImage" src="overlay/demon.png" alt="Overlay 3"></label>

            <input type="radio"  id="overlay4" name="overlayImage" value="overlay/lunette.png" required>
            <label for="overlay4"><img class="overlayImage" src="overlay/lunette.png" alt="Overlay 4"></label>

            <input type="radio"  id="overlay5" name="overlayImage" value="overlay/bob.png" required>
            <label for="overlay5"><img class="overlayImage" src="overlay/bob.png" alt="Overlay 5"></label>

            <input type="radio"  id="overlay6" name="overlayImage" value="overlay/ovnis.png" required>
            <label for="overlay6"><img class="overlayImage" src="overlay/ovnis.png" alt="Overlay 6"></label>

            <input type="radio"  id="overlay7" name="overlayImage" value="overlay/france.png" required>
            <label for="overlay7"><img class="overlayImage" src="overlay/france.png" alt="Overlay 7"></label>

            <p class="center"><button type="submit" id="submitButton" class='btn-action disabled' disabled>Créer l'image</button></p>
        </form>
        <p class="center"><button id="create" class='btn-action disabled' disabled>Creer montage</button></p>
        <div class="feedUser">
        <?php foreach ($feed as $picture): ?>
        <div class="picture-item">
            <img src="<?php echo $picture['filepath']; ?>" class="picture">
        </div>
        <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
<script src="/app/script/cam.js"></script>
<script src="/app/script/pop.js"></script>
<script>
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script>
