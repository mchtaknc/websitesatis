<?php
isLoggedRedirect('admin');
if (!isset($request[1])) {
    $request[1] = 'default';
}
if (!file_exists("templates/" . $request[0] . "-" . $request[1] . ".php")) {
    header("location:" . $domain_admin);
}
require_once("templates/" . $request[0] . "-" . $request[1] . ".php");