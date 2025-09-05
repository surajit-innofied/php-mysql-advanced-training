<?php
require __DIR__ . '/vendor/phpmailer/phpmailer/src/PHPMailer.php';
require __DIR__ . '/vendor/phpmailer/phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;

var_dump(class_exists('\PHPMailer\PHPMailer\PHPMailer')); // Should output: bool(true)