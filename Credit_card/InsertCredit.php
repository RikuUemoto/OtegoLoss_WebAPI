<?php
/*
作成者：坂口 白磨
    最終更新日：2022/1/
    目的：  クレカテーブルに決済情報を追加するWebAPI
    http通信例：
    http://localhost/software_engineering/Credit/InsertCredit.php?user_id=&
    card_number=&security_number=&card_comp=&nominee=&validated_date=
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
    $data = "credit_card";

    // URL後の各クエリストリングをGET
    if(isset($_GET["user_id"]) && isset($_GET["card_number"]) 
        && isset($_GET["security_number"]) && isset($_GET["card_comp"]) 
        && isset($_GET["nominee"]) && isset($_GET["validated_date"]) ) {

        // 各クエリストリングをエスケープ(xss対策)
        $param_user_id = htmlspecialchars($_GET["user_id"]);
        $param_card_number = htmlspecialchars($_GET["card_number"]);
        $param_security_number = htmlspecialchars($_GET["security_number"]);      
        $param_card_comp = htmlspecialchars($_GET["card_comp"]);
        $param_nominee = htmlspecialchars($_GET["nominee"]);
        $param_validated_date = htmlspecialchars($_GET["validated_date"]);
        
        /* 最新のクレカIDを取得 */
        $sql = "SELECT card_id FROM $data WHERE user_id = :user_id ORDER BY card_id DESC LIMIT 1";
        // クエリ(問い合わせ)
        $stmt = $db->prepare($sql);
        $stmt ->bindValue(':user_id', $param_user_id, PDO::PARAM_STR);
        $result = $stmt->execute();
        if (!$result) {
            // データベースとの接続を切断．
            unset($db);

            die('最新クレカIDの取得に失敗しました。');
        }
        echo 'user_idが'.$param_user_id.'のユーザの最新クレカID取得に成功しました';

        $card_id = $stmt->fetchAll(PDO::FETCH_ASSOC);
        print($card_id[0]["card_id"]);

        // SQL文をセット
        $sql = "INSERT INTO credit_card VALUES (:card_id,:user_id,:card_number,:security_number,:card_comp,:nominee,:validated_date)";
        $stmt = $db->prepare($sql);

        // idを自動追加する
        // 現在の最新IDの番号を取得
        $new_card_id = (int) $card_id[0]['card_id'];
        echo $new_card_id;
        echo gettype($new_card_id);

        // 新しいIDに1プラス
        $new_card_id += 1;
        echo $new_card_id;

        
        // パラメーターをセット
        $stmt->bindValue(':card_id', $new_card_id, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $param_user_id, PDO::PARAM_STR);
        $stmt->bindValue(':card_number', $param_card_number, PDO::PARAM_STR);
        $stmt->bindValue(':security_number', $param_security_number, PDO::PARAM_STR);
        $stmt->bindValue(':card_comp', $param_card_comp, PDO::PARAM_STR);
        $stmt->bindValue(':nominee', $param_nominee, PDO::PARAM_STR);
        $stmt->bindValue(':validated_date', $param_validated_date, PDO::PARAM_INT);
        
        // dbにexecute
        $result = $stmt->execute();
        if (!$result) {
            // データベースとの接続を切断．
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