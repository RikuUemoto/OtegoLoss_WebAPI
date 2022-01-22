<?php
/*
    作成者：坂口　白磨
    最終更新日：2022/1/18
    目的：お気に入りテーブルの「自分であるユーザID」と「出品者であるお気に入りユーザID」を問い合わせしてあればtrueを返す
    入力：user_id,favorite_user_id
    ※ ()はNULL可

    http通信例：
    http://localhost/software_engineering/Favorite/CheckFavorite.php?user_id=&favorite_user_id=
    

*/

//json形式ファイルのheader
header("Content-Type: application/json; charset=utf-8");

// DBとの連携
try{
    $db = new PDO('mysql:dbname=software;host=localhost;charset=utf8','root','root');
    //echo "接続OK";
    // データベース
    $data = "favorite";

    if(isset($_GET["user_id"]) && isset($_GET["favorite_user_id"])) {
        
        // numをエスケープ(xss対策)
        $param_user_id = htmlspecialchars($_GET["user_id"]);
        $param_favorite_user_id = htmlspecialchars($_GET["favorite_user_id"]);
        //SQL構文
        $table2 = "SELECT user_id,favorite_user_id FROM $data WHERE user_id = '$param_user_id' AND favorite_user_id  = '$param_favorite_user_id'";
        
        // メイン処理
        $arr["status"] = "yes";
        $sql2 = $db->query($table2);
        $arr = $sql2 -> fetchAll(PDO::FETCH_ASSOC);
        
        if($arr == NULL){
            $x = false;
        }else{
            $x = true;
        }
    } else {
        // paramの値が不適ならstatusをnoにしてプログラム終了
        $arr["status"] = "no";
    }

    // 配列をjson形式にデコードして出力, 第二引数は、整形するためのオプション
    print json_encode($x, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    //print json_encode($arr, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch(PDOException $e) {
    echo "error".$e->getMessage();
}


?>