<?php
/*
    作成者：松尾　匠馬
    最終更新日：2022/1/19
    目的：  商品通報テーブル、アカウント通報テーブルに通報に関する情報を追加する
    入力：  user_id, reported_id(product_id), report_reason　(商品通報)
            user_id, reported_id(user_id), report_reason　(アカウント通報)
    
    http通信例：　
    (商品通報の例)
    http://localhost/OtegoLoss_WebAPI/Product/InsertReport.php?user_id=u0000001
    &reported_id=g0000007
    
    (アカウント通報の例)
    http://localhost/OtegoLoss_WebAPI/Product/InsertReport.php?user_id=u0000001
    &reported_id=u0000007
   
    $_POST：
    report_reason =
    
    
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
    $data_report_pro = "report_product";
    $data_report_user = "report_user";


    // 通報の識別をするためreported_idの左端の文字(u or g)を変数に格納
    $identifier = substr($_GET["reported_id"], 0, 1);


    //商品通報
    // URL後の各クエリストリングをGET
    if(isset($_GET["user_id"]) && isset($_GET["reported_id"])
         && isset($_POST["report_reason"]) && $identifier = 'g') {

        // 各クエリストリングをエスケープ(xss対策)
        $param_user_id = htmlspecialchars($_GET["user_id"]);
        $param_reported_id = htmlspecialchars($_GET["reported_id"]);
        $param_report_reason = htmlspecialchars($_POST["report_reason"]);
        
        /* 最新の通報番号を取得 */
        $sql = "SELECT report_number FROM $data_report_pro WHERE user_id = :user_id ORDER BY card_id DESC LIMIT 1";
        // クエリ(問い合わせ)
        $stmt = $db->prepare($sql);
        $stmt ->bindValue(':user_id', $param_user_id, PDO::PARAM_STR);
        $result = $stmt->execute();
        if (!$result) {
            // データベースとの接続を切断．
            unset($db);

            die('最新通報番号の取得に失敗しました。');
        }
        echo 'user_idが'.$param_user_id.'のユーザの最新通報番号取得に成功しました';

        $report_number = $stmt->fetchAll(PDO::FETCH_ASSOC);
        print($report_number[0]["report_number"]);

        // SQL文をセット
        $sql = "INSERT INTO $data_report_pro VALUES (:report_number, :user_id, :reported_id, :report_reason)";
        $stmt = $db->prepare($sql);

        // idを自動追加する
        // 現在の最新IDの番号を取得
        $new_report_number = (int) $report_number[0]['report_number'];
        echo $new_report_number;
        echo gettype($new_report_number);

        // 新しいIDに1プラス
        $new_report_number += 1;
        echo $new_report_number;

        
        // パラメーターをセット
        $stmt->bindValue(':card_id', $new_report_number, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $param_user_id, PDO::PARAM_STR);
        $stmt->bindValue(':reported_id', $param_reported_id, PDO::PARAM_STR);
        $stmt->bindValue(':report_reason', $param_report_reason, PDO::PARAM_STR);


        // dbにexecute
        $result = $stmt->execute();
        if (!$result) {
            // データベースとの接続を切断．
            print_r($stmt->errorinfo());
            unset($db);
            die('登録失敗しました。');
        }
        echo '登録完了しました';


    //アカウント通報
    // URL後の各クエリストリングをGET
    } else if (isset($_GET["user_id"]) && isset($_GET["reported_id"])
                && isset($_POST["report_reason"]) && $identifier = 'u') {
                 
        // 各クエリストリングをエスケープ(xss対策)
        $param_user_id = htmlspecialchars($_GET["user_id"]);
        $param_reported_id = htmlspecialchars($_GET["reported_id"]);
        $param_report_reason = htmlspecialchars($_POST["report_reason"]);
        
        /* 最新の通報番号を取得 */
        $sql = "SELECT report_number FROM $data_report_user WHERE user_id = :user_id ORDER BY card_id DESC LIMIT 1";
        // クエリ(問い合わせ)
        $stmt = $db->prepare($sql);
        $stmt ->bindValue(':user_id', $param_user_id, PDO::PARAM_STR);
        $result = $stmt->execute();
        if (!$result) {
            // データベースとの接続を切断．
            unset($db);

            die('最新通報番号の取得に失敗しました。');
        }
        echo 'user_idが'.$param_user_id.'のユーザの最新通報番号取得に成功しました';

        $report_number = $stmt->fetchAll(PDO::FETCH_ASSOC);
        print($report_number[0]["report_number"]);

        // SQL文をセット
        $sql = "INSERT INTO $data_report_pro VALUES (:report_number, :user_id, :reported_id, :report_reason)";
        $stmt = $db->prepare($sql);

        // idを自動追加する
        // 現在の最新IDの番号を取得
        $new_report_number = (int) $report_number[0]['report_number'];
        echo $new_report_number;
        echo gettype($new_report_number);

        // 新しいIDに1プラス
        $new_report_number += 1;
        echo $new_report_number;

        
        // パラメーターをセット
        $stmt->bindValue(':card_id', $new_report_number, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $param_user_id, PDO::PARAM_STR);
        $stmt->bindValue(':reported_id', $param_reported_id, PDO::PARAM_STR);
        $stmt->bindValue(':report_reason', $param_report_reason, PDO::PARAM_STR);


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