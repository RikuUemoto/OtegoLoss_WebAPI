<?php
/*
    作成者：坂口 白磨
    最終更新日：2022/1/31
    目的：  お気に入りテーブルからお気に入り情報を削除
    入力：  user_id
    http通信例：
    http://localhost/software_engineering/Favorite/DeleteFavorite.php?user_id=
    
    その他：
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


    // URL後の各クエリストリングをGET
    if(isset($_GET["user_id"])) {

        // 各クエリストリングをエスケープ(xss対策)
        $param_user_id = htmlspecialchars($_GET["user_id"]);
        


        /* 削除する対象が存在するかどうか確認 */
        $sql = "SELECT * FROM favorite WHERE user_id = :user_id ";
        // クエリ(問い合わせ)
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $param_user_id, PDO::PARAM_STR);
        
        $stmt->execute();
        $count = $stmt->rowCount();
        if ($count == 0) {
            // データベースとの接続を切断．
            unset($db);
            die('user_idが'.$param_user_id.'のお気に入り情報は見つかりませんでした。');
        }
        echo 'user_idが'.$param_user_id.'のお気に入り情報が'.$count.'件見つかりました。';


        // SQL文をセット
        $sql = "DELETE FROM favorite WHERE user_id = :user_id";
        $stmt = $db->prepare($sql);

        // パラメーターをセット
        $stmt->bindValue(':user_id', $param_user_id, PDO::PARAM_STR);
        
        // dbにexecute
        $result = $stmt->execute();
        if (!$result) {
            // データベースとの接続を切断．
            unset($db);
            die('お気に入り情報の削除に失敗しました。');
        }
        echo 'お気に入り情報の削除が完了しました';

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