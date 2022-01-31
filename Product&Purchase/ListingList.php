<?php
/*
    作成者：坂口 白磨
    最終更新日：2022/1/24
    目的：  出品履歴の出品商品一覧(商品ID、商品名、価格、出品日、購入済み)を返すWebAPI
    入力：  user_id
    http通信例：
    http://localhost/software_engineering/Credit/ListingList.php?user_id=
    
    その他：
*/

//json形式ファイルのheader
header("Content-Type: application/json; charset=utf-8");

// DBとの連携
try{
    $db = new PDO('mysql:dbname=software;host=localhost;charset=utf8','root','root');
    //echo "接続OK";
    // データベース
    $data_pro = "product";
    $data_usr = "user";
    

    if(isset($_GET["user_id"])) {
        // numをエスケープ(xss対策)
        $param = htmlspecialchars($_GET["user_id"]);

        //SQL構文
        $table2 = "SELECT product_id,product_name,price,listing_date,purchased,product_image
                    FROM $data_pro
                    WHERE seller_id = '$param'";

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