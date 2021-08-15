<?php
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Illuminate\Database\Capsule\Manager as Capsule;
// Include the composer autoload file
include("sentinelconfig.php");
require 'vendor/autoload.php';

session_start();
//クロスサイトリクエストフォージェリ（CSRF）対策


header('X-FRAME-OPTIONS: SAMEORIGIN');

$regerror = 0;
if(isset($_POST['register']))
{
    //クロスサイトリクエストフォージェリ（CSRF）対策
    if($_SESSION['token']!=$_POST['token'])
    {
        header("location:/matching/register.php");
        //クロスサイトリクエストフォージェリのとき、強制的にリロードするようにした
        //下のはいまのところ意味ない
        exit('不正です');
    }
    
    $email = $_POST['email'];
    $a=['email'=>$email,];
    
    if(Sentinel::findByCredentials($a)==NULL)
    {
        $urltoken = hash('sha256',uniqid(rand(),true));
        $url = "http://localhost/matching/signup.php?urltoken=".$urltoken;
        try
        {
            //dateにはデフォルトでcurrentを入れている
            $pdo=new PDO("mysql:host=localhost;dbname=sentinel;charset=utf8","sentineluser","pass", [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
            $sth = $pdo->prepare("INSERT into pre_user(urltoken,mail,flag) values(:urltoken,:mail,'0')");
            $sth->bindValue(':urltoken',$urltoken,PDO::PARAM_STR);
            $sth->bindValue(':mail',$email,PDO::PARAM_STR);
            $sth->execute();

            $_SESSION['register']='success';
            $_SESSION['urlkari']=$url;
            echo("<p></p>");
            echo($url);//ローカル環境でメールを送るのはめんどくさいので、メール送信の代わりに表示させている
            //本番ではメールを送って、echoを消す

            //login.phpに飛んでいる、リロードしたら変なことになるかも
            //header("location:/matching/login.php");


            /*mb_language("Japanese");
            mb_internal_encoding("UTF-8");
            $to = $email;
            $subject = "メール確認です";
            $message = "登録の確認メールです。以下のリンクにアクセスしてください\r\n{$url}";
            $headers = "From: test@test.com";
            $from = "i191313@gm.ishikawa-nct.ac.jp";
            $pfrom   = "-f $from";
            if(mb_send_mail($to, $subject, $message, $headers,$pfrom))
            {
                echo("<p>送信しました</p>");
            }
            else
            {
                echo("<p>失敗した</p>");
            }*/
        }
        catch(PDOException $e)
        {
            print('Error:'.$e->getMessage());
            die();
        }
        /*$user = Sentinel::registerAndActivate($credentials);//登録してactibeにしている
        Sentinel::loginAndRemember($user);
        $userid = Sentinel::getUser()->id;
        $pdo=new PDO("mysql:host=localhost;dbname=sentinel;charset=utf8","sentineluser","pass", [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        $sth = $pdo->prepare("INSERT into profile(id) values(:id)");
        $sth->bindValue(":id",$userid,PDO::PARAM_INT);
        $sth -> execute();
        header("location:/matching");*/
        
    }
    else
    {
        $regerror=2;
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
    <h1>新規登録</h1>
    <?php if($regerror==1){?>
    <h2>未入力の部分があります</h2>
    <?php } else if($regerror==2){?>
    <h2>すでに使われているメールアドレスです</h2>
    <?php } ?>
    <form name="register" method="post">
        <p>email</p><input type="email" name="email">
        <input type="hidden" name="token" value="<?=$_SESSION['token']?>">
        <input type="submit" name='register'>
    </form>
    <a href="/matching/login.php">アカウント作成済みの方</a>
<?php


?>

</body>
</html>
