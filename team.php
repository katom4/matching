<?php
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Illuminate\Database\Capsule\Manager as Capsule;
include('base.php') ;
include('sentinelconfig.php');
header('Expires:-1');
header('Cache-Control:');
header('Pragma:');
//ログイン中かチェック
if ($user = Sentinel::getUser())
{
    //echo("<p>現在のユーザーid : {$user->id}</p>");
}
else
{
    header("location:/matching/login.php");
}

$pdo=new PDO("mysql:host=localhost;dbname=sentinel;charset=utf8","sentineluser","pass", [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$mode=0;
if(isset($_POST['n']))
{
    $selUserid=$_POST['n'];
    $mode=1;
}
else
{
    $selUserid=Sentinel::getUser()->id;
}
?>

<?php
    
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
    <?php if($mode==0){
        $sth = $pdo->prepare("SELECT * FROM profile where classid=:classid and id!=:id");
        $sth->bindValue(":classid",$classid,PDO::PARAM_INT);
        $sth->bindValue(":id",$userid,PDO::PARAM_INT);
        $sth->execute();
        
        foreach($sth as $row){
        ?>
        <div>
            <div class="p-3">
                <div class="mb-2">
                    <form name="<?php echo('us'.$row['id'])?>" action="/matching/team.php" method="post" class="p-0 m-0">
                        <a href="javascript:<?php echo('us'.$row['id'])?>.submit()"><?=$row['nickname']?></a>
                        <input type="hidden" value="<?=$row['id']?>" name="n">
                    </form>
                </div>
            </div>
        </div>
        <?php }
            } ?>
    <?php if($mode==1){
        $sth = $pdo->prepare("SELECT * FROM profile where id=:id");
        $sth->bindValue(":id",$selUserid,PDO::PARAM_INT);
        $sth->execute();
        $info=$sth->fetch();
        ?>
        <div class="p-3">
            <div class="mb-2">
                <div>ニックネーム　</div>
                <div>
                    <?=$info['nickname']?>
                </div>
                <div><a href="">メンバー一覧</a></div>
            </div>
        </div>
    <?php }?>
</body>
</html>