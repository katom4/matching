<?php 
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Illuminate\Database\Capsule\Manager as Capsule;
include('base.php');
include('sentinelconfig.php');
if(isset($_POST["studysubmit"]))
{
    $userid = Sentinel::getUser()->id;//今のuserid取得
    echo("aaa");
    $classid = getProfile('classid');;
    $text = $_POST['text'];
    $sth=$pdo -> prepare("INSERT into study(text,userid,classid) value(:text,:userid,:classid)");
    $sth ->bindValue(":text",$text,PDO::PARAM_STR);
    $sth ->bindValue(":userid",$userid,PDO::PARAM_INT);
    $sth ->bindValue(":classid",$classid,PDO::PARAM_INT);
    $sth->execute();
    header("location:/matching/study.php");
    
}
$filename='study';
?>
<script type="text/javascript">
    var filename='<?php echo $filename ?>';
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
<h1>Study</h1>
<?php include "modeSelect.html" ?>
    <form method="post">
        <input type="text" name="text" id="text">
        <input type="submit" name="studysubmit" onclick="OnButtonClick()"/>
    </form>
    <script type="text/javascript">
        
    </script>
    <div id="chat">
        <?php
            //チャットの表示部分
            $classid=getProfile('classid');
            echo("classid:{$classid}");
            $pdo=new PDO("mysql:host=localhost;dbname=sentinel;charset=utf8","sentineluser","pass", [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
            $sth = $pdo->prepare("SELECT * from study where classid = :classid order by id desc");
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
</body>
</html>