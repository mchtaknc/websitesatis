<?php
require_once "../_com/functions.php";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $name = str_replace(array("\r", "\n"), array(" ", " "), $name);
    $phone = $_POST["phone"];
    $subject = $_POST["subject"];
    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    $message = $_POST["message"];
    if (empty($name) or empty($phone) or empty($subject) or empty($message) or !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo "Hata! Gönderiminiz ile ilgili bir sorun oluştu. Lütfen formu doldurun ve tekrar deneyin.";
        exit;
    }
    $email_content = "<p>Ad Soyad: $name</p>";
    $email_content .= "<p>E-posta: $email</p>";
    $email_content .= "<p>Telefon: $phone</p>";
    $email_content .= "<p>Konu: $subject</p>";
    $email_content .= "<p>Mesaj:$message</p>";
    if (phpMailer("info@sitedeposu.com","Sitedeposu İletişim Formu",$email_content)) {
        http_response_code(200);
        echo "Teşekkürler! Mesajınız gönderildi.";
    } else {
        http_response_code(500);
        echo "Hata! Bir şeyler ters gitti ve mesajınızı gönderemedik.";
    }

} else {
    http_response_code(403);
    echo "Gönderinizle ilgili bir sorun oluştu, lütfen tekrar deneyin.";
}
