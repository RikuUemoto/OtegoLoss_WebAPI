<?php
/*
    作成者：植元 陸
    最終更新日：2022/1/12
    目的：  購入履歴に必要な情報を返す
    入力：  user_id
    http通信例：
    http://localhost/software_engineering/product/purchasehistory.php?user_id=u0000001
    
    その他：
*/

//json形式ファイルのheader
header("Content-Type: application/json; charset=utf-8");

// DBとの連携
try{
    $db = new PDO('mysql:dbname=test;host=localhost;charset=utf8','root','root');
    echo "接続OK";
    // データベース
    $data_pur = "purchase";
    $data_pro = "product";

    if(isset($_GET["user_id"])) {
        // numをエスケープ(xss対策)
        $param = htmlspecialchars($_GET["user_id"]);

        //SQL構文
        $table2 = "SELECT product_name, product_image, price
			FROM $data_pro
			WHERE product_id IN (SELECT product_id 
						        FROM $data_pur 
						        WHERE purchaser_id = '$param' 
						        ORDER BY purchase_id)";

        // メイン処理
        $arr["status"] = "yes";
        $sql2 = $db->query($table2);

        $arr = $sql2 -> fetchAll();

    } else {
        // paramの値が不適ならstatusをnoにしてプログラム終了
        $arr["status"] = "no";
    }

    // 配列をjson形式にデコードして出力, 第二引数は、整形するためのオプション
    print json_encode($arr, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch(PDOException $e) {
    echo "error".$e->getMessage();
}
?>