<?php 
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Illuminate\Database\Capsule\Manager as Capsule;
include('base.php') ;
include('sentinelconfig.php');
$pdo=new PDO("mysql:host=localhost;dbname=sentinel;charset=utf8","sentineluser","pass", [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
session_start();
//今は使わないのでchat.phpにリダイレクトしている
header("location:/matching/chat.php");

if(isset($_POST['choose']))
{
    $_SESSION['partnerid']=$_POST['partnerid'];
    $_SESSION['partnerNick']=$_POST['partnerNick'];
}
if(isset($_POST['personsubmit'])&&$_POST['text']!="")
{
    $userid = Sentinel::getUser()->id;//今のuserid取得
    $partnerid = $_SESSION['partnerid'];
    $text = $_POST['text'];
    $sth=$pdo -> prepare("INSERT into person(text,userid,partnerid) value(:text,:userid,:partnerid)");
    $sth ->bindValue(":text",$text,PDO::PARAM_STR);
    $sth ->bindValue(":userid",$userid,PDO::PARAM_INT);
    $sth ->bindValue(":partnerid",$partnerid,PDO::PARAM_INT);
    $sth->execute();
    header("location:/matching/person.php");
}

$filename='person';
?>
<script type="text/javascript">
    var filename='<?php echo $filename ?>';
    var userid = <?php echo Sentinel::getUser()->id ?>;
    var partnerid = <?php echo $_SESSION['partnerid'] ?>;
</script>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<h1>Chat</h1>
    <?php
    //自分のidのseasonとclassidが一致するidを調べている
    $id = Sentinel::getUser()->id;
    $sth = $pdo->prepare("SELECT * from classlog where userid=:userid");//まず自分のユーザーidのログをもってくる
    $sth->bindValue(":userid",$id,PDO::PARAM_INT);
    $sth->execute();
    $userArray=array();//検索する時に、同じユーザーがヒットするのを防ぐために、相手のユーザーidを配列で管理している
    foreach($sth as $row)
    {
        $season=$row['season'];//自分がいたseasonと
        $classid=$row['classid'];//自分がいたclassが対応している
        //自分がいたシーズンとクラスidが一致しているデータを取り出す（自分以外、重複なし）
        $a = $pdo->prepare("SELECT  userid from classlog
        where season = :season AND classid = :classid AND userid != :userid");
        $a->bindValue(":season",$season,PDO::PARAM_INT);
        $a->bindValue(":classid",$classid,PDO::PARAM_INT);
        $a->bindValue(":userid",$id,PDO::PARAM_INT);
        $a->execute();
        foreach($a as $u)
        {
            if(in_array($u['userid'],$userArray))//同じクラスになったことがある人が複数回あるときのチェック
            {
                continue;
            }
            else
            {
                $partnerid=$u['userid'];
                $n=$pdo->prepare("SELECT * from profile where id=:id");
                $n->bindValue(":id",$partnerid,PDO::PARAM_INT);
                $n->execute();
                $partnerNick=$n->fetch()['nickname'];
                array_push($userArray,$u['userid']);
            }
        ?>
            <form method="post">
                <input type="hidden" name='partnerid' value="<?=$partnerid?>">
                <input type="hidden" name='partnerNick' value="<?=$partnerNick?>">
                <input type="submit" name="choose" value="<?=$partnerNick?>">
            </form>
    <?php
        }
    }
    ?>
    <?php
    echo("<h2>to:{$_SESSION['partnerNick']}</h2>");
    ?>
    <form method="post">
        <input type="text" name="text" id="text">
        <input type="submit" name="personsubmit" value="送信" onclick="OnButtonClick()">
    </form>


    <div id="chat">
        <?php
            //チャットの表示部分
            if(isset($_SESSION['partnerid']))
            {
                $id=Sentinel::GetUser()->id;
                $pdo=new PDO("mysql:host=localhost;dbname=sentinel;charset=utf8","sentineluser","pass", [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
                $sth = $pdo->prepare("SELECT * from person where 
                    (userid=:userid AND partnerid=:partnerid) OR  (userid=:partnerid AND partnerid=:userid) 
                    order by id desc");
                $sth ->bindValue(":userid",$id,PDO::PARAM_STR);
                $sth ->bindValue(":partnerid",$_SESSION['partnerid'],PDO::PARAM_STR);
                $sth->execute();
                foreach($sth as $row)
                {
                    $sth = $pdo->prepare("SELECT nickname from profile where id = :userid");
                    $sth ->bindValue(":userid",$row['userid'],PDO::PARAM_INT);
                    $sth->execute();
                    $nickname = $sth->fetch()['nickname'];
                    echo("<p class='nickname'>{$nickname}</p>");
                    echo("<h3 class='chatchild'>{$row['text']}</h3>");
                }
            }
        ?>
    </div>
</body>
</html>