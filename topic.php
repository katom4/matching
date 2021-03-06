<?php 
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Illuminate\Database\Capsule\Manager as Capsule;
include('base.php');
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

if(isset($_POST["topicsubmit"])&&$_POST['text']!="")
{
    echo("aaa");
    $userid = Sentinel::getUser()->id;//今のuserid取得
    //$userid = 7;//今のuserid取得
    $classid = getProfile('classid');;
    $text = $_POST['text'];
    $sth=$pdo -> prepare("INSERT into topic(text,userid,classid) value(:text,:userid,:classid)");
    $sth ->bindValue(":text",$text,PDO::PARAM_STR);
    $sth ->bindValue(":userid",$userid,PDO::PARAM_INT);
    $sth ->bindValue(":classid",$classid,PDO::PARAM_INT);
    $sth->execute();
    header("location:/matching/topic.php");
}
$filename='topic';

if(isset($_POST["answer"]))
{
    $pdo=new PDO("mysql:host=localhost;dbname=sentinel;charset=utf8","sentineluser","pass", [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $xxx = $pdo->prepare("SELECT * from work order by season");
    $xxx->execute();
    $season=0;
    foreach($xxx as $row){$season=$row['season'];}

    if(isset($_FILES['upfile']['error']) && is_int($_FILES['upfile']['error']) && $_FILES["upfile"]["name"] !== ""){
        $raw_data = file_get_contents($_FILES['upfile']['tmp_name']);
        //拡張子を見る
        $tmp = pathinfo($_FILES["upfile"]["name"]);
        $extension = $tmp["extension"];
        if($extension === "jpg" || $extension === "jpeg" || $extension === "JPG" || $extension === "JPEG"){
            $extension = "jpeg";
        }
        elseif($extension === "png" || $extension === "PNG"){
            $extension = "png";
        }
        elseif($extension === "gif" || $extension === "GIF"){
            $extension = "gif";
        }
        elseif($extension === "mp4" || $extension === "MP4"){
            $extension = "mp4";
        }
        else{
            echo "非対応ファイルです．<br/>";
            echo ("<a href=\"index.php\">戻る</a><br/>");
            exit(1);
        }
        $fname = $_FILES["upfile"]["tmp_name"];
        $classid = getProfile('classid');;
        $text = $_POST['text'];

        $sql = "REPLACE INTO answer(text,fname, extension, raw_data,season,classid) VALUES (:text,:fname, :extension, :raw_data,:season,:classid);";
        $stmt = $pdo->prepare($sql);
        $stmt -> bindValue(":text",$text, PDO::PARAM_STR);
        $stmt -> bindValue(":fname",$fname, PDO::PARAM_STR);
        $stmt -> bindValue(":extension",$extension, PDO::PARAM_STR);
        $stmt -> bindValue(":raw_data",$raw_data, PDO::PARAM_STR);
        $stmt ->bindValue(":season",$season,PDO::PARAM_INT);
        $stmt ->bindValue(":classid",$classid,PDO::PARAM_INT);
        $stmt -> execute();
        header("location:/matching/topic.php");
    }else{
    $classid = getProfile('classid');;
    $text = $_POST['text'];
    $sth=$pdo -> prepare("REPLACE  into answer(text,season,classid) value(:text,:season,:classid)");
    $sth ->bindValue(":text",$text,PDO::PARAM_STR);
    $sth ->bindValue(":season",$season,PDO::PARAM_INT);
    $sth ->bindValue(":classid",$classid,PDO::PARAM_INT);
    $sth->execute();
    header("location:/matching/topic.php");
    }
    
}
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
<h1>Topic</h1>
<?php 
    $pdo=new PDO("mysql:host=localhost;dbname=sentinel;charset=utf8","sentineluser","pass", [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $sth = $pdo->prepare("SELECT * from work order by season");
    $sth->execute();
    $topic="";
    foreach($sth as $row){$topic=$row['text'];}
    echo("<h2>{$topic}</h2>");
?>
    <!--topic専用入力フォーム-->
    <div class="border-top border-bottom mb-2 p-2">
        <p>＊クラスで課題が完了した方は下記のフォームに入力をお願いします</p>
        <form method="post" autocomplete="off" class="form-inline" enctype="multipart/form-data">
        
            <textarea  name="text" class="form-control w-100"  rows="9"></textarea> 
        
            <div class="custom-file mt-3 w-75">
                <input type="file" name="upfile" class="custom-file-input" id="inputFile">
                <label class="custom-file-label" for="inputFile" data-browse="ファイル">選択してください</label>
            </div>

            <div class="ml-auto">
                <input type="submit" name="answer" class="btn btn-primary mt-3">
            </div>
            
        </form>
    </div>

    <form method="post" autocomplete="off" class="m-2"><!-- class="text m-1"を追加することで下に固定できる-->
        <div class="input-group">
            <input type="text" name="text" id="text" class="form-control m-1" placeholder="テキスト">
            <input type="submit" name="topicsubmit" onclick="OnButtonClick()" class="btn btn-success m-1"/>
        </div>
    </form>
    <script type="text/javascript">
        
    </script>
    <div id="chat">
        <?php
            //チャットの表示部分
            $classid=getProfile('classid');
            $pdo=new PDO("mysql:host=localhost;dbname=sentinel;charset=utf8","sentineluser","pass", [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
            $sth = $pdo->prepare("SELECT * from topic where classid = :classid order by id desc");
            $sth ->bindValue(":classid",$classid,PDO::PARAM_STR);
            $sth->execute();
            foreach($sth as $row)
            {
                $sth = $pdo->prepare("SELECT nickname from profile where id = :userid");
                $sth ->bindValue(":userid",$row['userid'],PDO::PARAM_INT);
                $sth->execute();
                $nickname = $sth->fetch()['nickname'];
                ?>
                <div class="container-fluid mt-2">
                    <div class="row">
                        <p class="text-muted small m-0 mt-2 ml-1"><?=$nickname?></p>
                    </div>
                    <div class="row">
                        <div class="bg-light border rounded ml-1">
                            <h6 class=' my-2 mx-2 '><?=$row['text']?></h6>
                        </div>
                    </div>
                </div>
                <?php
            }
        ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.js"></script>
    <script>
    bsCustomFileInput.init();
    </script>
</body>
</html>