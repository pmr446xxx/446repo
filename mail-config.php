<?php
// Konfiguracja SMTP dla PHPMailer
define('SMTP_HOST', 'hosting2626806.online.pro');
define('SMTP_PORT', 587);
define('SMTP_USER', 'admin@446dx.pl');
define('SMTP_PASS', 'Joanna2008@!!');
define('SMTP_FROM', 'admin@446dx.pl');
define('SMTP_FROM_NAME', '446DX.PL');

// Funkcja wysłania emaila resetowania hasła
function sendPasswordResetEmail($email, $username, $resetToken, $resetLink) {
    require_once 'PHPMailer/src/Exception.php';
    require_once 'PHPMailer/src/PHPMailer.php';
    require_once 'PHPMailer/src/SMTP.php';
    
    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
    
    try {
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USER;
        $mail->Password = SMTP_PASS;
        $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = SMTP_PORT;
        
        $mail->setFrom(SMTP_FROM, SMTP_FROM_NAME);
        $mail->addAddress($email, $username);
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        
        $mail->Subject = 'Reset hasła - 446DX.PL';
        $mail->Body = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; background-color: #f5f5f5; }
                .container { max-width: 600px; margin: 0 auto; background-color: white; padding: 20px; border-radius: 8px; }
                .header { background-color: #ff3b3b; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
                .content { padding: 20px; }
                .button { display: inline-block; background-color: #10b981; color: white; padding: 12px 30px; text-decoration: none; border-radius: 4px; margin: 20px 0; }
                .footer { background-color: #f5f5f5; padding: 15px; text-align: center; font-size: 12px; color: #666; border-radius: 0 0 8px 8px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Reset hasła</h1>
                </div>
                <div class='content'>
                    <p>Cześć <strong>$username</strong>,</p>
                    <p>Otrzymaliśmy prośbę o reset hasła do Twojego konta na 446DX.PL.</p>
                    <p>Kliknij poniższy przycisk, aby ustawić nowe hasło:</p>
                    <a href='$resetLink' class='button'>Reset hasła</a>
                    <p>Link jest ważny przez <strong>1 godzinę</strong>.</p>
                    <p>Jeśli to nie Ty złosiłeś prośbę, zignoruj ten email.</p>
                </div>
                <div class='footer'>
                    <p>© 446DX.PL - PMR DX Cluster</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('Mail Error: ' . $mail->ErrorInfo);
        return false;
    }
}
?>