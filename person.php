<?php 
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Illuminate\Database\Capsule\Manager as Capsule;
include('base.php') ;
include('sentinelconfig.php');
$pdo=new PDO("mysql:host=localhost;dbname=sentinel;charset=utf8","sentineluser","pass", [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
session_start();

if(isset($_POST['submit']))
{

}

$filename='chat';
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
    <?php
    //自分のidのseasonとclassidが一致するidを調べている
    $id = Sentinel::getUser()->id;
    $sth = $pdo->prepare("SELECT * from classlog where userid=:userid");
    $sth->bindValue(":userid",$id,PDO::PARAM_INT);
    $sth->execute();
    foreach($sth as $row)
    {
        $season=$row['season'];
        $classid=$row['classid'];
        $a = $pdo->prepare("SELECT * from classlog where season = :season AND classid = :classid");
        $a->bindValue(":season",$season,PDO::PARAM_INT);
        $a->bindValue(":classid",$classid,PDO::PARAM_INT);
        $a->execute();
        foreach($a as $u)
        {
            if($u['userid']==$id)
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
                
            }
        ?>
            <form method="post">
                <input type="hidden" name='partnerid' value="<?=$partnerid?>">
                <input type="submit" name="submit" value="<?=$partnerNick?>">
            </form>
    <?php
        }
    }
    ?>

</body>
</html>