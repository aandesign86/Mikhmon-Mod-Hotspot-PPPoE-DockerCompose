<?php
session_start();
error_reporting(0);
if (!isset($_SESSION["mikhmon"])) {
    header("Location:../admin.php?id=login");
} else {
    include_once('./lib/routeros_api.class.php');
    include_once('./include/config.php');
    include_once('./include/readcfg.php');
    
    $API = new RouterosAPI();
    if ($API->connect($iphost, $userhost, decrypt($passwdhost))) {
        if (isset($_GET['remove-pprofile'])) {
            $id = $_GET['remove-pprofile'];
            $API->comm("/ppp/profile/remove", array(".id" => $id));
        }
    }
}
?>