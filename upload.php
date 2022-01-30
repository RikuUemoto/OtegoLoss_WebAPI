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

//json形式ファイルのheader
header("Content-Type: application/json; charset=utf-8");

// DBとの連携
try{
    // データベースに接続する．
    $db = new PDO('mysql:dbname=software;host=localhost;charset=utf8','root','root');
    echo "接続OK";

    // URL後の各クエリストリングをGET
    if (is_uploaded_file($_FILES["upfile"]["tmp_name"]) && isset($_POST['filename']) && $_POST['filename'] != '') {

        // 各クエリストリングをエスケープ(xss対策)
        $param_id = htmlspecialchars($_POST['filename']);
        $param_image = $param_id.".jpg";
        $param_imgpath = "./image/".$param_id.".jpg";

        // ユーザIDと商品IDの識別をするためidの左端の文字(u or g)を変数に格納
        $identifier = substr(htmlspecialchars($param_id), 0, 1);

        /* 変更する対象が存在するかどうか確認 */
        if($identifier == 'g') {
            $sql = "SELECT * FROM product WHERE product_id = :id";
        } else if ($identifier == 'u') {
            $sql = "SELECT * FROM user WHERE user_id = :id";
        }
        // クエリ(問い合わせ)
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $param_id, PDO::PARAM_STR);
        $stmt->execute();
        $count = $stmt->rowCount();
        if ($count == 0) {
            // データベースとの接続を切断．
            print_r($stmt->errorinfo());
            unset($db);
            die($param_proid.'の商品またはユーザは見つかりませんでした。');
        }
        echo $param_proid.'の商品またはユーザが'.$count.'件見つかりました。';

        /* 以下，サーバ上に画像をアップロードする処理 */
        if (move_uploaded_file($_FILES["upfile"]["tmp_name"], "./image/".$param_image)) {
            chmod("./image/".$param_image, 0644);
            echo $param_image."をアップロードしました!!!";
        }

        /* 以下，productテーブルのproduct_imageに画像ファイルのパスを登録する処理 */
        if($identifier == 'g') {
            $sql = "UPDATE product
                    SET product_image = :image
                    WHERE product_id = :id";
        } else if ($identifier == 'u') {
            $sql = "UPDATE user
                    SET user_profile_image = :image
                    WHERE user_id = :id";
        }
        $stmt = $db->prepare($sql);

        // パラメーターをセット
        $stmt->bindValue(':image', $param_imgpath, PDO::PARAM_STR);
        $stmt->bindValue(':id', $param_id, PDO::PARAM_STR);

        // dbにexecute
        $result = $stmt->execute();
        if (!$result) {
            // データベースとの接続を切断．
            print_r($stmt->errorinfo());
            unset($db);
            die('product_imageもしくはuser_profile_imageの登録に失敗しました。');
        }
        echo 'product_imageもしくはuser_profile_imageの登録が完了しました';

    } else {
        // paramの値が不適ならプログラム終了
        echo "ファイルが選択されていません。またはIDが指定されていません。";
    }

} catch(PDOException $e) {
    echo "error".$e->getMessage();
}

// データベースとの接続を切断．
unset($db);
?>