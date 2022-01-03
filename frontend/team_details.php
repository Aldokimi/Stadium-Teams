<?php 
session_start();
$teamID = reset($_GET);


require_once(__DIR__ . "/../backend/utils/storage.inc.php");
require_once(__DIR__ . "/../backend/utils/auth.inc.php");

$userStorage     = new Storage(new JsonIO(__DIR__ . "/../backend/data/users.json"  ));
$teamsStorage    = new Storage(new JsonIO(__DIR__ . "/../backend/data/teams.json"  ));
$matchesStorage  = new Storage(new JsonIO(__DIR__ . "/../backend/data/matches.json"));
$commentsStorage = new Storage(new JsonIO(__DIR__ . "/../backend/data/comments.json"));

$auth = new Auth($userStorage);

$user       = $auth->authenticated_user();
$teams      = $teamsStorage->findAll();
$matches    = $matchesStorage->findAll();
$comments   = $commentsStorage->findAll();
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

// handeling new comments
function validate($input, &$data, &$errors){
    if(isset($input['comment']) && $input['comment'] != ''){
        $data['text'] = $input['comment'];
    }else{
        $errors['empty-comment'] = 'You cannot submit empty comment!';
    }
    return count($errors) === 0;
}
  
$errors = [];
$data = [];

if($_POST){
    if(validate($_POST, $data, $errors)){
        $data['author'] = $user['username'];
        $data['teamid'] = $teamID;
        $data['time'] = date('Y/m/d H:i:s');
        $commentsStorage->add($data);
        header("Location: ./team_details.php?teamID=". $teamID, true, 301);
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
    <title><?= $teams[$teamID]['name']?></title>
</head>
<body>
    <div class="team-rep">
        <img src="<?=$teams[$teamID]['logo']?>" alt="<?=$teams[$teamID]['name']?>logo" style="width:100%">
        <h1><?=$teams[$teamID]['name']?></h1>
    </div>
    <div class="btns" >
        <button type="button" id="seewho" onclick="location.href='./index.php';">MAIN PAGE</button>
    </div>
    <div class="container">
        <div class="matches">
            <?php foreach($matches as $id => $match):?>
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
                                    <?php if(isAdmin()):?>
                                        <button type="button" id="seewho" onclick="location.href='./modify.php?id=<?=$match['id']?>&teamID=<?=$teamID?>';">MODIFY</button>
                                    <?php endif?> <br>
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
                                    <?php if(isAdmin()):?>
                                        <button type="button" id="seewho" onclick="location.href='./modify.php?id=<?=$match['id']?>&teamID=<?=$teamID?>';">MODIFY</button>
                                    <?php endif?> <br>
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
            <?php endforeach?>

            <div class="comments">
                <div class="comments-container">
                    <h1>COMMENTS</h1>

                    <ul id="comments-list" class="comments-list">
                        <?php foreach($comments as $comment):?>
                            <?php if($comment['teamid'] == $teamID):?>
                                <li>
                                    <div class="comment-main-level">
                                        <div class="comment-avatar"><img src="http://i9.photobucket.com/albums/a88/creaticode/avatar_1_zps8e1c80cd.jpg" alt=""></div>
                                        <div class="comment-box">
                                            <div class="comment-head">
                                                <h6 class="comment-name by-author"><?=$comment['author']?></h6>
                                                <span><?=$comment['time']?></span>
                                                <i class="fa fa-reply"></i>
                                                <i class="fa fa-heart"></i>
                                            </div>
                                            <div class="comment-content">
                                                <?=$comment['text']?>
                                            </div>
                                            <div>
                                                <?php if(isAdmin()):?>
                                                    <button type="button" id="seewho" onclick="location.href='./delete.php?id=<?= $comment['id']?>&teamID=<?=$teamID?>';">DELETE</button>
                                                <?php endif?> <br>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            <?php endif?>
                        <?php endforeach?>
                    </ul>
                </div>
            </div>
            <div class="add-comment-container">
                <form class="login-form" action="" method="post" novalidate>
                    <fieldset>
                        <div class="form_grp">
                            <label>comment</label>
                            <textarea name="comment" id="comment" cols="30" rows="10"  placeholder="Add a comment..."
                                      style="color: <?= $isLoggedIn ? 'black' : 'red' ?>;font-size: larger;"
                                <?php if(!$isLoggedIn ):?>
                                      onclick="document.getElementById('comment').innerHTML='Login is required to write a comment!'" 
                                      readonly
                                <?php endif?>></textarea>       
                        </div>
                        <?php if($isLoggedIn):?>
                            <div class="form_grp">
                                <button type="submit" id="seewho2">Submit</button>
                                <?php if(isset($errors['empty-comment'])):?> 
                                    <div class="error"> 
                                        <span ><?=$errors['empty-comment']?> </span>
                                    </div>
                                <?php endif?>
                            </div>
                        <?php endif?>
                    </fieldset>
                </form>  
                
            </div>

        </div>
    </div>
    <div class="par-space"></div>
</body>
</html>