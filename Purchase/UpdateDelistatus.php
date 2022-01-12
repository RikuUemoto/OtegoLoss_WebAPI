<?php
/*
    作成者：植元 陸
    最終更新日：2022/1/13
    目的：  購入テーブルの配送状況をtrueに更新
    入力：  product_id
    http通信例：
    http://localhost/OtegoLoss_WebAPI/purchase/updatedelistatus.php?product_id=g0000111
    
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
    // データベース
    $data = "purchase";

    // URL後の各クエリストリングをGET
    if(isset($_GET["product_id"])) {
        
        // 各クエリストリングをエスケープ(xss対策)
        $param = htmlspecialchars($_GET["product_id"]);

        // SQL文をセット
        $sql = "UPDATE $data SET delivery_status = true WHERE product_id = :product_id";
        $stmt = $db->prepare($sql);

        // パラメーターをセット
        $stmt->bindValue(':product_id', $param, PDO::PARAM_STR);

        // dbにexecute
        $result = $stmt->execute();
        if (!$result) {
            // データベースとの接続を切断．
            unset($db);
            die('delivery_statusのtrueへの更新処理に失敗しました。');
        }
        echo 'delivery_statusのtrueへの更新処理完了しました';

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