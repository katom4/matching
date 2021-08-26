<?php 
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Illuminate\Database\Capsule\Manager as Capsule;
// Include the composer autoload file
include("sentinelconfig.php");
include("base.php");
require 'vendor/autoload.php';
session_start();

$pdo=new PDO("mysql:host=localhost;dbname=sentinel;charset=utf8","sentineluser","pass", [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$regerror = 0;
$error=0;
if(empty($_GET))
{
    header("location:register.php");
}
else
{
    if(isset($_GET["urltoken"]))
    {
        try
        {
            $sth = $pdo->prepare("SELECT mail from pre_user where urltoken=(:urltoken) AND flag=0 AND date> now() - interval 24 hour");
            $sth->bindValue(':urltoken',$_GET['urltoken'],PDO::PARAM_STR);
            $sth->execute();

            //レコード件数取得
            $row_count = $sth->rowCount();
            if($row_count==1)
            {
                $mail_array = $sth->fetch();
				$mail = $mail_array["mail"];
				$_SESSION['mail'] = $mail;
            }
            else
            {
                //24時間以内に２回目のリクエストや、tokenが一致しない場合
                $error=1;
            }
        }
        catch(PDOException $e)
        {
            echo($e);
        }
    }
    else
    {
        header("location:register.php");
    }
}
if(isset($_POST['submit']))
{
    //クロスサイトフォージェリの対策
    if($_SESSION['token']!=$_POST['token'])
    {
        header("location:/matching/register.php");
        quit();
    }
    $password=$_POST['password'];
    if($password=='')
    {
        $regerror = 1;
    }
    else
    {
        $credentials=[
            'email'    => $_SESSION['mail'],
            'password' => $_POST['password'],
        ];
        $user = Sentinel::registerAndActivate($credentials);//登録してactibeにしている
        Sentinel::loginAndRemember($user);
        $userid = Sentinel::getUser()->id;
        $pdo=new PDO("mysql:host=localhost;dbname=sentinel;charset=utf8","sentineluser","pass", [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        //profileの初期設定
        $sth = $pdo->prepare("INSERT into profile(id) values(:id)");
        $sth->bindValue(":id",$userid,PDO::PARAM_INT);
        $sth -> execute();
        //flagを1にする（トークンの無効化）
        $a = $pdo->prepare("UPDATE pre_user SET flag=1 WHERE mail=:mail");
        $a->bindValue(":mail",$_SESSION['mail'],PDO::PARAM_STR);
        $a->execute();

        header("location:/matching/profile.php");
    }
}
$token = base64_encode(openssl_random_pseudo_bytes(32));
$_SESSION['token'] = $token;
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
<div class="text-center">
    <h1>メール認証</h1>
</div>
    <div class="border mx-2 mt-2 text-center">
        <?php if($regerror==1){?>
        <h2>未入力の部分があります</h2>
        <?php } else if($regerror==2){?>
        <h2>すでに使われているメールアドレスです</h2>
        <?php } ?>
        <?php if($error==0){?>
        <form action="<?php echo $_SERVER['SCRIPT_NAME'] ?>?urltoken=<?php print $urltoken; ?>" method="post" autocomplete="off">
            <p>メールアドレス：<?=htmlspecialchars($mail, ENT_QUOTES, 'UTF-8')?></p>
            <div class='form-group px-2 w-75 mx-auto'>
                <label for="pass" class="form-label">パスワード</label>
                <input type="password" name="password" class="form-control" id="password">
            </div>
            <input type="hidden" name='token' value="<?=$token?>">
            <div class="px-2">
                <input type="submit" name='submit' class="btn btn-success mx-4" value="登録">
            </div>
        </form>
        <?php }?>
    </div>
</body>
</html>


