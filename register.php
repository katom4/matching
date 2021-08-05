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
    <h1>新規登録</h1>
    <form name="Register" method="post">
        <p>email</p><input type="text" name="email">
        <p>password</p><input type="password" name="password">
        <input type="submit" name='register'>
    </form>
    <a href="/matching/login.php">アカウント作成済みの方</a>
<?php

if(isset($_POST['register']))
{

    $email = $_POST['email'];
    $password = $_POST['password'];
    $user = Sentinel::registerAndActivate([
        'email'    => $email,
        'password' => $password
    ]);
    Sentinel::loginAndRemember($user);
    header("location:/matching");
}


?>

</body>
</html>
