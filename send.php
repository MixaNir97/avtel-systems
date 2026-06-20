<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/PHPMailer/src/Exception.php';
require __DIR__ . '/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/PHPMailer/src/SMTP.php';

header('Content-Type: text/plain; charset=utf-8');

function loadEnv($path) {
    if (!file_exists($path)) {
        die("Ошибка: .env не найден по пути $path");
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        $line = trim($line);

        // пропускаем комментарии
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }

        // разбиваем KEY=VALUE
        if (strpos($line, '=') !== false) {
            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            $_ENV[$key] = $value;
        }
    }
}

loadEnv(__DIR__ . '/.env');

$host   = $_ENV['SMTP_HOST'] ?? '';
$port   = $_ENV['SMTP_PORT'] ?? '';
$user   = $_ENV['SMTP_USER'] ?? '';
$pass   = $_ENV['SMTP_PASS'] ?? '';
$secure = $_ENV['SMTP_SECURE'] ?? '';
$mailTo = $_ENV['MAIL_TO'] ?? '';

echo "Host: " . htmlspecialchars($host) . "<br>";
echo "Username: " . htmlspecialchars($user) . "<br>";
echo "Secure: " . htmlspecialchars($secure) . "<br>";
echo "Port: " . htmlspecialchars($port) . "<br><br>";

if (!$host || !$port || !$user || !$pass || !$secure || !$mailTo) {
    die("Error with .env");
}

$mail = new PHPMailer(true);
try {
    $mail->SMTPDebug = 2;
    $mail->Debugoutput = 'html';
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';
    // SMTP
    $mail->isSMTP();
    $mail->Host = $_ENV['SMTP_HOST'];
    $mail->SMTPAuth = true;
    $mail->Username = $_ENV['SMTP_USER'];
    $mail->Password = $_ENV['SMTP_PASS'];
    $mail->SMTPSecure = $_ENV['SMTP_SECURE'];
    $mail->Port = (int)$_ENV['SMTP_PORT'];

    $mail->setFrom($_ENV['SMTP_USER'], 'Website');
    $mail->addAddress($_ENV['MAIL_TO']);

    $name = $_POST['name'] ?? '';
    $company = $_POST['company'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $location = $_POST['location'] ?? '';
    $message = $_POST['message'] ?? '';

    $mail->addReplyTo($email, $name);

    $mail->Subject = "Neue Anfrage von $name";

    $mail->Body =
        "Name: $name\n" .
        "Firma: $company\n" .
        "Email: $email\n" .
        "Telefon: $phone\n" .
        "Standort: $location\n\n" .
        "Nachricht:\n$message";

    $mail->send();

    echo "E-Mail gesendet!";
} catch (Exception $e) {
    echo "Fehler: " . $mail->ErrorInfo;
}