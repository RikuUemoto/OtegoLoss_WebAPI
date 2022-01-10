<?php
/*
    作成者：植元 陸
    最終更新日：2022/1/11
    目的：  出品履歴に必要な情報を返す
    入力：  user_id
    http通信例：
    http://localhost/software_engineering/product/listinghistory.php?user_id=u0000001
    
    その他：
    接続OK{
        "status": "yes",
        "product_id": "g0000001",
        "product_name": "きゅうり",
        "product_image": "aaaaaa",
        "purchased": 0
    }string(109) "{"status":"yes","product_id":"g0000001","product_name":"きゅうり","product_image":"aaaaaa","purchased":0}"
*/

//json形式ファイルのheader
header("Content-Type: application/json; charset=utf-8");

// DBとの連携
try{
    $db = new PDO('mysql:dbname=test;host=localhost;charset=utf8','root','root');
    echo "接続OK";
    // データベース
    $data = "product";

    if(isset($_GET["user_id"])) {
        // numをエスケープ(xss対策)
        $param = htmlspecialchars($_GET["user_id"]);
        //SQL構文
        $table2 = "SELECT product_id, product_name, product_image, purchased
                     FROM $data WHERE seller_id = '$param'";
        // メイン処理
        $arr["status"] = "yes";
        $sql2 = $db->query($table2);
        while($table2 = $sql2 -> fetch()) {
            $arr["product_id"] = $table2['product_id'];
            $arr["product_name"] = $table2['product_name'];
            $arr["product_image"] = $table2['product_image'];
            $arr["purchased"] = $table2['purchased'];
        }

    } else {
        // paramの値が不適ならstatusをnoにしてプログラム終了
        $arr["status"] = "no";
    }

    // 配列をjson形式にデコードして出力, 第二引数は、整形するためのオプション
    print json_encode($arr, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    $json = json_encode($arr, JSON_UNESCAPED_UNICODE);
    var_dump($json);

} catch(PDOException $e) {
    echo "error".$e->getMessage();
}
?>