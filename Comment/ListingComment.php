<?php
/*
    作成者：坂口 白磨
    最終更新日：2022/1/24
    目的：  商品に寄せられたコメント(ユーザIDとコメント内容)をすべて表示する
    入力：  product_id
    http通信例：
    http://localhost/software_engineering/Comment/ListingComment.php?product_id=g0000001
    
    その他：
*/

//json形式ファイルのheader
header("Content-Type: application/json; charset=utf-8");

// DBとの連携
try{
    $db = new PDO('mysql:dbname=software;host=localhost;charset=utf8','root','root');
    //echo "接続OK";
    // データベース
    $data_com = "comment";
    $data_usr = "user";
    

    if(isset($_GET["product_id"])) {
        // numをエスケープ(xss対策)
        $param = htmlspecialchars($_GET["product_id"]);

        //SQL構文
        $table2 = "SELECT $data_com.user_id,comment_body,user_name
                    FROM $data_com,$data_usr
                    WHERE $data_com.user_id = $data_usr.user_id
                    AND product_id = '$param'";

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