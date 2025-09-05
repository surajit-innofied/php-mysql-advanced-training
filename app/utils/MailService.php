<?php
// app/utils/MailService.php

require __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

// ✅ Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

function sendOrderMail($toEmail, $toName, $orderId, $orderItems, $totalAmount, $paymentStatus)
{
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = $_ENV['SMTP_HOST'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['ADMIN_EMAIL']; 
        $mail->Password   = $_ENV['ADMIN_APP_PASSWORD']; 
        $mail->SMTPSecure = 'tls';
        $mail->Port       = $_ENV['SMTP_PORT'];

        // From / To
        $mail->setFrom($_ENV['ADMIN_EMAIL'], $_ENV['ADMIN_NAME']);
        $mail->addAddress($toEmail, $toName);

        // Content
        $mail->isHTML(true);
        $mail->Subject = "Order Confirmation - Order #$orderId";

        // Order details HTML
        $itemsHtml = "";
        foreach ($orderItems as $item) {
            $itemsHtml .= "<li>{$item['name']} (x{$item['quantity']}) - \${$item['unit_price']}</li>";
        }

        $mail->Body = "
            <h2>Thank you for your order!</h2>
            <p><strong>Order ID:</strong> $orderId</p>
            <p><strong>Status:</strong> $paymentStatus</p>
            <p><strong>Total Amount:</strong> \$$totalAmount</p>
            <h3>Items:</h3>
            <ul>$itemsHtml</ul>
            <br>
            <p>We’ll notify you when your order ships.</p>
            <p>Best regards,Surajit Dey</p>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}
