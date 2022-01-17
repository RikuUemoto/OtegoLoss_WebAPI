<?php
/*
作成者：坂口 白磨
    最終更新日：2022/1/
    目的：  お気に入りテーブルにお気に入り情報を追加
    
    http通信例：
    http://localhost/software_engineering/Favorite/InsertFavorite.php?user_id=&favorite_user_id=
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
    if(isset($_GET["user_id"]) 
        && isset($_GET["favorite_user_id"]) ) {

        // 各クエリストリングをエスケープ(xss対策)
        //$param_favorite_id = htmlspecialchars($_GET["favorite_id"]);
        $param_user_id = htmlspecialchars($_GET["user_id"]);
        $param_favorite_user_id = htmlspecialchars($_GET["favorite_user_id"]);
        
        /* 最新のお気に入りIDを取得 */
        $sql = "SELECT favorite_id FROM favorite WHERE user_id = :user_id ORDER BY favorite_id DESC LIMIT 1";
        // クエリ(問い合わせ)
        $stmt = $db->prepare($sql);
        $stmt ->bindValue(':user_id', $param_user_id, PDO::PARAM_STR);
        $result = $stmt->execute();
        if (!$result) {
            // データベースとの接続を切断．
            print_r($stmt->errorinfo());
            unset($db);
            die('最新お気に入りIDの取得に失敗しました。');
        }
        echo 'user_idが'.$param_user_id.'のユーザの最新お気に入りID取得に成功しました';

        $favorite_id = $stmt->fetchAll(PDO::FETCH_ASSOC);
        print($favorite_id[0]["favorite_id"]);

        // SQL文をセット
        $sql = "INSERT INTO favorite VALUES (:favorite_id,:user_id,:favorite_user_id)";
        $stmt = $db->prepare($sql);

        // idを自動追加する
        // 現在の最新IDの番号を取得
        $new_favorite_id = (int) $favorite_id[0]['favorite_id'];
        echo $new_favorite_id;
        echo gettype($new_favorite_id);

        // 新しいIDに1プラス
        $new_favorite_id += 1;
        echo $new_favorite_id;

        
        // パラメーターをセット
        $stmt->bindValue(':favorite_id', $new_favorite_id, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $param_user_id, PDO::PARAM_STR);
        $stmt->bindValue(':favorite_user_id', $param_favorite_user_id, PDO::PARAM_STR);
        
        // dbにexecute
        $result = $stmt->execute();
        if (!$result) {
            // データベースとの接続を切断．
            print_r($stmt->errorinfo());
            unset($db);
            die('登録失敗しました。');
        }
        echo '登録完了しました';

    } else {
        // paramの値が不適ならerrorと出力してプログラム終了
        echo "error";
    }

} catch(PDOException $e) {
    echo "error".$e->getMessage();
}

// データベースとの接続を切断．
unset($db);

?>