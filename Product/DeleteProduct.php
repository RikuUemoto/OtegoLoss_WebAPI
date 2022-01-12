<?php
/*
    作成者：植元 陸
    最終更新日：2022/1/11
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
            die('product_idが'.$param_proid.'の商品は見つかりませんでした。');
        }
        echo 'product_idが'.$param_proid.'の商品が'.$count.'件見つかりました。';


        // SQL文をセット
        $sql = "DELETE FROM product WHERE product_id = :product_id";
        $stmt = $db->prepare($sql);

        // パラメーターをセット
        $stmt->bindValue(':product_id', $param_proid, PDO::PARAM_STR);

        // dbにexecute
        $result = $stmt->execute();
        if (!$result) {
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