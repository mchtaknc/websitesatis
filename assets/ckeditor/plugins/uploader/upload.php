<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/_com/functions.php";

$error['type'] = 0;
$error['message'] = "Parameter is wrong";

$allowed_file_types = ['image/png', 'image/jpeg'];
$maxsize = 3 * 1024 * 1024;
if ($_FILES['upload']['size'] > 0) {
    if (is_uploaded_file($_FILES['upload']['tmp_name'])) {
        $mime_type = mime_content_type($_FILES['upload']['tmp_name']);
        if (!in_array($mime_type, $allowed_file_types)) {
            $error['type']++;
            $error['message'] = "Allowed file types: png, jpg, jpeg";
        }
        if ($_FILES['upload']['size'] > $maxsize) {
            $error['type']++;
            $error['message'] = "The file size should be maximum 3 megabytes.";
        }

        if ($error['type'] == 0) {
            $dirname = date('Y/m/d');
            $eski_isim = $_FILES['upload']['name'];
            $eski_isim = explode(".", $eski_isim);
            $ext = "." . strtolower(end($eski_isim));
            $zaman = str_replace(".", "", microtime(true));
            $yeni_isim = $slugify->slugify($eski_isim[0]) . "_" . $zaman . $ext;
            $par = [
                    'input_name' => 'upload',
                    'resize' => 1200
                ];
            uploadImage($path, $yeni_isim, $par,'');
            echo json_encode([
                "uploaded" => 1,
                "fileName" => "$yeni_isim",
                "url" => $domain.'uploads/'.$dirname.'/'.$yeni_isim
            ]);
        } else {
            echo json_encode([
                "uploaded" => 0,
                "error" => [
                    "message" => $error['message']
                ]
            ]);
        }
    }
}
