<?php 
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Illuminate\Database\Capsule\Manager as Capsule;
include('base.php') ;
include('sentinelconfig.php');

//ログイン中かチェック
if ($user = Sentinel::getUser())
{
    //echo("<p>現在のユーザーid : {$user->id}</p>");
}
else
{
    header("location:/matching/login.php");
}

if(isset($_POST["chatsubmit"])&&$_POST['text']!="")
{
    $userid = Sentinel::getUser()->id;//今のuserid取得
    $classid = getProfile('classid');;
    $text = $_POST['text'];
    $sth=$pdo -> prepare("INSERT into chat(text,userid,classid) value(:text,:userid,:classid)");
    $sth ->bindValue(":text",$text,PDO::PARAM_STR);
    $sth ->bindValue(":userid",$userid,PDO::PARAM_INT);
    $sth ->bindValue(":classid",$classid,PDO::PARAM_INT);
    $sth->execute();
    header("location:/matching/chat.php");
}
$filename='chat';


if($classid==-1)
{
    header("location:/matching/profile.php");
}
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
    <div class="p-1">
        <h1>free</h1>
            <form method="post" autocomplete="off" class="m-2"><!-- class="text m-1"を追加することで下に固定できる-->
                <div class="input-group">
                    <input type="text" name="text" id="text" class="form-control m-1" placeholder="テキスト">
                    <input type="submit" name="chatsubmit" onclick="OnButtonClick()" class="btn btn-success m-1"/>
                </div>
            </form>
            <div class="pb-5 mb-5 mx-1" id="pa">
                <?php
                    //チャットの表示部分
                    $classid=getProfile('classid');
                    $pdo=new PDO("mysql:host=localhost;dbname=sentinel;charset=utf8","sentineluser","pass", [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
                    $sth = $pdo->prepare("SELECT * from chat where classid = :classid order by id desc");
                    $sth ->bindValue(":classid",$classid,PDO::PARAM_STR);
                    $sth->execute();
                    foreach($sth as $index => $row)
                    {
                        $sth = $pdo->prepare("SELECT nickname from profile where id = :userid");
                        $sth ->bindValue(":userid",$row['userid'],PDO::PARAM_INT);
                        $sth->execute();
                        $nickname = $sth->fetch()['nickname'];
                        $userid=$row['userid'];
                ?>
                <script type="text/javascript">
                    var selUserid='<?php echo $userid; ?>';
                </script>
                        <div class="container-fluid mt-2">
                                <div class="row">
                                    <form onclick="javascript:<?php echo('us'.$index)?>.submit()" name="<?php echo('us'.$index)?>" action="/matching/team.php" method="post" class="p-0 m-0">
                                        <p class="text-muted small m-0 mt-2 ml-1 yubi"><?=$nickname?></p>
                                        <input type="hidden" value="<?=$userid?>" name="n">
                                    </form>
                                </div>
                            <div class="row">
                                <div class="bg-light border rounded ml-1">
                                    <h6 class=' my-2 mx-2 '><?=$row['text']?></h6>
                                </div>
                            </div>
                        </div>
                        
                        
            <?php }
                ?>
            
        </div>
    </div>
</body>
</html>