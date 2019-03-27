<?php

namespace App\Models;

use PHPMailer\PHPMailer\PHPMailer;

class Mail
{

    private $mail;

    public function __construct()
    {
        $this->mail = new PHPMailer(true);
    }

    private function setConfig()
    {
        //$this->mail->SMTPDebug = 2;
        $this->mail->isSMTP();
        $this->mail->SMTPAuth = true;
        $this->mail->SMTPSecure = 'ssl';
        $this->mail->Host = 'smtp.uxsense.com.br';
        $this->mail->Port = 465;
        $this->mail->Username = '';
        $this->mail->Password = '';
        $this->mail->CharSet = 'utf-8';
        $this->mail->setFrom('no-replay@uxsense.com.br', 'UXSense');
        $this->mail->isHTML(true);
    }

    public function sendCodeChangePassword($email, $token, $name, $subject)
    {
        $this->setConfig();
        $this->mail->addAddress($email, $name);
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
        $this->setConfig();
        $this->mail->addAddress($register, $name);
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
        $send = $this->mail->send();
        if ($send) {
            return Mail::returnData(true, true, $this->mail, false);
        } else {
            return Mail::returnData(false, true, $this->mail, false);
        }
    }

    public static function returnData($sent = true, $created = true, $mail, $show_message = false)
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
