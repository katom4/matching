
<?php
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Illuminate\Database\Capsule\Manager as Capsule;
// Include the composer autoload file
require 'vendor/autoload.php';
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
    
    $subject=$_POST['subject'];
    $food=$_POST['food'];
    $club=$_POST['club'];
    $sex=$_POST['sex'];
    $occupation=$_POST['occupation'];
    $nickname=$_POST['nickname'];
    $next=$_POST['next'];

    $xxx = $pdo->prepare("SELECT id FROM profile");
    $xxx->execute();
    $AAA=0;
    foreach($xxx as $raw){if($raw['id']==$id){$AAA=1;}}

    if($AAA==0){
        $sth = $pdo->prepare("INSERT INTO profile(id,classid,subject,food,club,sex,occupation,nickname,next) 
        values(:id, :subject, :food, :club, :sex, :occupation, :nickname,:next)");
        $sth->bindValue(":id",$id,PDO::PARAM_INT);
        $sth->bindValue(":classid",$classid,PDO::PARAM_INT);//base.phpで取得している
        $sth->bindValue(":subject",$subject,PDO::PARAM_STR);
        $sth->bindValue(":food",$food,PDO::PARAM_STR);
        $sth->bindValue(":club",$club,PDO::PARAM_STR);
        $sth->bindValue(":sex",$sex,PDO::PARAM_STR);
        $sth->bindValue(":occupation",$occupation,PDO::PARAM_STR);
        $sth->bindValue(":nickname",$nickname,PDO::PARAM_STR);
        $sth->bindValue(":next",$next,PDO::PARAM_STR);
        $sth->execute();
    }else{
        $sth = $pdo->prepare("REPLACE INTO profile(id,classid,subject,food,club,sex,occupation,nickname,next) 
        values(:id, :classid,:subject, :food, :club, :sex, :occupation, :nickname,:next)");
        $sth->bindValue(":id",$id,PDO::PARAM_INT);
        $sth->bindValue(":classid",$classid,PDO::PARAM_INT);//base.phpで取得している
        $sth->bindValue(":subject",$subject,PDO::PARAM_STR);
        $sth->bindValue(":food",$food,PDO::PARAM_STR);
        $sth->bindValue(":club",$club,PDO::PARAM_STR);
        $sth->bindValue(":sex",$sex,PDO::PARAM_STR);
        $sth->bindValue(":occupation",$occupation,PDO::PARAM_STR);
        $sth->bindValue(":nickname",$nickname,PDO::PARAM_STR);
        $sth->bindValue(":next",$next,PDO::PARAM_STR);
        $sth->execute();
    }
    
    $sth=$pdo->prepare("SELECT count(id) as num from profile where classid = :classid and next = :next");
    $sth->bindValue(":classid",-1,PDO::PARAM_INT);
    $sth->bindValue(":next",1,PDO::PARAM_INT);
    $sth->execute();
    $count=$sth->fetch()['num'];
    if($count==4)
    {
        $sth=$pdo->prepare("SELECT max(classid) as max from profile");
        $sth->execute();
        $max=$sth->fetch()['max'];
        $sth = $pdo ->prepare("UPDATE profile set classid=:classid,next=:next 
            where classid=:classidWh and next = :nextWh");//classidのアップデート
        $sth->bindValue(":classid",$max+1,PDO::PARAM_INT);
        $sth->bindValue(":next",0,PDO::PARAM_INT);
        $sth->bindValue(":classidWh",-1,PDO::PARAM_INT);
        $sth->bindValue(":nextWh",1,PDO::PARAM_INT);
        $sth->execute();
    }
    header("location:/matching/profile.php");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <?php 
        $id=$user->id;
        $pdo=new PDO("mysql:host=localhost;dbname=sentinel;charset=utf8","root","", [PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING]);
        $xxx = $pdo->prepare("SELECT * FROM profile where id=$id");
        $xxx->execute();
        foreach($xxx as $raw){
            $raw_nickname=$raw['nickname'];
            $raw_subject=$raw['subject'];
            $raw_food=$raw['food'];
            $raw_club=$raw['club'];
            $raw_sex=$raw['sex'];
            $raw_occupation=$raw['occupation'];
            $raw_next=$raw['next'];
        }
    ?>
</head>
<body>
    <h1>profile</h1>
    <?php
    if($classid==-1&&getProfile("next")!=1)
    {
    ?>
    <div class="alert alert-warning m-2" role="alert">
        参加が未設定です！<br>クラスに参加したい場合は１番下のフォームで「参加する」を選択してください
    </div>
    <?php
    }
    if($classid==-1&&getProfile("next")==1)
    {
    ?>
    <div class="alert alert-primary m-2" role="alert">
        クラスの編成待ちです。しばらくお待ちください
    </div>
    <?php
    }
    ?>
    <form method="post" autocomplete="off" class="toprofile">
        <div class='form-group px-2'>
            <lavel for="nickname" class="form-label">ニックネーム<br></lavel>
            <input type="text" name="nickname" value="<?php echo($raw_nickname);?>" class="form-control" id="nickname"></input><br>
        </div>
        <div class="form-group  px-2">
            <lavel>性別
            <select name="sex" class="form-control">
                <option value=1>男</option>
                <option value=2>女</option>
            </select><br></lavel>
        </div>
        
        <div class="form-group  px-2">
            <lavel>得意教科
            <select name="subject" class="form-control">
                <option value=1>国語</option>
                <option value=2>数学</option>
                <option value=3>理科</option>
                <option value=4>社会</option>
                <option value=5>英語</option>
            </select><br></lavel>
        </div>
        
        <div class="form-group  px-2">
            <lavel>学生か社会人か
            <select name="occupation" class="form-control">
                <option value=1>学生</option>
                <option value=2>社会人</option>
            </select><br></lavel>
        </div>

        <div class='form-group px-2'>
            <lavel for="favfood" class="form-label">好きな食べ物<br></lavel>
            <input type="text" name="food" value="<?php echo($raw_food);?>" class="form-control" id="favfood"></input><br>
        </div>


        <div class='px-2'>
            <lavel for="club" class="form-label">部活動<br></lavel>
            <input type="text" name="club" value="<?php echo($raw_club);?>" class="form-control" id="club"></input><br>
        </div> 

        <div class="form-group  px-2">
            <lavel>
            <?php
            if($classid==-1){
            ?>
                クラスに参加しますか？
            <?php }
            else{
            ?>
                次のシーズンもクラスに参加しますか？
            <?php }
            ?>
            <select name="next" class="form-control">
                <option value=1>参加する</option>
                <option value=2>参加しない</option>
            </select><br></lavel>
        </div>

        <div class="px-2">
            <input type="submit" name="submit" class="btn btn-primary"></input>
        </div>
    </form>
</body>
</html>