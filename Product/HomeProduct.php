<?php
/*
    作成者：植元 陸
    最終更新日：2022/1/24
    目的：  ホーム画面で出力する最新の商品20個の商品情報をを返す
    入力：  なし
    http通信例：
    http://localhost/software_engineering/product/listinghistory.php?user_id=u0000001
    
    その他：
*/

//json形式ファイルのheader
header("Content-Type: application/json; charset=utf-8");

// DBとの連携
try{
    $db = new PDO('mysql:dbname=software;host=localhost;charset=utf8','root','root');
    //echo "接続OK";
    // データベース
    $data = "product";
    $data_usr = "user";


    //SQL構文
    $table2 = "SELECT product_id, product_name, product_image, price, seller_id, user_name
                    FROM $data, $data_usr
                    WHERE purchased = false
                    AND seller_id = user_id
                    ORDER BY listing_date DESC LIMIT 20";
    // メイン処理
    $arr["status"] = "yes";
    $sql2 = $db->query($table2);
    
    $arr = $sql2 -> fetchAll(PDO::FETCH_ASSOC);


    // 配列をjson形式にデコードして出力, 第二引数は、整形するためのオプション
    print json_encode($arr, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch(PDOException $e) {
    echo "error".$e->getMessage();
}
?>