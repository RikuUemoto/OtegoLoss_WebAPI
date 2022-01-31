<?php
/*
    作成者：坂口 白磨
    最終更新日：2022/1/8
    目的：  レビューをレビューデーブルに追加
            
    http通信例：
    http://localhost/software_engineering/Review/InsertReview.php?user_id=&review_user_id=&assessment=&comment=
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

    // データベース
    $data = "review";
    // URL後の各クエリストリングをGET
    if(isset($_POST["user_id"]) && isset($_POST["review_user_id"]) && isset($_POST["assessment"]) 
    && isset($_POST["comment"])  ) {

        // 各クエリストリングをエスケープ(xss対策)
        $param_user_id = htmlspecialchars($_POST["user_id"]); 
        $param_review_user_id = htmlspecialchars($_POST["review_user_id"]); 
        $param_assessment = htmlspecialchars($_POST["assessment"]);
        $param_comment = $_POST["comment"];

        // commentは任意
        if ($param_comment == '') {
            $param_comment = NULL;
        }
        
        /* 最新のレビューIDを取得 */
        $sql = "SELECT review_id FROM $data WHERE user_id = :user_id ORDER BY review_id DESC LIMIT 1";
        // クエリ(問い合わせ)
        $stmt = $db->prepare($sql);
        $stmt ->bindValue(':user_id', $param_user_id, PDO::PARAM_STR);
        $result = $stmt->execute();
        if (!$result) {
            // データベースとの接続を切断．
            print_r($stmt->errorinfo());
            unset($db);
            die('最新レビューIDの取得に失敗しました。');
        }
        echo 'user_idが'.$param_user_id.'のユーザの最新レビューID取得に成功しました';

        $review_id = $stmt->fetchAll(PDO::FETCH_ASSOC);
        print($review_id[0]["review_id"]);

       
       
        // SQL文をセット
        $sql = "INSERT INTO $data VALUES (:review_id,:user_id,:review_user_id,:assessment,:comment)";
        $stmt = $db->prepare($sql);

        // idを自動追加する
        // 現在の最新IDの番号を取得
        $new_review_id = (int) $review_id[0]['review_id'];
        echo $new_review_id;
        echo gettype($new_review_id);

        // 新しいIDに1プラス
        $new_review_id += 1;
        echo $new_review_id;
                   
                   
                   
        // パラメーターをセット
        $stmt->bindValue(':review_id' , $new_review_id, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $param_user_id, PDO::PARAM_STR);
        $stmt->bindValue(':review_user_id', $param_review_user_id, PDO::PARAM_STR);
        $stmt->bindValue(':assessment' , $param_assessment, PDO::PARAM_STR);
        $stmt->bindValue(':comment' , $param_comment, PDO::PARAM_STR);
        
        
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
 