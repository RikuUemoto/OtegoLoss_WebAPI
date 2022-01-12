<?php
/*
    作成者：植元 陸
    最終更新日：2022/1/11
    目的：  コメントテーブルからコメントを削除
    入力：  comment_id, product_id
    http通信例：
    http://localhost/software_engineering/comment/DeleteComment.php?comment_id=1&product_id=g0000005
    
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
    if(isset($_GET["comment_id"]) && isset($_GET["product_id"])) {

        // 各クエリストリングをエスケープ(xss対策)
        $param_comid = htmlspecialchars($_GET["comment_id"]);
        $param_proid = htmlspecialchars($_GET["product_id"]);


        /* 削除する対象が存在するかどうか確認 */
        $sql = "SELECT * FROM comment WHERE comment_id = :comment_id AND product_id = :product_id";
        // クエリ(問い合わせ)
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':comment_id', $param_comid, PDO::PARAM_INT);
        $stmt->bindValue(':product_id', $param_proid, PDO::PARAM_STR);
        $stmt->execute();
        $count = $stmt->rowCount();
        if ($count == 0) {
            // データベースとの接続を切断．
            unset($db);
            die('comment_idが'.$param_comid.'でproduct_idが'.$param_proid.'のコメントは見つかりませんでした。');
        }
        echo 'comment_idが'.$param_comid.'でproduct_idが'.$param_proid.'のコメントが'.$count.'件見つかりました。';


        // SQL文をセット
        $sql = "DELETE FROM comment WHERE comment_id = :comment_id AND product_id = :product_id";
        $stmt = $db->prepare($sql);

        // パラメーターをセット
        $stmt->bindValue(':comment_id', $param_comid, PDO::PARAM_INT);
        $stmt->bindValue(':product_id', $param_proid, PDO::PARAM_STR);

        // dbにexecute
        $result = $stmt->execute();
        if (!$result) {
            // データベースとの接続を切断．
            unset($db);
            die('コメントの削除に失敗しました。');
        }
        echo 'コメントの削除が完了しました';

    } else {
        // paramの値が不適ならerrorと出力してプログラム終了
        echo "error!入力値が不足・不適です．";
    }

} catch(PDOException $e) {
    echo "error".$e->getMessage();
}

// データベースとの接続を切断．
unset($db);

?>