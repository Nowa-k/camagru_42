<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

use League\OAuth2\Client\Provider\Google;

require 'vendor/autoload.php';

class User {
    public static function parsePwd($pwd) {
        if (empty($pwd)) {
            return false;
        }
        $reg = '/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[\da-zA-Z]{8,20}$/';
        if (preg_match($reg, $pwd) > 0) {
            return true;
        }
        return false;
    }

    public static function parseEmail($email) {
        $email = trim($email);
        if (empty($email)) {
            return false;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        return true;
    }

    public static function getAll() {
        $db = getDBConnection();
        $stmt = $db->query('SELECT * FROM users');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id) {
        $db = getDBConnection();
        $stmt = $db->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getByUuid($uuid) {
        $db = getDBConnection();
        $stmt = $db->prepare('SELECT * FROM users WHERE uuid = ?');
        $stmt->execute([$uuid]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getByMail($mail) {
        $db = getDBConnection();
        $stmt = $db->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$mail]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getByUsername($username, $code) {
        $db = getDBConnection();
        $stmt = $db->prepare('SELECT * FROM users WHERE username = ? AND uuid = ? ');
        $stmt->execute([$username, $code]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function add($username, $mail, $pwd) {
        $db = getDBConnection();
        if (!self::parsePwd($pwd)) {
            return "Mot de passe invalide";
        }
        if (!self::parseEmail($mail)) {
            return "Email invalide";
        }
        $pwdHashed = password_hash($pwd, PASSWORD_DEFAULT);
        $uuid = uniqid();
        try {
            $stmt = $db->prepare('INSERT INTO users (uuid, username, email, pwd) VALUES (?, ?, ?, ?)');
            $result = $stmt->execute([$uuid, $username, $mail, $pwdHashed]);
            if ($result) {
                return "Votre inscription est un succès.";
            } else {
                return "Impossible de créer";
            }
        } catch (PDOException $e) {
        }
        return "Error";
    }

    public static function login($username, $pwd) {
        $db = getDBConnection();
        $stmt = $db->prepare('SELECT * FROM users WHERE username = ? LIMIT 1');
        $stmt->execute([$username]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if (!$row) {
            return FALSE;
        }
    
        if (password_verify($pwd, $row['pwd'])) {
            $_SESSION['id'] = $row['id'];
            $_SESSION['uuid'] = $row['uuid'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['valide'] = $row['valide'];
            $_SESSION['notif'] = $row['notif'];
            return TRUE;
        } 
        return FALSE;
    }

    public static function updateUsername($id, $username) {
        if (empty($username)) {
            return;
        }
        $db = getDBConnection();
        try {
            $stmt = $db->prepare('UPDATE users SET username = ? WHERE id = ?');
            $result = $stmt->execute([$username, $id]);
            if ($result && $stmt->rowCount() == 1) {
                $_SESSION['username'] = $username;
                return "L'username a été mis à jour avec succès.";
            } else {
                return "Le changement d'username a échoué.";
            }
        } catch (PDOException $e) {
        }
        return "Le changement d'username a échoué.";
    }

    public static function updateMail($id, $mail) {
        if (empty($mail)) {
            return ;
        }
        $db = getDBConnection();
        try {
            $stmt = $db->prepare('UPDATE users SET email = ?, valide = ? WHERE id = ?');
            $stmt->execute([$mail, 0, $id]);
            if ($stmt->rowCount() == 1) {
                $_SESSION['valide'] = 0;
                $_SESSION['email'] = $mail;
                return "L'email a été mis à jour avec succès.";
            } else {
                return "Le changement d'email à échoue.";
            }
        } catch (PDOException $e) {
        }
        return "Le changement d'email à échoue.";
    }

    public static function updatePwd($id, $pwd) {
        if (empty($pwd)) {
            return ;
        }
        if (!self::parsePwd($pwd)) {
            return "Le mot de passe n'est pas conforme au règle.";
        }
        $pwd = password_hash($pwd, PASSWORD_DEFAULT);
        $db = getDBConnection();
        $stmt = $db->prepare('UPDATE users SET pwd = ? WHERE id = ?');
        $stmt->execute([$pwd, $id]);
        
        if ($stmt->rowCount()) {
            return "Le mot de passe à été mis à jour avec succès.";
        } else {
            return "Le changement de mot de passe à échoue.";
        }
    }

    public static function setting($id, $username, $mail, $pwd, $oldpwd) {
        $user = self::getById($id);
        if (!password_verify($oldpwd, $user['pwd'])) {
            $mess['pass'] = "Mot de passe actuel invalide.";
            return $mess;
        }
        $mess = [];
        if (!empty($username) && $username != $_SESSION['username']) {
            $mess['username'] = self::updateUsername($id, $username);
        }
        if (!empty($mail) && $mail != $_SESSION['email']) {
            $mess['mail'] = self::updateMail($id, $mail);
        }
        if (!empty($pwd)) {
            $mess['pwd'] = self::updatePwd($id, $pwd);
        }
        $user = self::getById($id);
        return $mess;
    }

    public static function notification($notification, $id) {
        if ($notification == '1' || $notification == '0') {
            $db = getDBConnection();
            $stmt = $db->prepare('UPDATE users SET notif = ? WHERE id = ?');
            $stmt->execute([$notification, $id]);
        }
    }

    public static function valideWithCode($uuid, $mail) {
        $db = getDBConnection();
        $stmt = $db->prepare('UPDATE users SET valide = ? WHERE uuid = ? and email = ?');
        $stmt->execute([1, $uuid, $mail]);
        if ($stmt->rowCount() == 1) {
            $_SESSION['valide'] = 1;
        }
    } 

    public static function mailForValide($to, $code) {
        $subject = "Valider son compte";
        $body = "<html>
                <head>
                    <title>Valider votre compte</title>
                </head>
                <body>
                    <h1>Merci de votre inscription sur le site !</h1>
                    <p>Dernière étape pour valider votre compte et profiter de Camagru.</p>
                    <p>Cliquer sur le bouton ci-dessous pour valider votre compte :</p>
                    <a href='http://127.0.0.1:8080/index.php?controller=user&action=verify&code=$code' target='_blank' style='background-color: #4CAF50; border: none; color: white; padding: 15px 32px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px; margin: 4px 2px; cursor: pointer; border-radius: 8px;'>Valider mon compte</a>
                </body>
                </html>";
        return self::send_mail($to, $subject, $body);
    }

    public static function resetPassword($username, $code, $pwd) {
        if (empty($username)) {
            $mess['username'] = "Username invalide.";
        }
        $user = self::getByUsername($username, $code);
        if ($user) {
            $mess['pwd'] = self::updatePwd($user['id'], $pwd);
        } else {
            $mess['username'] = "Error, username ou code invalide.";
        }
        return $mess;
    }

    public static function mailForPassword($to) {
        $user = self::getByMail($to);
        if (!$user) {
            return "Email inexistant";
        }
        $code = $user['uuid'];
        $subject = "Demande de nouveau mot de passe";
        $body = "
        <html>
        <head>
            <title>Valider votre compte</title>
        </head>
        <body>
            <h1>Voici le lien pour créer votre nouveau mot de passe.</h1>
            <p>Avec le code <span style='font-weight: bold;'>$code</span></p>
            <a href='http://127.0.0.1:8080/index.php?controller=user&action=forget&code=$code' target='_blank' style='background-color: #4CAF50; border: none; color: white; padding: 15px 32px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px; margin: 4px 2px; cursor: pointer; border-radius: 8px;'>Changer mon mot de passe</a>
            </body>
        </html>";
        return self::send_mail($to, $subject, $body);
    }

    public static function send_mail($to, $subject,  $body) {
        try {
            $mail = new PHPMailer(true);
        
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = getenv('MAIL');
            $mail->Password   = getenv('MAIL_PASSWORD');
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->setFrom(getenv('MAIL'));
            $mail->addAddress($to);
            $mail->CharSet = 'UTF-8';
            $mail->Encoding = 'base64';
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->send();
            return "Email envoyé";
        } catch (Exception $e) {
            return "L'envoi de l'email a échoué. Erreur: {$mail->ErrorInfo}";
        }
    }
}
?>
