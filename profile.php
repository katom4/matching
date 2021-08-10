//自分のステータス
//自分のステータスを入力
<?php

use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Illuminate\Database\Capsule\Manager as Capsule;
// Include the composer autoload file
require 'vendor/autoload.php';
$pdo=new PDO("mysql:host=localhost;dbname=sentinel;charset=utf8","root","", [PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING]);

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
if(isset($_GET['logout']))
{
    Sentinel::logout();
    header("location:/matching/login.php");
}
if ($user = Sentinel::getUser())
{
echo("<p>現在のユーザーid : {$user->id}</p>");
}
else{header("location:/matching/login.php");}


if(isset($_POST['submit']))
{
    $id=$user->id;
    $nickname=$_POST['nickname'];
    $japanese=$_POST['japanese'];
    $math=$_POST['math'];
    $social=$_POST['social'];
    $english=$_POST['english'];
    $science=$_POST['science'];
    $personality=$_POST['personality'];
    $hobby=$_POST['hobby'];

    $xxx = $pdo->prepare("SELECT id FROM profile where $id=id");
    $xxx->execute();
    $AAA=0;
    foreach($xxx as $raw){$AAA=1;}

    if($AAA==0){
        $sth = $pdo->prepare("INSERT INTO profile(id,japanese,math,social,english,science,personality,hobby,nickname) 
        values(:id, :japanese, :math, :social, :english, :science, :personality,:hobby,:nickname)");
        $sth->bindValue(":id",$id,PDO::PARAM_INT);
        $sth->bindValue(":japanese",$japanese,PDO::PARAM_STR);
        $sth->bindValue(":math",$math,PDO::PARAM_STR);
        $sth->bindValue(":social",$social,PDO::PARAM_STR);
        $sth->bindValue(":english",$english,PDO::PARAM_STR);
        $sth->bindValue(":science",$science,PDO::PARAM_STR);
        $sth->bindValue(":personality",$personality,PDO::PARAM_STR);
        $sth->bindValue(":hobby",$hobby,PDO::PARAM_STR);
        $sth->bindValue(":nickname",$nickname,PDO::PARAM_STR);
        $sth->execute();
    }else{
        $sth = $pdo->prepare("REPLACE INTO profile(id,japanese,math,social,english,science,personality,hobby,nickname) 
        values(:id, :japanese, :math, :social, :english, :science, :personality,:hobby,:nickname)");
        $sth->bindValue(":id",$id,PDO::PARAM_INT);
        $sth->bindValue(":japanese",$japanese,PDO::PARAM_STR);
        $sth->bindValue(":math",$math,PDO::PARAM_STR);
        $sth->bindValue(":social",$social,PDO::PARAM_STR);
        $sth->bindValue(":english",$english,PDO::PARAM_STR);
        $sth->bindValue(":science",$science,PDO::PARAM_STR);
        $sth->bindValue(":personality",$personality,PDO::PARAM_STR);
        $sth->bindValue(":hobby",$hobby,PDO::PARAM_STR);
        $sth->bindValue(":nickname",$nickname,PDO::PARAM_STR);
        $sth->execute();
    }
    
    
}

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
    <h1>profile</h1>
    <a href="/matching/index.php?logout=true">ログアウト</a>
    <a href="/matching">トップページに戻る</a>
    <h3>自分のプロフィールを入力してください</h3>
    <p>＊＊教科の欄は自分の能力を5段階で評価してください＊＊</p>
    <form method="post" autocomplete="off" class="toprofile">
        <lavel>ニックネーム：<br></lavel>
        <input type="text" name="nickname"></input><br>

        <lavel>国語の能力:
        <select name="japanese">
            <option value=1>1</option>
            <option value=2>2</option>
            <option value=3>3</option>
            <option value=4>4</option>
            <option value=5>5</option>
        </select><br></lavel>

        <lavel>数学の能力:
        <select name="math">
            <option value=1>1</option>
            <option value=2>2</option>
            <option value=3>3</option>
            <option value=4>4</option>
            <option value=5>5</option>
        </select><br></lavel>
        <lavel>社会の能力:
        <select name="social">
            <option value=1>1</option>
            <option value=2>2</option>
            <option value=3>3</option>
            <option value=4>4</option>
            <option value=5>5</option>
        </select><br></lavel>
        <lavel>英語の能力:
        <select name="english">
            <option value=1>1</option>
            <option value=2>2</option>
            <option value=3>3</option>
            <option value=4>4</option>
            <option value=5>5</option>
        </select><br></lavel>
        <lavel>理科の能力:
        <select name="science">
            <option value=1>1</option>
            <option value=2>2</option>
            <option value=3>3</option>
            <option value=4>4</option>
            <option value=5>5</option>
        </select><br></lavel>
        <lavel>性格タイプ:
        <select name="personality">
            <option value=1>おとなしい</option>
            <option value=2>頑張り屋</option>
            <option value=3>かっぱつ</option>
            <option value=4>人見知り</option>
            <option value=5>リーダー格</option>
        </select><br></lavel>

        趣味:<br>
        <input type="text" name="hobby"></input><br>
        <input type="submit" name="submit"></input>
    </form>
</body>
</html>