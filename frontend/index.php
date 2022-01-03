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

$numberOfTeams = count($teams);

usort($matches, 'date_compare');
$matches = array_reverse($matches);

if(isset($_SESSION['user'])){
    $username = $_SESSION['user']['username'];
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

$extraMatches = [];
$moreMatches  = true;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/index.css">
        <style>
        .more-matches{
            position: absolute;
            top: <?= $isLoggedIn ? '320%' : '300%'?>;
            left: 20%;
            margin-bottom: 50px;
            width: 25%;
        }

        .liked-teams-matches{
            position: absolute;
            top: <?= $isLoggedIn ? '320%' : '300%'?>;
            left: 69.3%;
            margin-bottom: 50px;
            width: 25%;
        }
        .par-space{
            float: left;
            height: 3rem;
            width: 3rem;
        }

        .heart {
            width: 100px;
            height: 100px;
            background: url("https://cssanimation.rocks/images/posts/steps/heart.png") no-repeat;
            background-position: 0 0;
            cursor: pointer;
            transition: background-position 1s steps(28);
            transition-duration: 0s;
        }
        .heart.is-active {
            transition-duration: 1s;
            background-position: -2800px 0;
        }
        .placement {
            position:relative;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
    </style>
    <title>ELTE Stadium</title>
</head>
<body>
    <div class="auths" <?= $isLoggedIn ? 'hidden' : '' ?> >
        <div style="float:left; padding: 0.2rem;"><a id="seewho" href="login.php">Login</a></div>
        <div style="float:left; padding: 0.2rem;"><a id="seewho2" href="register.php">Register</a></div>
    </div>
    <div class="auths" <?= $isLoggedIn ? '' : 'hidden' ?> >
        <div style="float:left; padding: 0.2rem;"><a id="seewho" href="logout.php">Log out</a></div>
    </div>
    <div class="description">
        <h1>ELTE Stadium</h1>
        <p>Welcon to <strong>The ELTE Stadium</strong> web page, 
            where the matches played at them will appear, 
            and the fans are able to follow the 
            results of their favorite teams, give comments to the teams and enjoy.</p>
    </div>

    <!-- Teams -->
    <div class="teams" style="grid-template-rows: <?php for($i=0;$i<$numberOfTeams;$i++):?><?="1fr"?> <?php endfor?>; 
            grid-template-columns: <?php for($i=0;$i<$numberOfTeams;$i++):?><?="1fr"?> <?php endfor?>;">
        <?php foreach($teams as $id => $team):?>
            <?php if($isLoggedIn):?>
                <div class="placement">
                    <div class="heart" onclick="document.getElementsByClassName('.heart').toggleClass('is-active')"></div>
                </div>
            <?php endif?>
            <div class="team" onclick="location.href='./team_details.php?teadID=<?=$id?>';" style="padding: 1rem;"> 
                <img src="<?=$team["logo"]?>" alt="<?=$team["name"]?>logo" style="width:100%">
                <h4 style="padding: 0.3rem;"><b><?=$team["name"]?></b></h4>
                <p style="padding: 0.3rem;"><?=$team["city"]?></p> 
            </div>    
        <?php endforeach?>
    </div>

    <!-- Last 5 matches -->
    <div class="last-5-matches">
        <div class="container">
            <div class="matches" <?php if($isLoggedIn):?> style="top: 200%; !important" 
                                    <?php else:?>  style="top: 50%; !important"<?php endif?>>
                <div class="comments-container" style="background-color: rgb(106, 255, 138); text-align:center;">
                    <h3 style="font-size: 3rem;"> LAST 5 MATCHES</h3>
                </div>
                <?php $cnt = 0; foreach( $matches as $id => $match):?>
                    <?php if($cnt === 5) break; ?>
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
                <?php $cnt++; endforeach?>
            </div>
        </div>
    </div>
    <?php if($isLoggedIn):?>
        <!-- Show More 5 maches -->
        <button id="seewho" class="more-matches">SHOW MORE MATCHES</button>
        <!-- Show Maches of your liked teams! -->
        <Button id="seewho2" class="liked-teams-matches">SHOW MATCHES FOR LIKED TEAMS</Button>
        <script>
            document.querySelector('.more-matches').addEventListener('click', onClick)
            function onClick(e) {
                console.log(1);
                const xhr = new XMLHttpRequest()
                xhr.open('get', 'ajax.php?type=more')
                xhr.addEventListener('load', function () { console.log(this.response) })
                xhr.responseType = 'json'
                xhr.send(null);
            }
        </script>
        <!-- Display the extra matches -->
        <div class="container">
            <div class="matches" style="top: 350% !important; left: 20% !important">
                <div class="comments-container" style="background-color: rgb(106, 255, 138); text-align:center;">
                    <h3 style="font-size: 3rem;"> <?= $moreMatches ? 'MORE 5 MATCHES' : 'LIKED TEAMS MATCHES'?></h3>
                </div>
                <?php foreach( $extraMatches as $id => $match):?>
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

    <div class="par-space"></div>
</body>
</html>