<?php

namespace App\Models;

use PHPMailer\PHPMailer\Exception as MailerException;
use PHPMailer\PHPMailer\PHPMailer;

class Mail
{

    private $mail;

    public function __construct()
    {
        $this->mail = new PHPMailer(true);
    }

    /**
     * SMTP settings come from config/mail.php (MAIL_* environment variables).
     * Credentials used to be hard coded here.
     */
    private function setConfig()
    {
        $smtp = (array) config('mail.mailers.smtp', []);
        $this->mail->isSMTP();
        $this->mail->SMTPAuth = !empty($smtp['username']);
        $encryption = $smtp['encryption'] ?? 'tls';
        $this->mail->SMTPSecure = $encryption === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Host = (string) ($smtp['host'] ?? 'localhost');
        $this->mail->Port = (int) ($smtp['port'] ?? 587);
        $this->mail->Username = (string) ($smtp['username'] ?? '');
        $this->mail->Password = (string) ($smtp['password'] ?? '');
        $this->mail->Timeout = 15;
        $this->mail->CharSet = PHPMailer::CHARSET_UTF8;
        $this->mail->setFrom(
            (string) config('mail.from.address', 'no-reply@uxsense.com.br'),
            (string) config('mail.from.name', 'UXSense')
        );
        $this->mail->isHTML(true);
    }

    public function sendCodeChangePassword($email, $token, $name, $subject)
    {
        try {
            $this->setConfig();
            $this->mail->addAddress($email, (string) $name);
        } catch (MailerException $e) {
            return Mail::returnData(false, true, $this->mail, false);
        }
        // User supplied values are escaped: the recipient name comes from the
        // request and could otherwise inject HTML into the message.
        $name = e((string) $name);
        $token = e((string) $token);
        ob_start();
        ?>
        <table border="0" cellpadding="5" cellspacing="0" align="center" style="width: 100%;background-color:#e6e6e6;padding:25px;font-size:14px;font-family:'Trebuchet MS', Helvetica, sans-serif">
            <tr>
                <th width="200"></th>
                <th width="300"></th>
            </tr>
            <tr>
                <td scope="col" colspan="2" align="center" style="padding-bottom: 20px;">
                    <h1 style="margin-bottom: 0;">UXSense</h1>
                    <small>Uma plataforma para avaliação de UX</small>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <h3>Olá <?php echo $name ?>! Este é o passo a passo para acessar o sistema:</h3>
                    <ol>
                        <li>Acesse o site <a href="http://www.uxsense.com.br/">http://www.uxsense.com.br/</a> e clique no menu "Primeiro Acesso"</li>
                        <li>Informe no campo "Código de Acesso" o código que você recebeu neste e-mail</li>
                        <li>Informe no campo Senha a sua nova senha</li>
                        <li>Clique em enviar</li>
                    </ol>
                </td>
            </tr>
            <tr>
                <td colspan="2"><strong>CÓDIGO DE ACESSO:</strong><br>
                    <h4><?php echo $token; ?></h4>
                </td>
            </tr>

        </table>
        <?php
        $html = ob_get_clean();
        $this->mail->Body = $html;
        $send = $this->send($subject);
        return $send;
    }

    public function sendLostPassword($register, $password, $name, $subject)
    {
        try {
            $this->setConfig();
            $this->mail->addAddress($register, (string) $name);
        } catch (MailerException $e) {
            return Mail::returnData(false, true, $this->mail, false);
        }
        $name = e((string) $name);
        $password = e((string) $password);
        ob_start();
        ?>
        <table border="0" cellpadding="5" cellspacing="0" align="center" style="width: 100%;background-color:#e6e6e6;padding:25px;font-size:14px;font-family:'Trebuchet MS', Helvetica, sans-serif">
            <tr>
                <th width="200"></th>
                <th width="300"></th>
            </tr>
            <tr>
                <td scope="col" colspan="2" align="center" style="padding-bottom: 20px;">
                    <h1 style="margin-bottom: 0;">UXSense</h1>
                    <small>Uma plataforma para avaliação de UX</small>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <h3>Olá <?php echo $name ?>! Você informou que esqueceu sua senha.</h3>
                    <p>Segue abaixo uma nova senha temporária.</p>
                    <p>Caso você acesse sua conta UXSense utilizando esta senha, a senha antiga deixará de existir.</p>
                </td>
            </tr>
            <tr>
                <td colspan="2"><strong>SENHA TEMPORÁRIA:</strong><br>
                    <h4><?php echo $password; ?></h4>
                </td>
            </tr>

        </table>
        <?php
        $html = ob_get_clean();
        $this->mail->Body = $html;
        $send = $this->send($subject);
        return $send;
    }

    private function send($subject)
    {
        $this->mail->Subject = '[UXSense] - ' . $subject;
        try {
            $send = $this->mail->send();
        } catch (MailerException $e) {
            $send = false;
        }
        if ($send) {
            return Mail::returnData(true, true, $this->mail, false);
        } else {
            return Mail::returnData(false, true, $this->mail, false);
        }
    }

    public static function returnData($sent, $created, $mail, $show_message = false)
    {
        $message = "";
        $address = [];
        $error = null;
        if (gettype($mail) == "string") {
            $message = $mail;
        } else {
            $error = $mail->ErrorInfo;
            $address = $mail->getToAddresses();
            $mail->clearAllRecipients();
        }
        $data = [
            "sent" => $sent,
            "created" => $created,
            "address" => $address,
            "error" => $error,
            "message" => $message,
            "showMessage" => $show_message,
        ];
        return $data;
    }

}
