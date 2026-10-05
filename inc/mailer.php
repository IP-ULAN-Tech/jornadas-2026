<?php
declare(strict_types=1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../lib/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../lib/PHPMailer/SMTP.php';
require_once __DIR__ . '/../lib/PHPMailer/Exception.php';

function enviar_email(
    string $paraEmail,
    string $paraNome,
    string $assunto,
    string $corpoHtml,
    string $corpoTexto = ''
): bool {
    $cfg = require __DIR__ . '/config.php';
    $smtp = $cfg['smtp'] ?? [];

    if (empty($smtp['ativo'])) {
        return false;
    }
    if (empty($smtp['host']) || empty($smtp['utilizador']) || empty($smtp['password'])) {
        return false;
    }

    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = $smtp['host'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $smtp['utilizador'];
        $mail->Password   = $smtp['password'];
        $mail->SMTPSecure = ($smtp['seguranca'] ?? 'tls') === 'ssl'
            ? PHPMailer::ENCRYPTION_SMTPS
            : PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = (int) ($smtp['port'] ?? 587);
        $mail->CharSet    = 'UTF-8';
        $mail->Timeout    = 20;

        $mail->setFrom($smtp['remetente'] ?? $smtp['utilizador'], $smtp['nome'] ?? 'Jornadas IPS 2026');
        $mail->addAddress($paraEmail, $paraNome);

        if (!empty($smtp['reply_to'])) {
            $mail->addReplyTo($smtp['reply_to']);
        }

        $mail->isHTML(true);
        $mail->Subject = $assunto;
        $mail->Body    = $corpoHtml;

        if ($corpoTexto === '') {
            $corpoTexto = strip_tags(str_replace(['<br>', '</p>', '</h1>', '</h2>'], "\n", $corpoHtml));
        }
        $mail->AltBody = $corpoTexto;

        $mail->send();
        return true;

    } catch (Exception $e) {
        error_log('PHPMailer erro: ' . $e->getMessage());
        return false;
    }
}

function enviar_notificacao_comissao(string $assunto, string $corpo): bool
{
    $cfg = require __DIR__ . '/config.php';
    $para = $cfg['smtp']['remetente'] ?? '';
    if (!$para) return false;
    return enviar_email($para, 'Comissão Organizadora', $assunto, $corpo);
}