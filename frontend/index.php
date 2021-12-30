<?php

require_once(__DIR__ . "/../backend/utils/storage.inc.php");
$teamsStorage   = new Storage(new JsonIO(__DIR__ . "/../backend/data/teams.json"));
$matchesStorage = new Storage(new JsonIO(__DIR__ . "/../backend/data/matches.json"));

$teams = $teamsStorage->findAll();
$matches = $matchesStorage->findAll();
$numberOfTeams = count($teams);

function date_compare($element1, $element2) {
    $datetime1 = strtotime($element1['date']);
    $datetime2 = strtotime($element2['date']);
    return $datetime1 - $datetime2;
} 

usort($matches, 'date_compare');

$isLoggedIn = false;
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/index.css">
    <title>ELTE Stadium</title>
</head>
<body>

    <div class="auths" <?= $isLoggedIn ? 'hidden' : '' ?> >
        <div style="float:left; padding: 0.2rem;"><a id="seewho" href="login.php">Sign Up</a></div>
        <div style="float:left; padding: 0.2rem;"><a id="seewho2" href="register.php">Register</a></div>
    </div>
    <div class="description">
        <h1>ELTE Stadium</h1>
        <p>Welcon to <strong>The ELTE Stadium</strong> web page, 
            where the matches played at them will appear, 
            and the fans are able to follow the 
            results of their favorite teams, give comments to the teams and enjoy.</p>
    </div>
    <div class="teams" style="grid-template-rows:    <?php for($i=0;$i<$numberOfTeams;$i++):?><?="1fr"?> <?php endfor?>; 
            grid-template-columns: <?php for($i=0;$i<$numberOfTeams;$i++):?><?="1fr"?> <?php endfor?>;">
        <?php foreach($teams as $id => $team):?>
            <div class="team" onclick="location.href='./team_details.php?teadID=<?=$id?>';" style="padding: 1rem;"> 
                <img src="<?=$team["logo"]?>" alt="<?=$team["name"]?>logo" style="width:100%">
                <h4 style="padding: 0.3rem;"><b><?=$team["name"]?></b></h4>
                <p style="padding: 0.3rem;"><?=$team["city"]?></p> 
            </div>    
        <?php endforeach?>
    </div>
    <!-- Last 5 matches -->
    <div class="last-5-matches">
        <?php $cnt = 0; foreach($matches as $matchID => $match):?>
            <?php 
                if($cnt === 5) break; 
                $cnt++;
                $team1ID = $match["home"]["id"];
                $team2ID = $match["away"]["id"];
                $team1 = $teams[$team1ID]['name'];
                $team2 = $teams[$team2ID]['name'];
            ?>
            <div class="match">
                <h4>Date: <?= $match["date"]?> </h5>
                <h4><?=$team1?></h4> 
                <span>VS</span>
                <h4><?=$team2?></h4>
                <p>Score: <?= $match["home"]["score"]. ' - ' .$match["away"]["score"]?> </p>
            </div>
        <?php endforeach?>
    </div>
    
</body>
</html>