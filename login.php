<?php
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Illuminate\Database\Capsule\Manager as Capsule;
// Include the composer autoload file
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
    echo("ss");
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
            echo("d");
        }
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
<h1>ログイン</h1>
<form name="login" method="post">
        <p>email</p><input type="email" name="email">
        <p>pass</p><input type="password" name="password">
        <input type="submit" name='login'>
</form>
<a href="/matching/register.php">新規登録</a>
</body>
</html>