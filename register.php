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
$regerror = 0;
if(isset($_POST['register']))
{

    $email = $_POST['email'];
    $password = $_POST['password'];

    $lname = $_POST['last_name'];
    $fname = $_POST['first_name'];
    if($lname==NULL||$fname==NULL)
    {
        $regerror=1;
    }
    else
    {
        $credentials=[
            'email'    => $email,
            'password' => $password,
            'first_name' => $fname,  
            'last_name' =>  $lname
        ];
        if(Sentinel::validForCreation($credentials))
        {
            $regerror=2;
        }
        else
        {
            $user = Sentinel::registerAndActivate($credentials);
            Sentinel::loginAndRemember($user);
            header("location:/matching");
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
    <h1>新規登録</h1>
    <?php if($regerror==1){?>
    <h2>名前を入力してください</h2>
    <?php } else if($regerror==2){?>
    <h2>すでに使われているメールアドレスです</h2>
    <?php } ?>
    <form name="Register" method="post">
        <p>email</p><input type="email" name="email">
        <p>password</p><input type="password" name="password">
        <p>苗字</p><input type="text" name="last_name">
        <p>名前</p><input type="text" name="first_name">
        <input type="submit" name='register'>
    </form>
    <a href="/matching/login.php">アカウント作成済みの方</a>
<?php


?>

</body>
</html>
