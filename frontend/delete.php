<?php
$id = ($_GET['id'] ?? '');
$teamID = ($_GET['teamID'] ?? '');
require_once(__DIR__ . "/../backend/utils/storage.inc.php");
$commentsStorage = new Storage(new JsonIO(__DIR__ . "/../backend/data/comments.json"));
if($id != '' and $teamID != ''){
    $commentsStorage->delete($id);
    header("Location: ./team_details.php?teadID=".$teamID, true, 301);
    exit();
}else{
    echo 'Link error!';
}