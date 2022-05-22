<?php
ob_start();
session_start();
$path = __DIR__ . "/../";
$pathAdmin = __DIR__ . "/../uygulama/";
$uploadPath = __DIR__ . "/../uploads/";

$error['type'] = 0;
$error['message'] = "Parametre Hatalı!";
require_once($path . '_com/vendor/autoload.php');
require_once($path . '_com/Whois.php');
require_once($path . '_com/config.php');

use Intervention\Image\ImageManagerStatic as Image;
use PHPMailer\PHPMailer\PHPMailer;
use Cocur\Slugify\Slugify;

$dirname = dirname($_SERVER['SCRIPT_NAME']);
$dirname = $dirname != '/' ? $dirname : null;
$basename = basename($_SERVER['SCRIPT_NAME']);
$request_uri = str_replace([$dirname, $basename], null, $_SERVER['REQUEST_URI']);
$domain = site_url();
$domain_admin = site_url() . 'panel911/';
$_GET = clear($_GET);
$_POST = clear($_POST);
$request = explode("?", substr($request_uri, 1));
if (!empty($request[1])) {
    $gets = explode("&", $request[1]);
    $get = array();
    foreach ($gets as $getto) {
        $getto = explode("=", $getto);
        $get[$getto[0]] = $getto[1];
    }
}
$uri = $request[0];
$request = explode("/", $request[0]);
$slugify = new Slugify();
if (cookie('staff')) {
    $cookie = hash('sha256', uniqid());
    $query = $db->prepare("select * from staff where cookie = ?");
    $query->execute([cookie('staff')]);
    $row = $query->fetch();
    if ($query->rowCount() > 0) {
        foreach ($row as $key => $val) {
            if ($key !== 'password') {
                $_SESSION['back'][$key] = $val;
            }
        }
    }
}
if (cookie('customer')) {
    $cookie = hash('sha256', uniqid());
    $query = $db->prepare("select * from customers where cookie = ? and status = 1");
    $query->execute([cookie('customer')]);
    $row = $query->fetch();
    if ($query->rowCount() > 0) {
        foreach ($row as $key => $val) {
            if ($key !== 'password') {
                $_SESSION['front'][$key] = $val;
            }
        }
    }
}

if (session('front.customer_id')) {
    $query = $db->prepare("select * from customers where customer_id = ? and status = 1");
    $query->execute([session('front.customer_id')]);
    $user = $query->fetch();
}

if(session('back.staff_id')) {
    $query = $db->prepare("select * from staff where staff_id = ?");
    $query->execute([session('back.staff_id')]);
    $staff = $query->fetch();
}
function register($data) {
    global $db;
    $hash = hash('sha256',$data['password']);
    $db->prepare("insert into customers (
            type,
            firstname,
            lastname,
            phone,
            company_name,
            tax_id,
            tax_office,
            email,
            password
        ) values (?,?,?,?,?,?,?,?,?)")->execute([
        $data['userType'],
        $data['firstname'],
        $data['lastname'],
        $data['phone'],
        $data['companyname'],
        $data['tax_id'],
        $data['tax_office'],
        $data['email'],
        $hash
    ]);
    $user_id = $db->lastInsertId();
    return $user_id;
}
function pre_up($str)
{
    $find = array('i', 'ı'); //any turkish chars
    $replace = array('İ','I');
    $new = str_replace($find,$replace,$str);
    return $new;
}
function urlVars($new=array()) {
    $vars=array_merge($_GET, $new);
    foreach ($vars as $key => $var) {
        $params[$x]='&' . $key . '=' . $var;
        $x++;
    } $str='?' . trim(implode($params), '&');
    return $str;
}
function staffCheck($staff_id) {
    global $db;
    $kontrol = $db->prepare("select * from staff where staff_id = ?");
    $kontrol->execute([$staff_id]);
    return $kontrol->rowCount() > 0;
}

function slugCheck($title, $type = '')
{
    global $db, $slugify;
    $slug = $slugify->slugify(unclear($title));
    $types = [
        'theme_category' => "theme_categories where slug LIKE :slug",
        'theme' => "themes where slug LIKE :slug"
    ];
    $slugKontrol = $db->prepare("select COUNT(*) as count from {$types[$type]}");
    $slugKontrol->execute([':slug' => $slug.'%']);
    $slugKontrol = $slugKontrol->fetch();
    if ($slugKontrol->count >= 1) {
        $slug = $slug . '-' . $slugKontrol->count;
    }
    return $slug;
}

function isJson($string)
{
    json_decode($string);
    return json_last_error() === JSON_ERROR_NONE;
}

