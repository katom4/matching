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
    $sth = $pdo ->prepare("SELECT * FROM profile");
    $sth -> execute();

    $users=array();
    foreach($sth as $num => $row)
    {
        if($row['next']==1)$users[$num]=1;
        else $users[$num]=0;
    }
    $s=$num;

    $c = $pdo ->prepare("SELECT count(id) AS num FROM profile where next=:n");
    $c->bindValue(":n",1,PDO::PARAM_INT);
    $c -> execute();

    $m = $pdo ->prepare("SELECT max(classid) AS max FROM profile");
    $m->execute();
    $max=$m->fetch()['max']+1;//クラス編成の時に、最大値+1を足すことでクラスがどんどん増えていく形にする

    $count=$c->fetch()['num'];//変更する人の総数
    $classes = array();
    for($i=0 ;$i<$count ;$i++)
    {
        $classes[$i] = (int)($i/4)+$max;
    }
    if($count % 4 ==1&&$count>3)
    {
        $classes[$count-1] = 0+$max;
    }
    else if($count % 4 ==2)
    {
        $classes[$count-1] = 0+$max;
        $classes[$count-2] = 1+$max;
    }
    if($count==6)//六人の時だけ特殊な並び替え
    {
        $classes[$count-1] = 1+$max;
        $classes[$count-2] = 1+$max;
        $classes[$count-3] = 1+$max;
    }
    shuffle($classes);
    $ccount=0;//変更する人を数える変数、$classesの適切な取得に必要
    for($num=0;$num<$s+1;$num++)
    {
        if($users[$num]==1)
        {
            $sth = $pdo ->prepare("UPDATE profile set classid=:classid,next=:next where id = $num+1");//classidのアップデート
            //idは１からの連番だが、$numは０からの連番になっているため、上で１足している
            $sth -> bindValue(":classid",$classes[$ccount],PDO::PARAM_INT);
            $sth -> bindValue(":next",0,PDO::PARAM_INT);
            $sth->execute();
            $ccount++;
        }
        else
        {
            $sth = $pdo ->prepare("UPDATE profile set classid=:classid,next=:next where id = $num+1");//classidのアップデート
            //idは１からの連番だが、$numは０からの連番になっているため、上で１足している
            $sth -> bindValue(":classid",-1,PDO::PARAM_INT);
            $sth -> bindValue(":next",0,PDO::PARAM_INT);
            $sth->execute();
        }
    }
}
    if(isset($_POST['org']) && $_POST['text']!="")
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
        $text=$_POST['text'];
        $sth = $pdo->prepare("INSERT INTO work(text) values(:text)");
        $sth->bindValue(":text",$text,PDO::PARAM_STR);
        $sth->execute();
        header("location:/matching/organize.php");
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
        <input type="text" name="text"></input>
        <input type="submit" name="org" value="クラス編成">
    </form>
    <p></p>
    <p></p>
    <a href="/matching">トップに戻る</a>

</body>
</html>