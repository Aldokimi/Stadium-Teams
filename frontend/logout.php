<?php
session_start();

require_once(__DIR__ . "/../backend/utils/storage.inc.php");
require_once(__DIR__ . "/../backend/utils/auth.inc.php");

$userStorage = new Storage(new JsonIO(__DIR__ . "/../backend/data/users.json"));
$auth = new Auth($userStorage);


if($auth->is_authenticated()){
    $auth->logout();
    header("Location: ./index.php", true, 301);
    exit();
}