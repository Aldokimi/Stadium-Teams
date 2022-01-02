<?php 
session_start();
$matchID = ($_GET['id'] ?? '');
$teamID  = ($_GET['teamID'] ?? '');


require_once(__DIR__ . "/../backend/utils/storage.inc.php");
require_once(__DIR__ . "/../backend/utils/auth.inc.php");

$userStorage    = new Storage(new JsonIO(__DIR__ . "/../backend/data/users.json"  ));
$teamsStorage   = new Storage(new JsonIO(__DIR__ . "/../backend/data/teams.json"  ));
$matchesStorage = new Storage(new JsonIO(__DIR__ . "/../backend/data/matches.json"));

$auth = new Auth($userStorage);

$user       = $auth->authenticated_user();
$teams      = $teamsStorage->findAll();
$matches    = $matchesStorage->findAll();
$isLoggedIn = $auth->is_authenticated();

// print_r($matchesStorage->findById($matchID));

// Original date
$home = $matchesStorage->findById($matchID)['home'];
$away = $matchesStorage->findById($matchID)['away'];
$date = $matchesStorage->findById($matchID)['date'];

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

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
function isValidDate($date, $format= 'Y-m-d'){
    return $date == date($format, strtotime($date));
}
function validate($input, &$data, &$errors){

    //validating home score
    if(isset($input['home-score']) && $input['home-score'] != ''){
        if(is_numeric($input['home-score'])){
            global $matchID;
            global $matchesStorage;
            $data['home'] = array(
                'id' => $matchesStorage->findById($matchID)['home']['id'],
                'score' => $input['home-score'],
            );
        }else{
            $errors['invalid-home-score'] = 'Invalid home score!';
        }
    }else{
        global $home;
        $data['home'] = $home;
    }

    //vakudating away score
    if(isset($input['away-score']) && $input['away-score'] != ''){
        if(is_numeric($input['away-score'])){
            global $matchID;
            global $matchesStorage;
            $data['away'] = array(
                'id' => $matchesStorage->findById($matchID)['away']['id'],
                'score' => $input['away-score'],
            );
        }else{
            $errors['invalid-away-score'] = 'Invalid away score!';
        }
    }else{
        global $away;
        $data['away'] = $away;
    }

    //validating data
    if(isset($input['date']) && $input['date'] != '' ){
        if(isValidDate($input['date'])){
            $data['date'] = $input['date'];
        }else{
            $errors['invalid-date'] = 'Invalid date!';
        }
    }else{
        global $date;
        $data['date'] = $date;
    }

    return count($errors) === 0;
}
  
$errors = [];
$data = [];
if($_POST){
    if(validate($_POST, $data, $errors)){
        $data['id'] = $matchID;
        $matchesStorage->update($matchID, $data);
        header("Location: ./team_details.php?teamID=".$teamID, true, 301);
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/index.css">
    <title>admin page</title>
</head>
<body>
<div class="container">
    <div class="matches">
        <?php foreach($matches as $id => $match):?>
            <?php if($match['id'] == $matchID):?>
                <?php if(home($match)):?>
                    <div class="match-content" 
                    style="background-color:<?= decided($match) ? matchColor((int)$match['home']['score'] , (int)$match['away']['score']) : 'white' ?>;">

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
                    <?php elseif(away($match)):?>
                        <div class="match-content" 
                        style="background-color:<?= decided($match) ? matchColor((int)$match['away']['score'] , (int)$match['home']['score']) : 'white' ?>;">
                        
                        <div class="column">
                            <div class="team-de team--home">
                                <div class="team-logo">
                                    <img src="<?=$teams[$match['away']['id']]['logo']?>" />
                                </div>
                                <h2 class="team-name"><?= getTeamName($match['away']['id'])?></h2>
                            </div>
                        </div>
                        <div class="column">
                            <div class="match-details">
                                <div class="match-date">
                                    <?=$match['date']?>
                                </div>
                                <div class="match-score">
                                    <?php if(decided($match)):?>
                                        away <span class="match-score-number match-score-number--leading"><?=$match['away']['score']?></span>
                                        <span class="match-score-divider">:</span>
                                        <span class="match-score-number"><?=$match['home']['score']?></span> home
                                    <?php else:?>
                                        away <span class="match-score-number match-score-number--leading">◻</span>
                                        <span class="match-score-divider">:</span>
                                        <span class="match-score-number">◻</span> home 
                                    <?php endif?>
                                </div>
                            </div>
                        </div>
                        <div class="column">
                            <div class="team-de team--away">
                                <div class="team-logo">
                                    <img src="<?=$teams[$match['home']['id']]['logo']?>" />
                                </div>
                                <h2 class="team-name"><?=getTeamName($match['home']['id'])?> </h2>
                            </div>
                        </div>
                    </div>
                <?php endif?>
            <?php endif?>
        <?php endforeach?>
    </div>

    <div class="form">
        <form class="login-form" action="" method="post" novalidate>
            <span class="logo">MODIFY MATCH <strong><?=$matchID?></strong></span>
            <div class="modify-container">
                <!-- HOME -->
                <h2 class="label">HOME: </h2>
                <div id="home-score">
                    <?php if(isset($data['home-score'])):?>
                        <input type="number" name="home-score" value="<?=$data['home-score']?>" placeholder="score"/>
                    <?php else:?>
                        <input type="number" name="home-score" placeholder="score" />
                    <?php endif?>
                </div>

                <!-- AWAY -->
                <h2 class="label">AWAY: </h2>

                <div id="away-score">
                    <?php if(isset($data['away-score'])):?>
                        <input type="number" name="away-score" value="<?=$data['away-score']?>" placeholder="score"/>
                    <?php else:?>
                        <input type="number" name="away-score" placeholder="score" />
                    <?php endif?>
                </div>

                <!-- DATE -->
                <h2 class="label">DATE: </h2>
                <?php if(isset($data['date'])):?>
                    <input type="date" id="date" name="date" value="<?=$data['date']?>" placeholder="score"/>
                <?php else:?>
                    <input type="date" id="date" name="date" placeholder="date" />
                <?php endif?>
            <button type="submit" id='seewho' class="btns" onclick="modifyMatch(<?= json_encode($data)?>)">modify</button>
            </div>
        </form>  
    </div>
</body>
</html>