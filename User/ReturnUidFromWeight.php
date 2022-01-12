<?php
/*
    作成者：松尾　匠馬
    最終更新日：2022/1/13
    目的：ユーザIDから重量を返す(ロス削減)
    入力：user_id
    ※ ()はNULL可

    http通信例：
    http://localhost/OtegoLoss_WebAPI/User/ReturnUidFromWeight.php?user_id=u0000002
    

*/

//json形式ファイルのheader
header("Content-Type: application/json; charset=utf-8");

// DBとの連携
try{
    $db = new PDO('mysql:dbname=software;host=localhost;charset=utf8','root','root');
    echo "接続OK";
    // データベース
    $data = "user";

    if(isset($_GET["user_id"])) {
        // numをエスケープ(xss対策)
        $param = htmlspecialchars($_GET["user_id"]);
        //SQL構文
        $table2 = "SELECT gross_weight FROM $data WHERE user_id = '$param'";
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