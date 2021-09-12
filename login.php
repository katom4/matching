<?php
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Illuminate\Database\Capsule\Manager as Capsule;
// Include the composer autoload file
include('base.php');
require 'vendor/autoload.php';

// Setup a new Eloquent Capsule instance
$capsule = new Capsule;

$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => 'localhost',
    'database'  => 'sentinel',
    'username'  => 'sentineluser',
    'password'  => 'pass',
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
]);


$capsule->bootEloquent();
session_start();

if (Sentinel::getUser())
{
    header("location:/matching");
}

if(isset($_POST['login']))
{
    $email = $_POST['email'];
    $password = $_POST['password'];

    $credentials = [  
        'email'  => $email,  
        'password' => $password,  
      ];

    $userid = (int)Sentinel::getUserRepository()->findByCredentials($credentials)->id;
    $user = Sentinel::findUserById($userid);
    if($user!=NULL)
    {
        if(Sentinel::validateCredentials($user, $credentials))
        {
            Sentinel::loginAndRemember($user);
            $_SESSION=array();
            header("Location:/matching");
        }
        else
        {
            echo("
                <div class='alert alert-warning border m-1' role='alert'>
                入力内容が間違っています！
                </div>
            ");
        }
    }
    else
    {
        echo("
                <div class='alert alert-warning border m-1' role='alert'>
                入力内容が間違っています！
                </div>
            ");
    }
   
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<div class="text-center">
    <h1>ログイン</h1>
</div>

<div class="border mx-2 text-center mx-auto w-75">
    <form name="login"  method="post" autocomplete="off">
        <div class='form-group px-2 w-75 mx-auto'>
            <label for="email" class="form-label">email</label>
            <input type="email" name="email"  class="form-control" id="email">
        </div>
        
        <div class='form-group px-2 w-75 mx-auto'>
            <label for="pass" class="form-label">password</label>
            <input type="password" name="password" class="form-control" id="password">
        </div>
        <div class="px-2">
            <a href="/matching/register.php" class="mx-2">新規登録はこちら</a>
            <input type="submit" name='login' class="btn btn-success mx-4" value="ログイン">
        </div>
    </form>
</div>

</body>
</html>