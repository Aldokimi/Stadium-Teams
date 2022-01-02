<?php 
session_start();

require_once(__DIR__ . "/../backend/utils/storage.inc.php");
require_once(__DIR__ . "/../backend/utils/auth.inc.php");

$userStorage   = new Storage(new JsonIO(__DIR__ . "/../backend/data/users.json"  ));
$auth = new Auth($userStorage);
$user = $auth->authenticated_user();

if($_GET){
    $teamID = $_GET['teamID'] ?? '';
    $userID = $_GET['userID'] ?? '';
    if($teamID != '' && $userID){
        $user['likedteams'][] = $teamID;
        $userStorage->update($userID, $user);
    }
    header("Location: ./index.php", true, 301);
    exit();
}