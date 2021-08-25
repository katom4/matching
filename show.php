<?php 
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Illuminate\Database\Capsule\Manager as Capsule;
include('base.php');
include('sentinelconfig.php');
session_start();
header('Expires:-1');
header('Cache-Control:');
header('Pragma:');

$sql = "SELECT max(season) FROM answer ORDER BY id;";
$sth = $pdo->prepare($sql);
$sth->execute();
$max=$sth->fetch()[0];
if(!isset($_POST['selSeason']))
{
    $selSeason=$max;
    $_SESSION['season']=$max;
}
else
{
    $selSeason=$_POST['selSeason'];
    $_SESSION['season']=$_POST['selSeason'];
}
$sql = "SELECT text FROM work where season=:season";
$sth = $pdo->prepare($sql);
$sth->bindValue(":season",$_SESSION['season'],PDO::PARAM_INT);
$sth->execute();
$text=$sth->fetch()[0];
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
    <link rel="stylesheet" href="chat.css">
</head>
<body>
<h1>SHOW</h1>
<div class="p-1 text-center">
    <h4><?=$text?></h4>
</div>

<form method="post">
<?php
    for($i=$_SESSION['season']+2;$i>0;$i--)
    {
        if($i>$max)continue;
        if($i==$selSeason){
?>

    <input type="submit" name="selSeason" value="<?= $i?>" class="btn btn-success w20" disabled>
<?php
        }
        else
        {
?>
    
    <input type="submit" name="selSeason" value="<?= $i?>" class="btn btn-success w20">
<?php
        }
        if($i<=$_SESSION['season']-2)break;
    }
?>
</form>
<div class="ontainer">
    <?php
    //DBから取得して表示する.
    $sql = "SELECT * FROM answer where season=:season ORDER BY id;";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(":season",$_SESSION['season'],PDO::PARAM_INT);
    $stmt -> execute();
    while ($row = $stmt -> fetch(PDO::FETCH_ASSOC)){
        echo("<div class='m-1 p-1 border border-success'>");
        echo ($row["text"]."<br>");
        //動画と画像で場合分け
        $target = $row["fname"];
        if($row["extension"] == "mp4"){
            echo ("<video src=\"import_answer.php?target=$target\" width=\"426\" height=\"240\" controls></video>");
        }
        elseif($row["extension"] == "jpeg" || $row["extension"] == "png" || $row["extension"] == "gif"){
            echo ("<img src='import_answer.php?target=$target '>");
        }
        echo ("<br/><br/>");
        echo("</div>");
    }
    ?>
</div>
</body>
</html>