<?php
/*
    作成者：植元 陸
    最終更新日：2022/1/18
    目的：  コメントテーブルにコメントを追加
    入力：  product_id, user_id, comment_body
    http通信例：
    http://localhost/OtegoLoss_WebAPI/Comment/InsertComment.php?product_id=g0000008&user_id=u0000100&comment_body=A12345678910JQK
    
    その他：
*/

#ステータスコードを追記する必要あり
//エラーリポート
error_reporting(E_ALL);
//json形式ファイルのheader
header("Content-Type: application/json; charset=utf-8");

// DBとの連携
try{
    // データベースに接続する．
    $db = new PDO('mysql:dbname=software;host=localhost;charset=utf8','root','root');
    echo "接続OK";


    // URL後の各クエリストリングをGET
    if(isset($_POST["product_id"]) && isset($_POST["user_id"]) 
                                    && isset($_POST["comment_body"])) {

        // 各クエリストリングをエスケープ(xss対策)
        $param_proid = htmlspecialchars($_POST["product_id"]);
        $param_usrid = htmlspecialchars($_POST["user_id"]);

        // comment_bodyは空文字列を許さない
        if($_POST['comment_body'] == '') {
            // データベースとの接続を切断．
            unset($db);
            die('コメントが何も入力されていません．コメントを入力してください．');
        }

        /* 最新のコメントIDを取得 */
        $sql = "SELECT comment_id FROM comment WHERE product_id = :product_id ORDER BY comment_id DESC LIMIT 1";
        // クエリ(問い合わせ)
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':product_id', $param_proid, PDO::PARAM_STR);
        $result = $stmt->execute();
        if (!$result) {
            // データベースとの接続を切断．
            print_r($stmt->errorinfo());
            unset($db);
            die('最新コメントIDの取得に失敗しました。');
        }
        echo 'product_idが'.$param_proid.'の商品の最新コメントID取得に成功しました';

        $comment_id = $stmt->fetchAll(PDO::FETCH_ASSOC);
        print($comment_id[0]['comment_id']);


        // SQL文をセット
        $sql = "INSERT INTO comment VALUES (:comment_id, :product_id, :user_id, :comment_body)";
        $stmt = $db->prepare($sql);

        // idを自動追加する
        // 現在の最新IDの番号を取得
        $new_comid = (int) $comment_id[0]['comment_id'];
        echo $new_comid;
        echo gettype($new_comid);

        // 新しいIDに1プラス
        $new_comid += 1;
        echo $new_comid;

        // パラメーターをセット
        $stmt->bindValue(':comment_id', $new_comid, PDO::PARAM_INT);
        $stmt->bindValue(':product_id', $param_proid, PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $param_usrid, PDO::PARAM_STR);
        $stmt->bindValue(':comment_body', $_POST['comment_body'], PDO::PARAM_STR);

        // dbにexecute
        $result = $stmt->execute();
        if (!$result) {
            // データベースとの接続を切断．
            print_r($stmt->errorinfo());
            unset($db);
            die('登録失敗しました。');
        }
        echo '登録完了しました';

    } else {
        // paramの値が不適ならerrorと出力してプログラム終了
        echo "error";
    }

} catch(PDOException $e) {
    echo "error".$e->getMessage();
}

// データベースとの接続を切断．
unset($db);

?>