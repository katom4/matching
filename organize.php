<?php
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Illuminate\Database\Capsule\Manager as Capsule;
// Include the composer autoload file
require 'vendor/autoload.php';

$pdo=new PDO("mysql:host=localhost;dbname=sentinel;charset=utf8","sentineluser","pass", [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

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

if(Sentinel::getUser()->email!="kanri@kanri.com")
{
    header("location:/matching");
}

function organize()
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
            $sth = $pdo ->prepare("UPDATE profile set classid=:classid where id = $num");//classidのアップデート
            $sth -> bindValue(":classid",$classes[$num],PDO::PARAM_INT);
            $sth->execute();
        }
}
    

    if(isset($_POST['org']))
    {
        organize();
        //classlogに追加
        $xxx = $pdo->prepare("SELECT id,classid FROM profile order by id");
        $xxx->execute();
        $yyy = $pdo->prepare("SELECT season FROM classlog");
        $yyy->execute();
        $season=0;
        foreach($yyy as $row){
            if($season<=$row['season']){
                $season=$row['season']+1;
            }
        }
        foreach($xxx as $row){
            $sth = $pdo->prepare("INSERT INTO classlog(userid,season,classid) values(:userid,:season,:classid)");
            $sth->bindValue(":userid",$row['id'],PDO::PARAM_STR);
            $sth->bindValue(":season",$season,PDO::PARAM_STR);
            $sth->bindValue(":classid",$row['classid'],PDO::PARAM_STR);
            $sth->execute();
        }
        
    }

if(isset($_POST['submit']))
{
    $text=$_POST['text'];
    $sth = $pdo->prepare("INSERT INTO work(text) values(:text)");
    $sth->bindValue(":text",$text,PDO::PARAM_STR);
    $sth->execute();
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
    <form method ="post">
        <input type="submit" name="org" value="クラス編成">
    </form>
    <p></p>
    <p></p>
    <a href="/matching">トップに戻る</a>

    <form method="post" autocomplete="off" class="upWork">
        <input type="text" name="text"></input>
        <input type="submit" name="submit"></input>
    </form>
</body>
</html>