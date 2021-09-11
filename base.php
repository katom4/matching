<?php
//ログイン処理をとかをまとめたやつ
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Illuminate\Database\Capsule\Manager as Capsule;
include('sentinelconfig.php');


$capsule->bootEloquent();

//----↑sentinelとの接続部分



session_start();

if ($user = Sentinel::getUser())
{
    $logName="ログアウト";
}
else
{
    $logName="ログイン";
}

$pdo=new PDO("mysql:host=localhost;dbname=sentinel;charset=utf8","sentineluser","pass", [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

//ログアウト時の処理
if(isset($_GET['logout']))
{
    Sentinel::logout();
    header("location:/matching/login.php");
}


//Profileの要素を取得する関数
function getProfile($e)
{
    $pdo=new PDO("mysql:host=localhost;dbname=sentinel;charset=utf8","sentineluser","pass", [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $sth = $pdo->prepare("SELECT * from profile where id = :id");//classidの取得
    $sth ->bindValue(":id",Sentinel::getUser()->id ,PDO::PARAM_INT);
    $sth->execute();
    $infos = $sth->fetch();
    return $infos[$e];
}



//チャット送信時の処理
/*if(isset($_POST["submit"]))
{
    $userid = Sentinel::getUser()->id;//今のuserid取得
    $classid = getProfile('classid');;
    $text = $_POST['text'];
    $sth=$pdo -> prepare("INSERT into chat(text,userid,classid) value(:text,:userid,:classid)");
    $sth ->bindValue(":text",$text,PDO::PARAM_STR);
    $sth ->bindValue(":userid",$userid,PDO::PARAM_INT);
    $sth ->bindValue(":classid",$classid,PDO::PARAM_INT);
    $sth->execute();
    header("location:/matching");
}*/

//権限者のクラス編成用のページリンクを表示
if(Sentinel::getUser()->email=="kanri@kanri.com")
{
    echo('<a href="/matching/organize.php">クラス編成</a>');
}

//jsにclassidの値を渡す（チャットをクラスごとに同期するようにするため）
$classid=getProfile('classid');
$nickname=getProfile('nickname');
$userid=Sentinel::getUser()->id;
//echo("classid:{$classid}");


//classidが１の人をprofileに誘導する関数


?>


<script type="text/javascript">
var nickname='<?php echo $nickname; ?>';
var classid='<?php echo $classid; ?>';
var userid='<?php echo $userid; ?>';
</script>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="chat.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="chat.css">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</head>
<body>
    <nav class="navbar navbar-expand-sm navbar-light cbg-green sticky-top fixed-top">
        <a class="navbar-brand font-weight-bold" href="/matching">えんえんかうんと</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle bg-green" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                        チャット
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                        <li><a class="dropdown-item" href="/matching/chat.php">free</a></li>
                        <li><a class="dropdown-item" href="/matching/game.php">game</a></li>
                        <li><a class="dropdown-item" href="/matching/topic.php">topic</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/matching/show.php">発表</a>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/matching/profile.php">プロフィール</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link"  href="/matching/index.php?logout=true"><?=$logName?></a>
                </li>
            </ul>
        </div>
    </nav>
    
    <?php //include "modeSelect.html" ?>
    <!--<form method="post">
        <input type="text" name="text" id="text">
        <input type="submit" name="submit" onclick="OnButtonClick()"/>
    </form>
    <script type="text/javascript">
        
    </script>
    -->
    
        <?php
            /*//チャットの表示部分
            $classid=getProfile('classid');
            echo("classid:{$classid}");
            $pdo=new PDO("mysql:host=localhost;dbname=sentinel;charset=utf8","sentineluser","pass", [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
            $sth = $pdo->prepare("SELECT * from chat where classid = :classid order by id desc");
            $sth ->bindValue(":classid",$classid,PDO::PARAM_STR);
            $sth->execute();
            foreach($sth as $row)
            {
                $sth = $pdo->prepare("SELECT nickname from profile where id = :userid");
                $sth ->bindValue(":userid",$row['userid'],PDO::PARAM_INT);
                $sth->execute();
                $nickname = $sth->fetch()['nickname'];
                echo("<p class='nickname'>{$nickname}</p>");
                echo("<h3 class='chatchild'>{$row['text']}</h3>");
            }*/
        ?>
    
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js" integrity="sha384-q2kxQ16AaE6UbzuKqyBE9/u/KzioAlnx2maXQHiDX9d4/zp8Ok3f+M7DPm+Ib6IU" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.min.js" integrity="sha384-pQQkAEnwaBkjpqZ8RU1fF1AKtTcHJwFl3pblpTlHXybJjHpMYo79HY3hIi4NKxyj" crossorigin="anonymous"></script>
</body>
</html>

