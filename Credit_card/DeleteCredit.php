<?php
/*
作成者：坂口 白磨
    最終更新日：2022/1/
    目的：  クレカデーブルから決済情報を削除するWebAPI
            
    http通信例：
    http://localhost/software_engineering/credit/DeleteCredit.php?card_id=1&user_id=a0000001
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


    // URL後のクエリストリングをGET
    if(isset($_GET["card_id"]) && isset($_GET["user_id"])) {

        // クエリストリングをエスケープ(xss対策)
        $param_card = htmlspecialchars($_GET["card_id"]);
        $param_user = htmlspecialchars($_GET["user_id"]);


        /* 削除する対象が存在するかどうか確認 */
        $sql = "SELECT * FROM credit_card WHERE card_id = :card_id AND user_id = :user_id";
        // クエリ(問い合わせ)
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':card_id', $param_card, PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $param_user, PDO::PARAM_STR);
        $stmt->execute();
        $count = $stmt->rowCount();
        if ($count == 0) {
            // データベースとの接続を切断．
            unset($db);

            die('card_idが'.$param_card.'でuser_idが'.$param_user.'の決済情報は見つかりませんでした。');
        }
        echo 'card_idが'.$param_card.'でuser_idが'.$param_user.'の決済情報が'.$count.'件見つかりました。';


        // SQL文をセット
        $sql = "DELETE FROM credit_card WHERE card_id = :card_id AND user_id = :user_id";
        $stmt = $db->prepare($sql);

        // パラメーターをセット
        $stmt->bindValue(':card_id', $param_card, PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $param_user, PDO::PARAM_STR);
        // dbにexecute
        $result = $stmt->execute();
        if (!$result) {
            print_r($stmt->errorinfo());
            unset($db);
            die('決済情報の削除に失敗しました。');
        }
        echo '決済情報の削除が完了しました';

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