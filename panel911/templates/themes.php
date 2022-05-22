<?php
isLoggedRedirect('admin');
if(!isset($request[1])) {
    $request[1] = 'default';
} else {
    if($request[1] == "category" && !isset($request[2])) {
        $request[2] = "default";
    }
}
$requests = rtrim(implode('-',$request),'-');
if(is_numeric(end($request))) {
    $requests = rtrim($requests,end($request));
    $requests = rtrim($requests,'-');
}
if (!file_exists("templates/".$requests.".php")) {
    //redirect($domain_admin);
}
require_once("templates/".$requests.".php");