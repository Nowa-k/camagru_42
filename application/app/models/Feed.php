<?php
class Feed {
    public static function getAll($firstResult, $resultsPerPage) {
        $db = getDBConnection();
        $stmt = $db->prepare('SELECT feed.*, users.username
                      FROM feed 
                      JOIN users ON feed.userid = users.id
                      ORDER BY feed.created_at DESC 
                      LIMIT :offset, :limit');

        $stmt->bindValue(':limit', $resultsPerPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $firstResult, PDO::PARAM_INT);
        $stmt->execute();
        $feeds = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($feeds as &$feed) {
            $stmtComments = $db->prepare('SELECT comments.comment, users.username 
                FROM comments 
                JOIN users ON comments.iduser = users.id 
                WHERE comments.idfile = ? 
                ORDER BY comments.created_at ASC');
            $stmtComments->execute([$feed['id']]);
            $feed['cmts'] = $stmtComments->fetchAll(PDO::FETCH_ASSOC);
    
            $stmtLikes = $db->prepare('SELECT likes.*, users.username 
                FROM likes 
                JOIN users ON likes.iduser = users.id 
                WHERE likes.idfile = ?');
            $stmtLikes->execute([$feed['id']]);
            $feed['liks'] = $stmtLikes->fetchAll(PDO::FETCH_ASSOC);
        }
        return $feeds;
    }

    public static function getTotalFeedCount() {
        $db = getDBConnection();
        $stmt = $db->query('SELECT COUNT(*) FROM feed');
        return $stmt->fetchColumn();
    }
    

    public static function getById($id) {
        $db = getDBConnection();
        $stmt = $db->prepare('SELECT feed.*, users.username
                    FROM feed
                    JOIN users ON feed.userid = users.id
                    WHERE feed.id = ?');
        $stmt->execute([$id]);
        $feed = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$feed) {
            return ;
        }
        $stmtComments = $db->prepare('SELECT comments.comment, users.username 
            FROM comments 
            JOIN users ON comments.iduser = users.id 
            WHERE comments.idfile = ? 
            ORDER BY comments.created_at ASC');
        $stmtComments->execute([$feed['id']]);
        $feed['cmts'] = $stmtComments->fetchAll(PDO::FETCH_ASSOC);

        $stmtLikes = $db->prepare('SELECT likes.*, users.username 
            FROM likes 
            JOIN users ON likes.iduser = users.id 
            WHERE likes.idfile = ?');
        $stmtLikes->execute([$feed['id']]);
        $feed['liks'] = $stmtLikes->fetchAll(PDO::FETCH_ASSOC);
        return $feed;
    }

    public static function getUserFeed($userid) {
        $db = getDBConnection();
        $stmt = $db->prepare('SELECT * FROM feed WHERE userid = ? ORDER BY created_at DESC');
        $stmt->execute([$userid]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    

    public static function add($overlayImage) {
        if ($_FILES['userImage']['size'] > 10485760) {
            return "Le fichier est trop volumineux.";
        }
        $targetDir = "uploads/";

        $file_extension = pathinfo(basename($_FILES["userImage"]["name"]), PATHINFO_EXTENSION);
        $extensions = Array('jpg','png');

        if (!in_array($file_extension, $extensions)){
            return "Le fichier envoyé n'est pas au bon format.<br> Format demandé: png, jpg, jpeg";
        }
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $userImage = $targetDir . basename($_FILES["userImage"]["name"]);
        if (!move_uploaded_file($_FILES["userImage"]["tmp_name"], $userImage)) {
            return "Erreur lors du téléchargement de l'image utilisateur.";
        }

        $overlay = imagecreatefrompng($overlayImage);
        $userImg = imagecreatefromstring(file_get_contents($userImage));

        $widthOverlay = imagesx($overlay);
        $heightOverlay = imagesy($overlay);

        $w = imagesx($userImg);
        $h = imagesy($userImg);

        $posMidX = ceil($w / 2 - $widthOverlay / 2);
        $posBtmY = ceil(($h / 4) * 3 - $heightOverlay / 3);
        $posMidY = ceil($h / 2 - $heightOverlay / 2);
        $posTopY = ceil($h / 4 - $heightOverlay / 2);

        $assets = [
            ["overlay/ange.png", $posMidX, $posMidY],
            ["overlay/bob.png", $posMidX, 0],
            ["overlay/cat.png", 0, $posMidY],
            ["overlay/demon.png", $posMidX, $posMidY],
            ["overlay/france.png", $posMidX, $posBtmY],
            ["overlay/lunette.png", $posMidX, $posTopY],
            ["overlay/ovnis.png", $posMidX, 0],
        ];

        foreach ($assets as $asset) {
            if ($asset[0] == $overlayImage) {
                $canva = $asset;
            } 
        }

        imagecopy($userImg, $overlay, $canva[1], $canva[2], 0, 0, $widthOverlay, $heightOverlay);
        $filename = $targetDir . uniqid() . ".png";
        imagepng($userImg, $filename);

        imagedestroy($overlay);
        imagedestroy($userImg);

        $db = getDBConnection();
        $stmt = $db->prepare("INSERT INTO feed (filepath, userid) VALUES (?, ?)");
        $stmt->execute([$filename, $_SESSION['id']]);
        return "Succès: L'image à bien été crée.";
    }

    public static function create($canvasData, $overlayPath) {
        $targetDir = "uploads/";
        list($type, $canvasData) = explode(';', $canvasData);
        list(, $canvasData) = explode(',', $canvasData);
        $canvasData = base64_decode($canvasData);

        $canvasImage = imagecreatefromstring($canvasData);
        if ($canvasImage === false) {
            die('Erreur lors de la création de l\'image à partir des données du canvas.');
        }

        $overlayImage = imagecreatefrompng($overlayPath);
        if ($overlayImage === false) {
            die('Erreur lors du chargement de l\'image overlay.');
        }

        $canvasWidth = imagesx($canvasImage);
        $canvasHeight = imagesy($canvasImage);
        $overlayWidth = imagesx($overlayImage);
        $overlayHeight = imagesy($overlayImage);     

        $posMidX = ceil($canvasWidth / 2 - $overlayWidth / 2);
        $posBtmY = ceil(($canvasHeight / 4) * 3 - $overlayHeight / 3);
        $posMidY = ceil($canvasHeight / 2 - $overlayHeight / 2);
        $posTopY = ceil($canvasHeight / 4 - $overlayHeight / 2);
        
        $assets = [
            ["overlay/ange.png", $posMidX, $posMidY],
            ["overlay/bob.png", $posMidX, 0],
            ["overlay/cat.png", 0, $posMidY],
            ["overlay/demon.png", $posMidX, $posMidY],
            ["overlay/france.png", $posMidX, $posBtmY],
            ["overlay/lunette.png", $posMidX, $posTopY],
            ["overlay/ovnis.png", $posMidX, 0],
            ];
            
        foreach ($assets as $asset) {
            if ($asset[0] == $overlayPath) {
                $canva = $asset;
            } 
        }

        imagecopy($canvasImage, $overlayImage, $canva[1], $canva[2], 0, 0, $overlayWidth, $overlayHeight);

        $filename = $targetDir . uniqid() . ".png";
        imagepng($canvasImage, $filename);

        imagedestroy($canvasImage);
        imagedestroy($overlayImage);
        $db = getDBConnection();
        $stmt = $db->prepare("INSERT INTO feed (filepath, userid) VALUES (?, ?)");
        $stmt->execute([$filename, $_SESSION['id']]);

        return ($overlayWidth);
    }

    public static function del($idsession, $idfeed) {
        $feed = self::getById($idfeed);
        if (!$feed) {
            return ;
        }
        if ($feed['userid'] != $idsession) {
            return;
        }
        $db = getDBConnection();
        $stmt = $db->prepare('DELETE FROM feed WHERE id = ?');
        $stmt->execute([$idfeed]);
    }

    public static function comment($idfile, $comment, $user) {
        $db = getDBConnection();
        $user = intval($user);
        $stmt = $db->prepare("INSERT INTO comments (idfile, comment, iduser) VALUES (?, ?, ?)");
        $result = $stmt->execute([$idfile, $comment, $user]);
        if (!$result || $stmt->rowCount() == 0) {
            return "Erreur: Impossible d'ajouter le commentaire.";   
        }
        $stmt = $db->prepare('UPDATE feed SET comments = comments + 1 WHERE id = ?');
        $result = $stmt->execute([$idfile]);
        if (!$result || $stmt->rowCount() == 0) {
            return "Erreur: Problème lors de l'action";
        }
        $stmt = $db->prepare('SELECT userid FROM feed WHERE id = ?');
        $stmt->execute([$idfile]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = $db->prepare('SELECT email, notif FROM users WHERE id = ?');
        $stmt->execute([$user['userid']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user['notif'] == '0') {
            return ;
        }
        $subject = "Vous avez un nouveau commentaire";
        $message = "
        <html>
        <head>
            <title>Nouveau commentaire</title>
        </head>
        <body>
            <p>Quelqu'un a commenté une de vos publications.</p>
            <a href='http://127.0.0.1:8080/index.php?controller=feed&action=zoom&code=$idfile' target='_blank' style='background-color: #4CAF50; border: none; color: white; padding: 15px 32px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px; margin: 4px 2px; cursor: pointer; border-radius: 8px;'>Voir la publication</a>
            </body>
        </html>";

        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: ". getenv('MSMTPRC_MAIL') . "\r\n";
        $headers .= "Reply-To: ". getenv('MSMTPRC_MAIL') . "\r\n";

        if (mail('a.ferrand69@gmail.com', $subject, $message, $headers)) {
            return "E-mail envoyé avec succès.";
        } else {
            return "Erreur lors de l'envoi de l'e-mail.";
        }
    }

    public static function like($user, $file) {
        $db = getDBConnection();
        $user = intval($user);
        $file = intval($file);

        $stmt = $db->prepare('SELECT * FROM likes WHERE idfile = ? AND iduser = ?');
        $stmt->execute([$file, $user]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $stmt = $db->prepare("DELETE FROM likes WHERE idfile = ? AND iduser = ?");
            $stmt->execute([$file, $user]);
            $stmt = $db->prepare('UPDATE feed SET likes = likes - 1 WHERE id = ?');
            $stmt->execute([$file]);
        } else {
            $stmt = $db->prepare("INSERT INTO likes (idfile, iduser) VALUES (?, ?)");
            $stmt->execute([$file, $user]);
            $stmt = $db->prepare('UPDATE feed SET likes = likes + 1 WHERE id = ?');
            $stmt->execute([$file]);
        }
    }
}