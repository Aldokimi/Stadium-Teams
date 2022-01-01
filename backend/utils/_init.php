<?php

session_start ();

require_once(__DIR__ . "/storage.inc.php");
require_once(__DIR__ . "/auth.inc.php");

$userStorage    = new Storage(new JsonIO(__DIR__ . "/../data/users.json"  ));
$teamsStorage   = new Storage(new JsonIO(__DIR__ . "/../data/teams.json"  ));
$matchesStorage = new Storage(new JsonIO(__DIR__ . "/../data/matches.json"));

$auth = new Auth($userStorage);

$user = $auth->authenticated_user();
$teams = $teamsStorage->findAll();
$matches = $matchesStorage->findAll();

$numberOfTeams = count($teams);

usort($matches, 'date_compare');

if(isset($_SESSION['user'])){
    $username = array_shift($_SESSION)['username'];
}

$isLoggedIn = $auth->is_authenticated();

function date_compare($element1, $element2) {
    $datetime1 = strtotime($element1['date']);
    $datetime2 = strtotime($element2['date']);
    return $datetime1 - $datetime2;
} 

function getTeamName($id){
    global $teamsStorage;
    return $teamsStorage->findById($id)['name'];
}

function matchColor($score1 , $score2){
    if($score1 > $score2) return '#66ff66';
    else if($score1 < $score2) return '#ff3333';
    return '#ffff1a';
}

function isAdmin(){
    return (isset($username) && $username == 'admin');
}