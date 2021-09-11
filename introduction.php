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

if(isset($_POST['n']))
{
    $selUserid=$_POST['n'];
}
else
{
    $selUserid=Sentinel::getUser()->id;
}

?>

<?php
    $pdo=new PDO("mysql:host=localhost;dbname=sentinel;charset=utf8","root","", [PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING]);
    $sth = $pdo->prepare("SELECT * FROM profile where id=:id");
    $sth->bindValue(":id",$selUserid,PDO::PARAM_INT);
    $sth->execute();
    $info=$sth->fetch();
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
    <div class="p-3">
        <div class="mb-2">
            <div>ニックネーム　</div>
            <div><?=$info['nickname']?></div>
        </div>
    </div>
</body>
</html>