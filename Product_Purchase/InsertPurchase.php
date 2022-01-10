<?php
/*
    作成者：植元 陸
    最終更新日：2022/1/11
    目的：  購入テーブルに購入情報を追加
    入力：  purchaser_id, product_id, card_id, address_id
    http通信例：
    http://localhost/software_engineering/product_purchase/insertpurchase.php?purchaser_id=u0000009&product_id=g0000111&card_id=25&address_id=789
    
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
    $db = new PDO('mysql:dbname=test;host=localhost;charset=utf8','root','root');
    echo "接続OK";


    // URL後の各クエリストリングをGET
    if(isset($_GET["purchaser_id"]) && isset($_GET["product_id"])
                && isset($_GET["card_id"]) && isset($_GET["address_id"])) {
        
        // 各クエリストリングをエスケープ(xss対策)
        $param_prrid = htmlspecialchars($_GET["purchaser_id"]);
        $param_proid = htmlspecialchars($_GET["product_id"]);
        $param_crdid = htmlspecialchars($_GET["card_id"]);
        $param_adrid = htmlspecialchars($_GET["address_id"]);


        /* 商品テーブルの"購入済み"属性の値をtrueに変更 */
        // SQL文をセット
        $sql = "UPDATE product SET purchased = true WHERE product_id = :product_id";
        $stmt = $db->prepare($sql);
        // パラメーターをセット
        $stmt->bindValue(':product_id', $param_proid, PDO::PARAM_STR);
        // SQLを実行
        $stmt->execute();


        /* 購入テーブルに購入情報を追加 */
        /* 最新の購入IDを取得 */
        $sql = "SELECT purchase_id FROM purchase WHERE purchaser_id = :purchaser_id ORDER BY purchase_id DESC LIMIT 1";
        // クエリ(問い合わせ)
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':purchaser_id', $param_prrid, PDO::PARAM_STR);
        $result = $stmt->execute();
        if (!$result) {
            die('最新購入IDの取得に失敗しました。');
        }
        echo 'purchaser_idが'.$param_prrid.'の購入情報の最新購入ID取得に成功しました';

        $purchase_id = $stmt->fetchAll(PDO::FETCH_ASSOC);
        print($purchase_id[0]['purchase_id']);


        // SQL文をセット
        $sql = "INSERT INTO purchase VALUES (:purchase_id, :purchaser_id, :product_id, NOW(), :card_id, :address_id, false)";
        $stmt = $db->prepare($sql);

        // idを自動追加する
        // 現在の最新IDの番号を取得
        $new_purid = (int) $purchase_id[0]['purchase_id'];
        echo $new_purid;
        echo gettype($new_purid);

        // 新しいIDに1プラス
        $new_purid += 1;
        echo $new_purid;

        // パラメーターをセット
        $stmt->bindValue(':purchase_id', $new_purid, PDO::PARAM_INT);
        $stmt->bindValue(':purchaser_id', $param_prrid, PDO::PARAM_STR);
        $stmt->bindValue(':product_id', $param_proid, PDO::PARAM_STR);
        $stmt->bindValue(':card_id', $param_crdid, PDO::PARAM_INT);
        $stmt->bindValue(':address_id', $param_adrid, PDO::PARAM_INT);

        // dbにexecute
        $result = $stmt->execute();
        if (!$result) {
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