<?php
    //データファイルの読み込み
    $data_file = 'keijiban.txt';
    $ext = file_exists($data_file);
    $lines = $ext ? file($data_file) : array();
    $errMsg = "";
    date_default_timezone_set('Asia/Tokyo');

    //[書き込む]ボタンが押されたとき
    if(isset($_POST['submit'])) {
        //エラーならメッセージを設定
        if(empty($_POST['name'])){
            $errMsg = '名前を入力してください<br>';
        }
        elseif(empty($_POST['free'])){
            $errMsg = '記事を入力してください';
        }

        //エラーメッセージが設定されていなかったら新規データを追加
        if(!$errMsg){
            //タグなどを適用させない関数
            function convert_str($str){
                $str = htmlspecialchars($str);
                $str = preg_replace("/\r\n/","<br>",$str);
                $str = preg_replace("/\r|\n/","<br>",$str);
                return $str;
            }

            $ln = explode(",", $lines[0]);
            $no = $ext ? sprintf("%03d", $ln[0]+1) : "001";
            $name =convert_str($_POST['name']);
            $free =convert_str($_POST['free']);
            $delkey = !empty($_POST['delkey']) ? convert_str($_POST['delkey']) :
                                '#####';
            
            $time = date("Y/m/d h:i:s");

            //新規データをカンマ区切りテキストとし、配列の先頭に追加
            $data= "$no,$name,$free,$delkey,$time\n";
            array_unshift($lines, $data);
        }
    }

    //[削除]ボタンが押されたら、指定のNoと削除キーが
    //既存のデータとそれぞれ一致したものを探して削除
    if(isset($_POST['delbtn']) && $ext) {
        for($i = 0; $i <count($lines); $i++) {
            $ln = explode(",", $lines[$i]);
            if($ln[0] == $_POST['no'] && $ln[3] == $_POST['Rdkey']){
                array_splice($lines, $i, 1);
            break;
            }
        }
    }

    //[書き込む]または[削除]いずれかのボタンが押されていたら、
    //上記配列をファイルに書き込む
    if(isset($_POST['submit']) || isset($_POST['delbtn'])) {
        $fk = fopen($data_file, 'w');
        foreach($lines as $line)
            fputs($fk, $line);
        fclose($fk);
    }
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>タカチカの掲示板</title>
<link rel="stylrsheet" href="keijiban,css">
</head>
<body>
<div id="wrapper">
<form method="post" action="keijiban.php">
<div class="center">
    <div id="title">タカチカの掲示板</div>
    <p>
    No:<input type="text" name="no" size="5">
    削除キー:<input type="text" name="Rdkey" size="20">
    <input type="submit" name="delbtn" value="削除"
    </p>
</div>
<?php
    //エラーメッセージが設定されたとき表示
    if($errMsg)
        echo "<div id='errMsg'>".$errMsg."</div>";
?>

<div id="edit_area">
    <p>
    名前:<input type="text" name="name" size="26">
    削除キー:<input type="text" name="delkey" size="20">
    </p>
    <p>
    記事:<br><textarea name="free"></textarea>
    </p>
    <p class="center">
    <input type="submit" name="submit" value="書き込む">
    <input type="reset" value="取り消す"
    </p>
</div>

<?php
    //ファイルから一行ずつ読み込みテーブルにセットしていく
    foreach($lines as $line) {
        $ln = explode(",", $line);
        echo "<div><p class='entry_ID'>[No.".$ln[0]."]名前:".$ln[1]."&nbsp;";
        echo "書込み日付:".$ln[4]."</p>";
        echo "<p>".$ln[2]."</p>";
        echo "</div><hr>";
    }
?>

</form>
</div>
</body>
</html>
