<?php
/*
    作成者：植元 陸
    最終更新日：2022/1/29
    目的：  画像をアップロードする
    入力：  
    http通信例：
    https://ec2-13-114-108-27.ap-northeast-1.compute.amazonaws.com/
    その他："./image/".$_FILES["upfile"]["name"]
*/

/* アップロードされたファイル情報の確認
print_r($_FILES['upfile']);
echo $_FILES['upfile']['name']."<br/>\n";
echo $_FILES['upfile']['type']."<br/>\n";
echo $_FILES['upfile']['tmp_name']."<br/>\n";
echo $_FILES['upfile']['error']."<br/>\n";
echo $_FILES['upfile']['size']."<br/>\n";
*/

if (is_uploaded_file($_FILES["upfile"]["tmp_name"]) && isset($_POST['filename']) && $_POST['filename'] != '') {

    $filename = htmlspecialchars($_POST['filename']);
    if (move_uploaded_file($_FILES["upfile"]["tmp_name"], "./image/".$filename)) {
        chmod("./image/".$filename, 0644);
        echo $filename."をアップロードしました!!!";
    }
} else {
    echo "ファイルが選択されていません。またはファイル名が指定されていません。";
}
?>