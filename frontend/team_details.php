<?php 
    require_once(__DIR__ . "/../backend/utils/storage.inc.php");
    $teamsStorage   = new Storage(new JsonIO(__DIR__ . "/../backend/data/teams.json"));
    $matchesStorage = new Storage(new JsonIO(__DIR__ . "/../backend/data/matches.json"));

    $teams = $teamsStorage->findAll();
    $matches = $matchesStorage->findAll();
    $teamID = reset($_GET);

    function getTeamName($id){
        global $teamsStorage;
        return $teamsStorage->findById($id)['name'];
    }
    
    function matchColor($score1 , $score2){
        if($score1 > $score2) return '#66ff66';
        else if($score1 < $score2) return '#ff3333';
        return '#ffff1a';
    }

    $isLoggedIn = false;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/index.css">
    <title><?= $teams[$teamID]['name']?></title>
</head>
<body>
    <div class="team-rep">
        <img src="<?=$teams[$teamID]['logo']?>" alt="<?=$teams[$teamID]['name']?>logo" style="width:100%">
        <h1><?=$teams[$teamID]['name']?></h1>
    </div>
    <div class="matches-played">
       <h2>Matches played</h2>
       <table style="text-align:center;">
           <tr>
               <th>Data</th>
               <th>Match</th>
               <th>type</th>
               <th>Score</th>
           </tr>
            <?php foreach($matches as $id => $match):?>
                <?php if($match['home']['id'] == $teamID):?>
                    <tr style="background-color:<?= matchColor((int)$match['home']['score'] , (int)$match['away']['score']) ?>;">
                        <td><?=$match['date']?></td>
                        <td><?= getTeamName($match['home']['id']) . "   VS   " . getTeamName($match['away']['id'])?></td>
                        <td><?='home'?></td>
                        <td><?=$match['home']['score']?></td>
                    </tr>
                <?php endif?>
                <?php if($match['away']['id'] == $teamID):?>
                    <tr style="background-color:<?= matchColor((int)$match['away']['score'], (int)$match['home']['score'])?>;">
                        <td><?=$match['date']?></td>
                        <td><?=getTeamName($match['home']['id']) . "   VS   " . getTeamName($match['away']['id'])?></td>
                        <td><?='away'?></td>
                        <td><?=$match['away']['score']?></td>
                    </tr>
                <?php endif?>
            <?php endforeach?>
       </table>
    </div>

    <div class="comment">
        <h4>Add comment for the team: </h4>
        <textarea name="comment" id="comment" cols="30" rows="10" style="color: <?= $isLoggedIn ? 'black' : 'red' ?>;font-size: larger;"
            <?php if(!$isLoggedIn ):?>
                onclick="document.getElementById('comment').innerHTML='Login required to write a comment!'" 
                readonly
            <?php endif?>>
        </textarea><br>
        <button type="button" id="seewho">Submit</button>
    </div>

</body>
</html>