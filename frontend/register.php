<?php
session_start();

require_once(__DIR__ . "/../backend/utils/storage.inc.php");
require_once(__DIR__ . "/../backend/utils/auth.inc.php");

$userStorage = new Storage(new JsonIO(__DIR__ . "/../backend/data/users.json"));
$auth = new Auth($userStorage);

if($auth->is_authenticated()){
    header("Location: ./index.php", true, 301);
    exit();
}

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function validate($input, &$data, &$errors){

    //validating username
    if(!isset($input["username"] ) || ($input['username'] == '')){
        $errors['username-required'] = "Username is required!";
    }else if (!preg_match("/^[a-zA-Z-' ]*$/", test_input($input["username"]) )) {
        $errors['invalid-username'] = "Invalid username, only letters and white space allowed!";
    }else{
        $data['username'] = test_input($input["username"]);
    }

    //validating email
    if(!isset($input["email"] ) || ($input['email'] == '')){
        $errors['email-required']  = "Email is required!";
    }else if (!filter_var(test_input($input["email"]), FILTER_VALIDATE_EMAIL)) {
        $errors['invalid-email']  = "Invalid email format!";
    }else{
        $data['email'] = test_input($input["email"]);
    }

    //validating password
    if(!isset($input["password"] ) || ($input['password'] == '')){
        $errors['password-required'] = "Password is required!";
    }
    if(!isset($input["password-confirmation"] ) || ($input['password-confirmation'] == '')){
        $errors['password-confirmation-required'] = "Password confirmation is required!";
    }else if(test_input($input["password"]) !== test_input($input["password-confirmation"])){
        $errors['password-not-same'] = "Both Passwords should be the same!";
    }else{
        $data["password"] = test_input($input["password"]);
        $data["password-confirmation"] = test_input($input["password-confirmation"]);
    }
    

    return count($errors) === 0;
}
    
$errors = [];
$data = [];
if($_POST){
    if(validate($_POST, $data, $errors)){
        $newData = [
            "username"  => $data['username'],
            "email" => $data['email'],
            "password"  => $data['password'], 
        ];
        if( !is_null($auth->register($newData)) ){
            header("Location: ./login.php");
            exit();
        }else{
            $errors['user-exists'] = "User already exisit, please login!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="style/auth.css"/>
</head>
<body>
    <div class="title">
        <h1>ELTE Stadium Web App</h1>
    </div>

    <div class="login">

        <div class="form">
            <form class="login-form" action="" method="post" novalidate>
                <span class="logo">REGISTER</span>

                <!-- Username  -->
                <h3 class="label">Username: </h3>
                <?php if(isset($data['username'])):?>
                    <input type="text" name="username" placeholder="e.g: dokimi" required value="<?=$data['username']?>"/>
                <?php else:?>
                    <input type="text" name="username" placeholder="e.g: dokimi" required/>
                <?php endif?>
                        
                <?php if(isset($errors['invalid-username'])):?> 
                    <div class="error"> 
                        <span><?=$errors['invalid-username']?></span> 
                    </div>
                <?php endif?>
                <?php if(isset($errors['username-required'])):?> 
                    <div class="error"> 
                        <span><?=$errors['username-required']?></span> 
                    </div>
                <?php endif?>

                <!-- Email  -->
                <h3 class="label">Email: </h3>
                <?php if(isset($data['email'])):?>
                    <input type="text" name="email" placeholder="e.g: email@something.com" required value="<?=$data['email']?>"/>                    
                <?php else:?>
                    <input type="text" name="email" placeholder="e.g: email@something.com" required/>
                <?php endif?>
                
                <?php if(isset($errors['invalid-email'])):?> 
                    <div class="error"> 
                        <span><?=$errors['invalid-email']?> </span> 
                    </div>
                <?php endif?>
                <?php if(isset($errors['email-required'])):?> 
                    <div class="error"> 
                        <span><?=$errors['email-required']?>
                    </div>
                </span> <?php endif?>

                <!-- Password  -->
                <h3 class="label">Password: </h3>
                <input type="password" name="password" placeholder="enter password" required/>

                <?php if(isset($errors['password-required'])):?> 
                    <div class="error"> 
                        <span><?=$errors['password-required']?></span> 
                    </div>
                <?php endif?>

                <!-- Password confirmation  -->
                <h3 class="label">Password Confirmation: </h3>
                <input type="password" name="password-confirmation" placeholder="enter password agin" required/>

                <?php if(isset($errors['password-confirmation-required'])):?> 
                    <div class="error"> 
                        <span><?=$errors['password-confirmation-required']?></span> 
                    </div>
                <?php endif?>
                <?php if(isset($errors['password-not-same'])):?> 
                    <div class="error"> 
                        <span><?=$errors['password-not-same']?></span> 
                    </div>
                <?php endif?>

                <input type="submit" class="btns" value="Register">
                <?php if(isset($errors['user-exists'])):?> 
                    <div class="error"> 
                        <span><?=$errors['user-exists']?></span> 
                    </div>
                <?php endif?>

                <button type="button" class="btns" onclick="location.href='./index.php';">main page</button>
                <button type="button" class="btns" onclick="location.href='./login.php';">login</button>
            </form>
        </div>
    </div>
</body>
</html>