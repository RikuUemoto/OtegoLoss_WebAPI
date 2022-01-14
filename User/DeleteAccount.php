<?php
/*
    作成者：松尾　匠馬
    最終更新日：2022/1/12
    目的：  ユーザテーブルからアカウント情報を削除
    入力：  user_id
    http通信例：
    http://localhost/OtegoLoss_WebAPI/User/DeleteAccount.php?user_id=u0000001
    
*/

#ステータスコードを追記する必要あり
//エラーリポート
error_reporting(E_ALL);
//json形式ファイルのheader
header("Content-Type: application/json; charset=utf-8");

// DBとの連携
try{
    // データベースに接続する．
    $db = new PDO('mysql:dbname=software;host=localhost;charset=utf8','root','root');
    echo "接続OK";


    // URL後のクエリストリング"user_id"をGET
    if(isset($_GET["user_id"])) {

        // クエリストリングをエスケープ(xss対策)
        $param_userid = htmlspecialchars($_GET["user_id"]);


        /* 削除する対象が存在するかどうか確認 */
        $sql = "SELECT * FROM user WHERE user_id = :user_id";
        // クエリ(問い合わせ)
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $param_userid, PDO::PARAM_STR);
        $stmt->execute();
        $count = $stmt->rowCount();
        if ($count == 0) {
            unset($db);
            die('product_idが'.$param_userid.'のアカウントは見つかりませんでした。');
        }
        echo 'user_idが'.$param_userid.'のアカウントが'.$count.'件見つかりました。';


        // SQL文をセット
        $sql = "DELETE FROM user WHERE user_id = :user_id";
        $stmt = $db->prepare($sql);

        // パラメーターをセット
        $stmt->bindValue(':user_id', $param_userid, PDO::PARAM_STR);

        // dbにexecute
        $result = $stmt->execute();
        if (!$result) {
            unset($db);
            die('アカウントの削除に失敗しました。');
        }
        echo 'アカウントの削除が完了しました';

    } else {
        // paramの値が不適ならerrorと出力してプログラム終了
        echo "error!入力値が不足・不適です．";
    }

} catch(PDOException $e) {
    echo "error".$e->getMessage();
}

// データベースとの接続を切断．
unset($db);

?>