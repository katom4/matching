<?php 
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Illuminate\Database\Capsule\Manager as Capsule;
include('base.php');
include('sentinelconfig.php');

$sql = "SELECT max(season) FROM answer ORDER BY id;";
$sth = $pdo->prepare($sql);
$sth->execute();
$max=$selSeason=$sth->fetch()[0];
$max=7;
if(!isset($_POST['selSeason']))
{
    $selSeason=$max;
}
else
{
    $selSeason=$_POST['selSeason'];
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
<h1>SHOW</h1>


<form method="post">
<?php
    for($i=$selSeason+2;$i>0;$i--)
    {
        if($i>$max)continue;
?>
    <input type="submit" name="selSeason" value="<?= $i?>">
    <?php
    if($i<=$selSeason-2)break;
    }
    ?>
</form>
<?php
    //DBから取得して表示する.
    $sql = "SELECT * FROM answer where season=:season ORDER BY id;";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(":season",$selSeason,PDO::PARAM_INT);
    $stmt -> execute();
    while ($row = $stmt -> fetch(PDO::FETCH_ASSOC)){
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
    }
    ?>

</body>
</html>