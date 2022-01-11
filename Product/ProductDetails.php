<?php
/*
    作成者：植元 陸
    最終更新日：2022/1/12
    目的：  商品詳細画面に必要な商品詳細情報を返す
    入力：  product_id
    http通信例：
    http://localhost/OtegoLoss_WebAPI/product/productdetails.php?product_id=g0000111
    
    その他：
*/

//json形式ファイルのheader
header("Content-Type: application/json; charset=utf-8");

// DBとの連携
try{
    $db = new PDO('mysql:dbname=test;host=localhost;charset=utf8','root','root');
    echo "接続OK";
    // データベース
    $data_pro = "product";
    $data_usr = "user";

    if(isset($_GET["product_id"])) {
        // numをエスケープ(xss対策)
        $param = htmlspecialchars($_GET["product_id"]);
        //SQL構文
        $table2 = "SELECT product_name, product_desc, product_image, recipe_url,
                            category, price, delivery_meth, listing_date,
                            weight, prefecture, user_name, purchased
                     FROM $data_pro, $data_usr 
                     WHERE seller_id = user_id
                     AND product_id = '$param'";
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