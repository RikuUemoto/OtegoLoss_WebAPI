<?php
/*
    作成者：植元 陸
    最終更新日：2022/1/24
    目的：  出品履歴の商品詳細情報と配送状況を返す
    入力：  product_id
    http通信例：
    http://localhost/OtegoLoss_WebAPI/product&purchase/listingdetails.php?product_id=g0000001
    
    その他：
*/

//json形式ファイルのheader
header("Content-Type: application/json; charset=utf-8");

// DBとの連携
try{
    $db = new PDO('mysql:dbname=software;host=localhost;charset=utf8','root','root');
    //echo "接続OK";
    // データベース
    $data_pur = "purchase";
    $data_pro = "product";
    $data_usr = "user";

    if(isset($_GET["product_id"])) {
        // numをエスケープ(xss対策)
        $param = htmlspecialchars($_GET["product_id"]);

        //SQL構文
        $table2 = "SELECT pro.product_name, pro.product_desc, pro.product_image, pro.recipe_url,
                    pro.category, pro.price, pro.delivery_meth, pro.listing_date,
                    pro.weight, pro.prefecture, pro.seller_id, usr.user_name, pro.purchased, pur.delivery_status
                    FROM $data_usr usr, $data_pro pro LEFT JOIN $data_pur pur ON pro.product_id = pur.product_id
                    WHERE pro.seller_id = usr.user_id
                    AND pro.product_id = '$param'";

        // メイン処理
        $arr["status"] = "yes";
        $sql2 = $db->query($table2);

        $arr = $sql2 -> fetchAll(PDO::FETCH_ASSOC);

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