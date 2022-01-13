<?php
/*
    作成者：植元 陸
    最終更新日：2022/1/12
    目的：  配送手続きに必要な配送先情報を返す
    入力：  product_id
    http通信例：
    http://localhost/OtegoLoss_WebAPI/purchase&delivery_address/shippinginfo.php?product_id=g0000010
    
    その他：
*/

//json形式ファイルのheader
header("Content-Type: application/json; charset=utf-8");

// DBとの連携
try{
    $db = new PDO('mysql:dbname=software;host=localhost;charset=utf8','root','root');
    echo "接続OK";
    // データベース
    $data_pur = "purchase";
    $data_del = "delivery_address";

    if(isset($_GET["product_id"])) {
        // numをエスケープ(xss対策)
        $param = htmlspecialchars($_GET["product_id"]);
        //SQL構文
        $table2 = "SELECT real_name, telephone_number, postal_code, address
                    FROM $data_del 
                    WHERE user_id = (SELECT purchaser_id
                                        FROM $data_pur
                                        WHERE product_id = '$param')
                    AND d_address_id = (SELECT address_id
                                        FROM $data_pur
                                        WHERE product_id = '$param')";

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