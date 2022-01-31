<?php
/*
    作成者：植元 陸
    最終更新日：2022/1/30
    目的：  商品テーブルから商品を削除
    入力：  product_id
    http通信例：
    http://localhost/software_engineering/product/DeleteProduct.php?product_id=g0000001
    
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


    // URL後のクエリストリング"product_id"をGET
    if(isset($_GET["product_id"])) {

        // クエリストリングをエスケープ(xss対策)
        $param_proid = htmlspecialchars($_GET["product_id"]);


        /* 削除する対象が存在するかどうか確認 */
        $sql = "SELECT * FROM product WHERE product_id = :product_id";
        // クエリ(問い合わせ)
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':product_id', $param_proid, PDO::PARAM_STR);
        $stmt->execute();
        $count = $stmt->rowCount();
        if ($count == 0) {
            // データベースとの接続を切断．
            unset($db);
            die('product_idが'.$param_proid.'の商品は見つかりませんでした。');
        }
        echo 'product_idが'.$param_proid.'の商品が'.$count.'件見つかりました。';

        /* 購入済みの商品は削除できない */
        $product = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if($product[0]['purchased'] == true) {
            // データベースとの接続を切断．
            unset($db);
            die('商品IDが '.$param_proid.' の商品は購入済みであるため削除できません。');
        }

        /* コメントテーブルでその商品に関するコメントはすべて削除する（商品情報を削除するための準備1） */
        $sql = "DELETE FROM comment WHERE product_id = :product_id";
        $stmt = $db->prepare($sql);

        // パラメーターをセット
        $stmt->bindValue(':product_id', $param_proid, PDO::PARAM_STR);

        // dbにexecute
        $result = $stmt->execute();
        if (!$result) {
            // データベースとの接続を切断．
            unset($db);
            die('商品IDが '.$param_proid.' の商品に対するコメントを削除できませんでした。');
        }
        echo '商品IDが '.$param_proid.' の商品に対するコメントをすべて削除しました。';


        /* 商品通報テーブルでその商品に関する商品通報情報はすべて削除する（商品情報を削除するための準備2） */
        $sql = "DELETE FROM report_product WHERE reported_id = :reported_id";
        $stmt = $db->prepare($sql);

        // パラメーターをセット
        $stmt->bindValue(':reported_id', $param_proid, PDO::PARAM_STR);

        // dbにexecute
        $result = $stmt->execute();
        if (!$result) {
            // データベースとの接続を切断．
            unset($db);
            die('商品IDが '.$param_proid.' の商品に対する通報情報を削除できませんでした。');
        }
        echo '商品IDが '.$param_proid.' の商品に対する通報情報をすべて削除しました。';


        /* 商品情報を削除する */
        $sql = "DELETE FROM product WHERE product_id = :product_id";
        $stmt = $db->prepare($sql);

        // パラメーターをセット
        $stmt->bindValue(':product_id', $param_proid, PDO::PARAM_STR);

        // dbにexecute
        $result = $stmt->execute();
        if (!$result) {
            // データベースとの接続を切断．
            unset($db);
            die('商品の削除に失敗しました。');
        }
        echo '商品の削除が完了しました';

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