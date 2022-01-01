<?php 
session_start();
$matchID = reset($_GET);


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
$homeTeam  = $teamsStorage->findById($matchesStorage->findById($matchID)['home']['id'])['name'] ;
$awayTeam  = $teamsStorage->findById($matchesStorage->findById($matchID)['away']['id'])['name'] ;
$homeScore = $matchesStorage->findById($matchID)['home']['score'] ;
$awayScore = $matchesStorage->findById($matchID)['away']['score'] ;
$date      = $matchesStorage->findById($matchID)['date'];

$timeToModify = false;

print_r($_GET);


function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function validate($input, &$data, &$errors){
    
    return count($errors) === 0;
}
  
$errors = [];
$data = [];
  
if(validate($_GET, $data, $errors)){
    
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/auth.css">
    <title>admin page</title>
</head>
<body>
    <div class="title">
        <h1>ELTE Stadium Web App</h1>
    </div>

    <div class="login" <?= !$timeToModify ? '' : 'hidden' ?>>
        <div class="form">
            <form class="login-form" action="" method="get" novalidate>
                <span class="logo">MODIFY MATCH <strong><?=$matchID?></strong></span>
                <div class="modify-container">
                    <!-- HOME -->
                    <h2 class="label">home: </h2>

                    <select name="home-team" id="cars">
                        <option value="choose" selected="selected" disabled>CHOOSE TEAM</option>
                        <?php foreach($teams as $team):?>
                            <option value="<?=$team['id'];?>"><?=$team['name'];?></option>
                        <?php endforeach?>
                    </select>

                    <?php if(isset($data['home-score'])):?>
                        <input type="number" id="home-score" name="home-score" value="<?=$data['home-score']?>" placeholder="score"/>
                    <?php else:?>
                        <input type="number" id="home-score" name="home-score" placeholder="score" />
                    <?php endif?>

                    <!-- AWAY -->
                    <h2 class="label">away: </h2>
                        
                    <select name="away-team" id="cars">
                        <option value="none" selected="selected" disabled>CHOOSE TEAM</option>
                        <?php foreach($teams as $team):?>
                            <option value="<?=$team['id'];?>"><?=$team['name'];?></option>
                        <?php endforeach?>
                    </select>

                    <?php if(isset($data['away-score'])):?>
                        <input type="number" id="away-score" name="away-score" value="<?=$data['away-score']?>" placeholder="score"/>
                    <?php else:?>
                        <input type="number" id="away-score" name="away-score" placeholder="score" />
                    <?php endif?>

                    <!-- DATE -->
                    <h2 class="label">date: </h2>
                    <?php if(isset($data['away-date'])):?>
                        <input type="date" id="away-date" name="away-date" value="<?=$data['away-date']?>" placeholder="score"/>
                    <?php else:?>
                        <input type="date" id="date" name="away-date" placeholder="date" />
                    <?php endif?>
                <button type="submit" class="btns">modify</button>
                </div>
            </form>  
        </div>
    </div>

    <div class="login"  <?= $timeToModify ? '' : 'hidden' ?>>
        <div class="form">
            <form class="login-form" action="" method="post" novalidate>
                <span class="logo">MODIFY MATCH</span>
                
                <!-- Username -->
                <h3 class="label">Username: </h3>
                <?php if(isset($data['username'])):?>
                    <input class="costum_input" type="text" id="username" name="username" value="<?=$data['username']?>" placeholder="e.g: Mohammed" required/>
                <?php else:?>
                    <input class="costum_input" type="text" id="username" name="username" placeholder="Username" required/>
                <?php endif?>

                <?php if(isset($errors['invalid-username'])):?>
                    <div class="error">
                        <span ><?=$errors['invalid-username']?></span>
                    </div>
                <?php endif?>
                <?php if(isset($errors['username-required'])):?>
                    <div class="error"> 
                        <span ><?=$errors['username-required']?></span>
                    </div>
                <?php endif?>
                        
                <!-- Password -->
                <h3 class="label">Passowrd: </h3>
                <input class="costum_input" type="password" id="pass" name="password" required/>

                <?php if(isset($errors['password-required'])):?>
                    <div class="error"> 
                        <span ><?=$errors['password-required']?></span>
                    </div>
                <?php endif?>

                <input type="submit" class="btns" value="modify">
                <?php if(isset($errors['user-does-not-exisit'])):?> 
                    <div class="error"> 
                        <span ><?=$errors['user-does-not-exisit']?> </span>
                    </div>
                <?php endif?>

                <button type="button" class="btns" onclick="location.href='./index.php';">main page</button>
            </form>  
        </div>
    </div>
    <script src="./js/admin.js"></script>
</body>
</html>