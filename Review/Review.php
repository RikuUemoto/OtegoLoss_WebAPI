<?php
/*
    作成者：松尾 匠馬
    最終更新日：2022/1/24
    目的：  レビューテーブルの生産者ID(ユーザID)からレビュー情報を返す(出品者情報)
    入力：  user_id
    http通信例：
    http://localhost/OtegoLoss_WebAPI/product/Review.php?user_id=u0000111
    
    その他：
*/

//json形式ファイルのheader
header("Content-Type: application/json; charset=utf-8");

// DBとの連携
try{
    $db = new PDO('mysql:dbname=software;host=localhost;charset=utf8','root','root');
    //echo "接続OK";
    // データベース
    $data_review = "review";
    $data_usr = "user";

    if(isset($_GET["user_id"])) {
        // numをエスケープ(xss対策)
        $param = htmlspecialchars($_GET["user_id"]);
        //SQL構文
        $table2 = "SELECT review_user_id, user_name, assessment, comment
                     FROM $data_review, $data_usr
                     WHERE $data_review.user_id = '$param'
                     AND review_user_id = $data_usr.user_id";
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