<?php
session_start();

require_once(__DIR__ . "/../backend/utils/storage.inc.php");
require_once(__DIR__ . "/../backend/utils/auth.inc.php");

$userStorage    = new Storage(new JsonIO(__DIR__ . "/../backend/data/users.json"  ));
$teamsStorage   = new Storage(new JsonIO(__DIR__ . "/../backend/data/teams.json"  ));
$matchesStorage = new Storage(new JsonIO(__DIR__ . "/../backend/data/matches.json"));

$auth = new Auth($userStorage);

$user = $auth->authenticated_user();
$teams = $teamsStorage->findAll();
$matches = $matchesStorage->findAll();

if($_GET){
    if(isset($_GET['more'])){
        usort($matches, 'date_compare');
        $matches = array_reverse($matches);
        $data = [];
        $cnt = 0;
        foreach($matches as $match){
            if($cnt <= 5 && $cnt > 10){
                $data[] = $match;
            }
            if($cnt == 10) break;
            $cnt++;
        }
        echo json_encode($data);
    }else if(isset($_GET['fav'])){
        $data = [];
        foreach($user['likedteams'] as $id){
            foreach($matches as $match){
                if($match['home']['id'] == $id || $match['away']['id'] == $id){
                    $data[] = $match;
                }   
            }
        }
        echo json_encode($data);
    }else{
        header("Location: ./index.php", true, 301);
        exit();
    }
}