function arrayDot($keys,$array = array()) {
    $items = $array;
    $keys = explode('.',$keys);
    foreach($keys as $key) {
        if(!is_array($items)) {
            return false;
        }
        $items = $items[$key];
    }
    return $items;
}
function session($name)
{
    $arr = arrayDot($name,$_SESSION);
    return isset($arr) ? $arr : false;
}

function cookie($name)
{
    return isset($_COOKIE[$name]) ? $_COOKIE[$name] : false;
}

function checkForm($required) {
    global $error;
    foreach ($required as $key => $field) {
        if($key == 'email') {
            if(!filter_var($_POST[$key],FILTER_VALIDATE_EMAIL)) {
                $error['type']++;
                $error['message'] = "Lütfen geçerli bir e-posta adresi giriniz.";
                break;
            }
        }
        if (!isset($_POST[$key]) || $_POST[$key] == '') {
            $error['message'] = "{$field} gereklidir.";
            break;
        }
    }
    return $error['type'] == 0 ? false : true;
}

function requireCheck($required) {
    global $error;
    $return = true;
    foreach ($required as $key => $field) {
        if (!isset($_POST[$key]) || $_POST[$key] == '') {
            $error['type']++;
            $error['message'] = "{$field} gereklidir.";
            $return = false;
            break;
        } else {
            if($key == 'password') {
                if($_POST['password'] !== $_POST['password_confirmation']) {
                    $error['type']++;
                    $error['message'] = "Şifre ve tekrarı birbiriyle uyuşmamaktadır.";
                    $return = false;
                    break;
                }
                if (strlen($_POST['password']) < 8) {
                    $error['type']++;
                    $error['message'] = "Şifreniz en az 8 karakterden oluşmalıdır.";
                    $return = false;
                    break;
                }
            }
            if($key == 'email') {
                if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                    $error['type']++;
                    $error['message'] = "Lütfen geçerli bir e-posta adresi giriniz.";
                    $return = false;
                    break;
                }
            }
            if($key == 'website' || $key == 'url') {
                if(!filter_var($_POST[$key], FILTER_VALIDATE_URL)) {
                    $error['type']++;
                    $error['message'] = "Lütfen geçerli bir url giriniz.";
                    $return = false;
                    break;
                }
            }
        }
    }
    return $return;
}

function rearrangeUploadArray(array $array)
{
    if (!is_array(reset($array)))
        return $array;
    $rearranged = [];
    foreach ($array as $property => $values)
        foreach ($values as $key => $value)
            $rearranged[$key][$property] = $value;
    foreach ($rearranged as &$value)
        $value = rearrangeUploadArray($value);
    return $rearranged;
}

