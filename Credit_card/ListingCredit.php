<?php
/*
    作成者：坂口 白磨
    最終更新日：2022/1/24
    目的：  クレカ情報をすべて表示する
    入力：  user_id
    http通信例：
    http://localhost/software_engineering/Credit/ListingCredit.php?user_id=
    
    その他：
*/

//json形式ファイルのheader
header("Content-Type: application/json; charset=utf-8");

// DBとの連携
try{
    $db = new PDO('mysql:dbname=software;host=localhost;charset=utf8','root','root');
    //echo "接続OK";
    // データベース
    $data_cre = "credit_card";
    $data_usr = "user";
    

    if(isset($_GET["user_id"])) {
        // numをエスケープ(xss対策)
        $param = htmlspecialchars($_GET["user_id"]);

        //SQL構文
        $table2 = "SELECT card_id,card_number,security_number,card_comp,nominee,validated_date
                    FROM $data_cre
                    WHERE user_id = '$param'";

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