<?php
/*
    作成者：植元 陸
    最終更新日：2022/1/11
    目的：  商品テーブルに商品を追加
    入力：  product_name, product_desc, product_image, (recipe_url), category, price,
    　　　　delivery_meth, weight, prefecture, seller_id
    　　　　※ ()はNULL可
    http通信例：
    http://localhost/software_engineering/Product/InsertProduct.php?product_name=ai
    &product_desc=1%EF%BC%91a%E3%81%82&product_image=fvsdlvjsk&category=%E9%AD%9A&price=3000
    &delivery_meth=%E5%86%B7%E5%87%8D&weight=500&prefecture=39
    &seller_id=u0000004&recipe_url=https://www.kochi-tech.ac.jp
    
    その他：product_desc, recipe_urlは$_POSTで入手しないと大きくなりすぎる可能性がある
    　　　　product_imageは画像パスを格納，画像自体はサーバのローカルディスク上とか
    　　　　出来ればcheck制約が行えなかった日本語部分の制約は行う必要がある
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

    /* 最新の商品IDを取得 */
    $table = "SELECT CAST(RIGHT(product_id, 7) AS signed) AS product_id
                FROM product 
                ORDER BY CAST(RIGHT(product_id, 7) AS signed) DESC LIMIT 1";
    // クエリ(問い合わせ)
    $sql = $db->query($table);
    $product_num = $sql->fetchAll(PDO::FETCH_ASSOC);
    print($product_num[0]['product_id']);


    // URL後の各クエリストリングをGET
    if(isset($_GET["product_name"]) && isset($_GET["product_desc"]) 
        && isset($_GET["product_image"]) && isset($_GET["category"]) 
        && isset($_GET["price"]) && isset($_GET["delivery_meth"]) 
        && isset($_GET["weight"]) && isset($_GET["prefecture"]) && isset($_GET["seller_id"])) {

        // 各クエリストリングをエスケープ(xss対策)
        $param_pname= htmlspecialchars($_GET["product_name"]);
        $param_pdesc = htmlspecialchars($_GET["product_desc"]);
        $param_pimg = htmlspecialchars($_GET["product_image"]);      
        $param_cate = htmlspecialchars($_GET["category"]);
        $param_price = htmlspecialchars($_GET["price"]);
        $param_deliv = htmlspecialchars($_GET["delivery_meth"]);
        $param_weigh = htmlspecialchars($_GET["weight"]);
        $param_pref = htmlspecialchars($_GET["prefecture"]);
        $param_selid = htmlspecialchars($_GET["seller_id"]);
        // recipe_urlは任意
        if (isset($_GET["recipe_url"])) {
            $param_reurl = htmlspecialchars($_GET["recipe_url"]);
        } else {
            $param_reurl = '';
        }

        // SQL文をセット
        $sql = "INSERT INTO product VALUES (:product_id, :product_name, :product_desc, 
                :product_image, :recipe_url, :category, :price, :delivery_meth, NOW(),
                :weight, :prefecture, :seller_id, false)";
        $stmt = $db->prepare($sql);

        // idを自動追加する
        // 現在の最新IDの番号を取得
        $new_prnum = (int) $product_num[0]['product_id'];
        echo $new_prnum;
        echo gettype($new_prnum);

        // 新しいIDに1プラス
        $new_prnum += 1;
        echo $new_prnum;

        // 商品IDとしてふさわしい形（gXXXXXXX）にする．
        $new_proid = 'g'.str_pad(strval($new_prnum), 7, '0', STR_PAD_LEFT);

        // パラメーターをセット
        $stmt->bindValue(':product_id', $new_proid, PDO::PARAM_STR);
        $stmt->bindValue(':product_name', $param_pname, PDO::PARAM_STR);
        $stmt->bindValue(':product_desc', $param_pdesc, PDO::PARAM_STR);
        $stmt->bindValue(':product_image', $param_pimg, PDO::PARAM_STR);
        $stmt->bindValue(':recipe_url', $param_reurl, PDO::PARAM_STR);
        $stmt->bindValue(':category', $param_cate, PDO::PARAM_STR);
        $stmt->bindValue(':price', $param_price, PDO::PARAM_INT);
        $stmt->bindValue(':delivery_meth', $param_deliv, PDO::PARAM_STR);
        $stmt->bindValue(':weight', $param_weigh, PDO::PARAM_INT);
        $stmt->bindValue(':prefecture', $param_pref, PDO::PARAM_INT);
        $stmt->bindValue(':seller_id', $param_selid, PDO::PARAM_STR);

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