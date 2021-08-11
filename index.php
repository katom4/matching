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

//----↑sentinelとの接続部分

session_start();
$pdo=new PDO("mysql:host=localhost;dbname=sentinel;charset=utf8","sentineluser","pass", [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

//ログアウト時の処理
if(isset($_GET['logout']))
{
    Sentinel::logout();
    unset($_SESSION["classid"]);
    header("location:/matching/login.php");
}

//ログイン中かチェック
if ($user = Sentinel::getUser())
{
    echo("<p>現在のユーザーid : {$user->id}</p>");
}
else
{
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
if(isset($_POST["submit"]))
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
    
    
}

//権限者のクラス編成用のページリンクを表示
if(Sentinel::getUser()->email=="kanri@kanri.com")
{
    echo('<a href="/matching/organize.php">クラス編成</a>');
}

//jsにclassidの値を渡す（チャットをクラスごとに同期するようにするため）
$classid=getProfile('classid');
$nickname=getProfile('nickname');

echo("classid:{$classid}");
?>


<script type="text/javascript">
var nickname='<?php echo $nickname; ?>';
var classid='<?php echo $classid; ?>';
</script>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="chat.js"></script>
</head>
<body>
    <h1>index.php</h1>
    <p><a href="/matching/index.php?logout=true">ログアウト</a></p>
    <p><a href="/matching/profile.php">プロフィール</a></p>

    <form method="post">
        <input type="text" name="text" id="text">
        <input type="submit" name="submit" onclick="OnButtonClick()"/>
    </form>
    <script type="text/javascript">
        
    </script>
    <div id="chat">
        <?php
            //チャットの表示部分
            $classid=getProfile('classid');
            echo("classid:{$classid}");
            $pdo=new PDO("mysql:host=localhost;dbname=sentinel;charset=utf8","sentineluser","pass", [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
            $sth = $pdo->prepare("SELECT * from chat where classid = :classid order by chatid desc");
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
            }
        ?>
    </div>
</body>
</html>

