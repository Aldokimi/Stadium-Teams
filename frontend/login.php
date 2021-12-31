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
        if(isset($input["username"] ) && (!$input['username'] == '')){
            $data['username'] = test_input($input["username"]);
        }else{
            $errors['username-required'] = "Username is required!";
        }
    
        if(isset($input["password"]) && (!$input['password'] == '')){
            $data['password'] = test_input($input["password"]);
        }else{
            $errors['password-required'] = "Password is required!";
        }
        return count($errors) === 0;
    }
      
    $errors = [];
    $data = [];
      
    if(validate($_POST, $data, $errors)){
        if(isset($_SESSION['user'])){
            unset($_SESSION['user']);
        }
        $user = $auth->authenticate($data['username'], $data['password']);
        if(!is_null($user)){
            $auth->login($user);
            header("Location: /index.php", true, 301);
            exit();
        }else{
            print(1);
            $errors['user-does-not-exisit'] = 'User does not exisit!';
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login</title>
    <link rel="stylesheet" href="style/auth.css">
</head>
<body>
    <div class="title">
        <h1>ELTE Stadium Web App</h1>
    </div>
    
    <div class="login">
        <div class="form">
            <form class="login-form" action="" novalidate method="post">
                <span class="logo">LOGIN</span>
                <h3 class="label">Email: </h3>
                <?php if(isset($data['username'])):?>
                    <input class="costum_input" type="text" id="username" name="username" value="<?=$data['username']?>" placeholder="e.g: Mohammed" required/>
                    <span class="error"><?= isset($errors['invalid-username']) ? $errors['invalid-username'] : 
                                            (isset($errors['username-required']) ? $errors['username-required'] : "" ) ?></span>
                <?php else:?>
                    <input class="costum_input" type="text" id="username" name="username" placeholder="Username" required/>
                <?php endif?>
                <h3 class="label">Passowrd: </h3>
                <input class="costum_input" type="password" id="pass" name="password" required/>
                <?php if(isset($data['password'])):?>
                    <span class="error"><?php if(isset($errors['password-required'])):?> <?=$errors['password-required']?> <?php endif?></span>
                <?php endif?>
                <input type="submit" class="btns" value="Login">
                <?php if(isset($errors['user-does-not-exisit'])):?> <span class="error"><?=$errors['user-does-not-exisit']?> </span><?php endif?>
                <button type="button" class="btns" onclick="location.href='./index.php';">main page</button>
                <button type="button" class="btns" onclick="location.href='./register.php';">register</button>
            </form>  
        </div>
    </div>  
</body>
</html>