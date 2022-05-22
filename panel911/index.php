<?php
require_once(__DIR__."/../_com/functions.php");
if ($request[0] == 'login') {
    require_once("templates/login.php");
} elseif ($request[0] == 'sifremi-unuttum') {
    require_once("templates/sifremi-unuttum.php");
} elseif ($request[0] == 'logout') {
    logOut('admin');
} else {

    if ($request[0] == '') {
        $request[0] = 'default';
    }
    if (!file_exists("templates/" . $request[0] . ".php")) {
        redirect($domain_admin);
    }
    require_once("templates/header.php");
    require_once("templates/" . $request[0] . ".php");
    require_once("templates/footer.php");
}
