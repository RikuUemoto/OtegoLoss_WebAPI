<?php
/*
    作成者：矢野皓己
    最終更新日：2022/1/120
    目的：      ユーザテーブルのweightに買ったものの重さを加算する
    入力：  user_id, product_id
    http通信例：
    http:///AddWeight.php?user_id=u0000001&product_id=g0000001
    
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
    if(isset($_GET["user_id"]) && isset($_GET["product_id"])){
        
        // 各クエリストリングをエスケープ(xss対策)
        $param_useid = htmlspecialchars($_GET["user_id"]);
        $param_proid = htmlspecialchars($_GET["product_id"]);


        /* 商品テーブルの"購入済み"属性の値をtrueに変更 */
        // SQL文をセット
        $sql = "UPDATE user, product
        SET user.gross_weight = user.gross_weight + product.weight
        WHERE user.user_id = :user_id
        AND product.product_id = :product_id";
        $stmt = $db->prepare($sql);
        // パラメーターをセット
        $stmt->bindValue(':user_id', $param_useid, PDO::PARAM_STR);        
        $stmt->bindValue(':product_id', $param_proid, PDO::PARAM_STR);
        // SQLを実行
        $stmt->execute();
        $result = $stmt->execute();
        
        if (!$result) {
            // データベースとの接続を切断．
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