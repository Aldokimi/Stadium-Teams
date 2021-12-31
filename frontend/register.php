<?php
    session_start();
    
    require_once(__DIR__ . "/../backend/utils/storage.inc.php");
    require_once(__DIR__ . "/../backend/utils/auth.inc.php");

    $userStorage = new Storage(new JsonIO(__DIR__ . "/../backend/data/users.json"));
    $auth = new Auth($userStorage);

    if($auth->is_authenticated()){
        header("Location: /index.php", true, 301);
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
        if(isset($input["username"] ) && (!$input['username'] == '')){
            $data['username'] = test_input($input["fullname"]);
            if (!preg_match("/^[a-zA-Z-' ]*$/",$data['username'] )) {
              $errors['invalid-username'] = "Invalid username, only letters and white space allowed!";
            }
        }else{
            $errors['username-required'] = "Username is required!";
        }

        //validating email
        if(isset($input["email"] ) && (!$input['email'] == '')){
            $data['email'] = test_input($input["email"]);
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors['invalid-email']  = "Invalid email format!";
            }
        }else{
            $errors['email-required']  = "Email is required!";
        }

        //validating password
        if(isset($input["password"] ) && isset($input["password-confirmation"] ) 
            && (!$input['password'] == '') && (!$input['password-confirmation'] == '') ){
            $data["password"] = test_input($input["password"]);
            $data["password-confirmation"] = test_input($input["password-confirmation"]);
            if($data["password"] !== $data["password-confirmation"]){
                $errors['password-not-same'] = "Both Passwords should be the same!";
            }
        }

        return count($errors) === 0;
    }
        
    $errors = [];
    $data = [];
        
    if(validate($_POST, $data, $errors)){
        $newData = [
            "username"  => $data['username'],
            "email" => $data['email'],
            "password"  => $data['password'], 
        ];
        if( !is_null($auth->register($newData)) ){
            header("Location: /login.php");
            exit();
        }else{
            $errors['user-exists'] = "User already exisit, please login!";
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
            <form class="login-form" action="" novalidate method="post">
            <span class="logo">REGISTER</span>
                <h3 class="label">Username: </h3>
                <?php if(isset($data['username'])):?>
                    <input type="text" name="username" placeholder="e.g: dokimi" required value="<?=$data['username']?>"/>
                    <?php if(isset($errors['invalid-username'])):?> <span class="error"><?=$errors['invalid-username']?></span> <?php endif?>
                    <?php if(isset($errors['username-required'])):?> <span class="error"><?=$errors['username-required']?></span> <?php endif?>
                <?php else:?>
                    <input type="text" name="username" placeholder="e.g: dokimi" required/>
                <?php endif?>

                <h3 class="label">Email: </h3>
                <?php if(isset($data['email'])):?>
                    <input type="text" name="email" value="<?=$data['email']?>" placeholder="e.g: email@something.com" required/>
                    <?php if(isset($errors['invalid-email'])):?> <span class="error"><?=$errors['invalid-email']?></span> <?php endif?>
                    <?php if(isset($errors['email-required'])):?> <span class="error"><?=$errors['email-required']?></span> <?php endif?>
                <?php else:?>
                    <input type="text" name="email" placeholder="e.g: email@something.com" required/>
                <?php endif?>

                <h3 class="label">Password: </h3>
                <input type="password" name="pass" placeholder="enter password" required/>
                <h3 class="label">Password Confirmation: </h3>
                <input type="password" name="pass2" placeholder="enter password agin" required/>
                <?php if(isset($errors['password-not-same'])):?> <span class="error"><?=$errors['password-not-same']?></span> <?php endif?>

                <input type="submit" class="btns" value="Register">
                <?php if(isset($errors['user-exists'])):?> <span class="error"><?=$errors['user-exists']?></span> <?php endif?>

                <button type="button" class="btns" onclick="location.href='./index.php';">main page</button>
                <button type="button" class="btns" onclick="location.href='./login.php';">login</button>
            </form>
        </div>
    </div>
</body>
</html>