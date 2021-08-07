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



function organize()
{
    
}
    

    if(isset($_POST['org']))
    {
        $pdo=new PDO("mysql:host=localhost;dbname=sentinel;charset=utf8","sentineluser","pass", [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        $sth = $pdo ->prepare("SELECT count(id) AS num FROM profile");
        $sth -> execute();
        
        $count = $sth->fetch()['num'];//ユーザーの数のカウント
        $classes = array();
        for($i=0 ;$i<$count ;$i++)
        {
            $classes[$i] = (int)($i/4);
        }
        if($count % 4 ==1&&$count>3)
        {
            $classes[$count-1] = ($count % 4)-1;
        }
        else if($count % 4 ==2&&$count>6)
        {
            $classes[$count-1] = ($count % 4)-1;
            $classes[$count-2] = ($count % 4)-2;
        }
        shuffle($classes);
        foreach($classes as $num => $row)
        {
            $sth = $pdo ->prepare("UPDATE profile set classid=:classid where id = $num");
            $sth -> bindValue(":classid",$classes[$num],PDO::PARAM_INT);
            $sth->execute();
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
    <?php
    if(Sentinel::getUser()->email=="kanri@kanri.com"){
    ?>
    <form method ="post">
        <input type="submit" name="org">
    </form>
    <?php } ?>
</body>
</html>