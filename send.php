<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$mail = new PHPMailer(true);

try {
    // 🔐 Настройки SMTP (ЗАМЕНИ НА СВОИ)
    $mail->isSMTP();
    $mail->Host = 'mail.yourdomain.com'; // например: mail.site.com
    $mail->SMTPAuth = true;
    $mail->Username = 'info@yourdomain.com'; // твоя почта
    $mail->Password = 'your_password'; // пароль
    $mail->SMTPSecure = 'tls'; // или 'ssl'
    $mail->Port = 587; // 465 для ssl

    // 📩 От кого
    $mail->setFrom('info@yourdomain.com', 'Сайт');

    // 📬 Кому
    $mail->addAddress('info@yourdomain.com');

    // 📄 Данные из формы
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];

    // ✉️ Контент
    $mail->isHTML(false);
    $mail->Subject = 'Новое сообщение с сайта';
    $mail->Body =
        "Имя: $name\n" .
        "Email: $email\n\n" .
        "Сообщение:\n$message";

    $mail->send();
    echo "Сообщение отправлено!";
} catch (Exception $e) {
    echo "Ошибка: {$mail->ErrorInfo}";
}