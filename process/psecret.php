<?php
session_start();
error_reporting(0);
if (!isset($_SESSION["mikhmon"])) {
    header("Location:../admin.php?id=login");
} else {
    // routeros api (Pastikan API terhubung agar variabel $API tidak kosong)
    include_once('./lib/routeros_api.class.php');
    include_once('./include/config.php');
    include_once('./include/readcfg.php');
    $API = new RouterosAPI();
    $API->connect($iphost, $userhost, decrypt($passwdhost));

    // FIX: Menggunakan window.location.href agar browser langsung memicu reload tabel halaman secara bersih
    
    // Proses Hapus PPP Secret
    if (isset($_GET['remove-pppsecret'])) {
        $id = $_GET['remove-pppsecret'];
        $API->comm("/ppp/secret/remove", array(".id" => $id));
        echo "<script>window.location.href='./?ppp=secrets&session=".$session."';</script>";
        exit();
    }
    
    // Proses Lock / Disable PPP Secret
    if (isset($_GET['disable-pppsecret'])) {
        $id = $_GET['disable-pppsecret'];
        $API->comm("/ppp/secret/set", array(".id" => $id, "disabled" => "yes"));
        echo "<script>window.location.href='./?ppp=secrets&session=".$session."';</script>";
        exit();
    }

    // Proses Unlock / Enable PPP Secret
    if (isset($_GET['enable-pppsecret'])) {
        $id = $_GET['enable-pppsecret'];
        $API->comm("/ppp/secret/set", array(".id" => $id, "disabled" => "no"));
        echo "<script>window.location.href='./?ppp=secrets&session=".$session."';</script>";
        exit();
    }
}
?>