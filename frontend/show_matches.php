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
$isLoggedIn = $auth->is_authenticated();
function getTeamName($id){
    global $teamsStorage;
    return $teamsStorage->findById($id)['name'];
}

function matchColor($score1 , $score2){
    if($score1 > $score2) return 'rgb(122, 255, 104)';
    else if($score1 < $score2) return 'rgb(255, 88, 88)';
    return 'rgb(245, 255, 104)';
}

function isAdmin(){
    return (isset($_SESSION['user']['username']) && $_SESSION['user']['username'] == 'admin');
}

function home($match){ global $teamID; return $match['home']['id'] == $teamID ;} 
function away($match){ global $teamID; return $match['away']['id'] == $teamID ;}
function decided($match) {return is_numeric($match['home']['score'])  && is_numeric($match['away']['score']);}

$data = [];
if(isset($_GET['type']) && $_GET['type'] == 'fav'){
    foreach($userStorage->findById($user['id'])['likedteams'] as $id){
        foreach($matches as $match){
            if($match['home']['id'] == $id || $match['away']['id'] == $id){
                $data[] = $match;
            }   
        }
    }
}else if(isset($_GET['type']) && $_GET['type'] == 'all'){
    $data = $matches;
}else{
    header("Location: ./index.php", true, 301);
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style/index.css">
    <title>show matches</title>
</head>
<body>
    <?php if($_GET):?>
        <div class="container">
            <div class="matches" >
                <div class="comments-container" style="background-color: rgb(106, 255, 138); text-align:center;">
                    <h3 style="font-size: 3rem;"> <?= ($_GET['type'] == 'all') ? 'ALL MATCHES' : 'FAVORATE TEAMS MATCHES' ?> </h3>
                </div>
                <?php foreach($data as $match):?>
                <div class="match-content" style="border: 1px solid green;">
                    <div class="column">
                        <div class="team-de team--home">
                            <div class="team-logo">
                                <img src="<?= $teams[$match['home']['id']]['logo']?>" />
                            </div>
                            <h2 class="team-name"><?= getTeamName($match['home']['id'])?></h2>
                        </div>
                    </div>
                    <div class="column">
                        <div class="match-details">
                            <div class="match-date">
                                <?=$match['date']?>
                            </div>
                            <div class="match-score">
                                <?php if(decided($match)):?>
                                    home <span class="match-score-number match-score-number--leading"><?=$match['home']['score']?></span>
                                    <span class="match-score-divider">:</span>
                                    <span class="match-score-number"><?=$match['away']['score']?></span> away
                                <?php else:?>
                                    home <span class="match-score-number match-score-number--leading">◻</span>
                                    <span class="match-score-divider">:</span>
                                    <span class="match-score-number">◻</span> away
                                <?php endif?>
                            </div>
                        </div>
                    </div>
                    <div class="column">
                        <div class="team-de team--away">
                            <div class="team-logo">
                                <img src="<?= $teams[$match['away']['id']]['logo']?>" />
                            </div>
                            <h2 class="team-name"><?=getTeamName($match['away']['id'])?> </h2>
                        </div>
                    </div>
                </div>
                <?php endforeach?>
            </div>
        </div>
    <?php endif?>
</body>
</html>