function uploadImage($input, $par = array(), $upload = '', $thumb = false)
{
    global $uploadPath,$slugify;
    $date = date('Y/m/d');
    if ($upload != '') {
        $upload = $upload . '/' . $date . '/';
    } else {
        $upload = $date . '/';
    }
    if (!is_dir($uploadPath . $upload)) {
        mkdir($uploadPath . $upload, 0755, true);
    }
    $path_info = pathinfo($input['name']);
    $fileName = $slugify->slugify($path_info['filename']) . '-' . uniqid();
    $img = Image::make($input['tmp_name']);
    $img->orientate();
    if (isset($par['resize']) && $par['resize'] != false) {
        if (isset($par['resize']['x']) || isset($par['resize']['y'])) {
            $img->resize($par['resize']['x'], $par['resize']['y'], function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        }
    }
    if (isset($par['widen']) && $par['widen'] != false) {
        $img->widen($par['widen']);
    }
    if (isset($par['fit']) && $par['fit'] != false) {
        $img->fit($par['fit']['x'], $par['fit']['y']);
    }
    $img->save($uploadPath . $upload . $fileName . '.' . $path_info['extension']);
    $img->destroy();
    if ($thumb) {
        $img = Image::make($input['tmp_name']);
        $img->orientate();
        $img->resize(400, 400, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        $img->save($uploadPath . $upload . $path_info['filename'] . "_thumb." . $path_info['extension']);
        $img->destroy();
    }
    return [
        'fullName' => $fileName . '.' . $path_info['extension'],
        'name' => $fileName,
        'extension' => $path_info['extension'],
        'path' => 'uploads/' . $upload,
    ];
}

function site_url($url = '')
{
    return BASE_URL . '/' . $url;
}

function assets_url($url = '')
{
    return site_url('assets/' . $url);
}

function clear($x)
{
    if (!is_array($x)) {
        $x = htmlspecialchars(trim($x));
    } else {
        array_walk_recursive($x, function (&$item) {
            $item = htmlspecialchars(trim($item));
        });
    }
    return $x;
}

function unclear($x)
{
    if (!is_array($x)) {
        $x = htmlspecialchars_decode($x);
    } else {
        array_walk_recursive($x, function (&$item) {
            $item = htmlspecialchars_decode($item);
        });
    }
    return $x;
}

function debug($on = 0)
{
    if ($on == 0) {
        error_reporting(0);
        ini_set("display_errors", 0);
    } else {
        error_reporting(E_ALL);
        ini_set("display_errors", 1);
    }
}

function curl($url, $post = '')
{
    $user_agent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; tr; rv:1.9.0.6) Gecko/2009011913 Firefox/3.0.6';
    $timeout = 1;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSLVERSION, 6);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    if (!empty($post)) {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    }
    curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

function phpMailer($alici, $baslik, $mesaj, $reply = array())
{
    $mail = new PHPMailer;
    $mail->clearAllRecipients();
    $mail->clearAddresses();
    $mail->clearCCs();
    $mail->clearBCCs();
    $mail->clearAttachments();
    $mail->SMTPDebug = 0;
    $mail->isSMTP();
    $mail->Host = 'mail.sitedeposu.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'smtp@sitedeposu.com';
    $mail->Password = 'fz5zJ1#4';
    $mail->SMTPSecure = "tls";
    $mail->Port = 587;
    $mail->smtpConnect(
        array(
            "ssl" => array(
                "verify_peer" => false,
                "verify_peer_name" => false,
                "allow_self_signed" => true
            )
        )
    );
    $mail->setFrom("info@sitedeposu.com", "Sitedeposu");
    if (!empty($reply)) {
        $mail->addReplyTo($reply['mail'], $reply['name']);
    }
    if (is_array($alici)) {
        foreach ($alici as $item) {
            $mail->addAddress($item);
        }
    } else {
        $mail->addAddress($alici);
    }
    $mail->Subject = $baslik;
    $mail->CharSet = "UTF-8";
    $mail->isHTML(true);
    $mail->Body = $mesaj;
    if ($mail->send()) {
        return true;
    } else {
        return false;
    }
}

function isLogged($inline = '')
{
    if ($inline == '') {
        return isset($_SESSION['front']['customer_id']);
    } else {
        return isset($_SESSION['back']['staff_id']);
    }
}

function isLoggedRedirect($inline = '')
{
    global $domain;
    global $domain_admin;
    if ($inline == '') {
        if(!isLogged()) {
            redirect($domain);
        }
    } else {
        if(!isLogged('admin')) {
            redirect($domain_admin.'login');
        }
    }
}

function randomString($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function logOut($par = '')
{
    global $domain;
    global $domain_admin;
    $random_string = hash("sha256", randomString());

    if ($par == '') {
        $_SESSION['front'] = array();
        setcookie("customer", $random_string, strtotime("-1 day"), "/");
        redirect($domain);
    } else if ($par == 'admin') {
        $_SESSION['back'] = array();
        setcookie("staff", $random_string, strtotime("-1 day"), "/");
        redirect($domain_admin);
    }
}

function virgulNokta($str)
{
    $str = str_replace(",", ".", $str);
    return $str;
}

function noktaVirgul($str)
{
    $str = str_replace(".", ",", $str);
    return $str;
}

function obj2arr($obj)
{
    $array = json_decode(json_encode($obj), true);
    return $array;
}

function redirect($url)
{
    if (!headers_sent()) {
        header('Location: ' . $url);
        exit;
    } else {
        echo '<script type="text/javascript">';
        echo
            'window.location.href="' . $url .
            '";';
        echo
        '</script>';
        echo '<noscript>';
        echo '<meta http-equiv="refresh" content="0;url=' . $url . '"/>';
        echo '</noscript>';
        exit;
    }
}

function get_client_ip()
{
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if (getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if (getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if (getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if (getenv('HTTP_FORWARDED'))
        $ipaddress = getenv('HTTP_FORWARDED');
    else if (getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = '127.0.0.1';
    return $ipaddress;
}

function createDateRangeArray($strDateFrom, $strDateTo)
{
//Ymd
    $aryRange = array();
    $iDateFrom = mktime(1, 0, 0, substr($strDateFrom, 4, 2), substr($strDateFrom, 6, 2), substr($strDateFrom, 0, 4));
    $iDateTo = mktime(1, 0, 0, substr($strDateTo, 4, 2), substr($strDateTo, 6, 2), substr($strDateTo, 0, 4));
    if ($iDateTo >= $iDateFrom) {
        array_push($aryRange, date('Ymd', $iDateFrom));
        while ($iDateFrom < $iDateTo) {
            $iDateFrom += 86400;
            array_push($aryRange, date('Ymd', $iDateFrom));
        }
    }
//    $aryRange = array_pop($aryRange);
    return $aryRange;
}
