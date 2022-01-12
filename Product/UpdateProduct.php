<?php
/*
    作成者：植元 陸
    最終更新日：2022/1/13
    目的：  商品テーブルの商品詳細情報を更新
    入力：  product_id, product_name, product_desc, product_image, recipe_url, 
    　　　　category, price, delivery_meth, weight, prefecture
    http通信例：
    http://localhost/OtegoLoss_WebAPI/product/UpdateProduct.php?product_name=a
    &product_desc=&product_image=a&category=%E9%AD%9A&price=3000
    &delivery_meth=%E5%86%B7%E8%94%B5&weight=500&prefecture=1&seller_id=u0000008
    &recipe_url=s&product_id=g0000111
    
    その他：購入済みの商品の商品詳細情報は変更できないように処理を記述した方が良いのか？
    　　　　recipe_urlに関しては空文字列を送ってもらう
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
    $data = "product";

    // URL後の各クエリストリングをGET
    if(isset($_GET["product_id"]) && isset($_GET["product_name"]) 
        && isset($_GET["product_desc"]) && isset($_GET["product_image"]) 
        && isset($_GET["recipe_url"]) && isset($_GET["category"]) && isset($_GET["price"]) 
        && isset($_GET["delivery_meth"]) && isset($_GET["weight"]) && isset($_GET["prefecture"])) {

        // 各クエリストリングをエスケープ(xss対策)
        $param_proid= htmlspecialchars($_GET["product_id"]);
        $param_pname= htmlspecialchars($_GET["product_name"]);
        $param_pdesc = htmlspecialchars($_GET["product_desc"]);
        $param_pimg = htmlspecialchars($_GET["product_image"]);      
        $param_reurl = htmlspecialchars($_GET["recipe_url"]);      
        $param_cate = htmlspecialchars($_GET["category"]);
        $param_price = htmlspecialchars($_GET["price"]);
        $param_deliv = htmlspecialchars($_GET["delivery_meth"]);
        $param_weigh = htmlspecialchars($_GET["weight"]);
        $param_pref = htmlspecialchars($_GET["prefecture"]);

        // 日本語文字列を使うCHECK制約
        if(strcmp($param_cate, "野菜") != 0 && strcmp($param_cate, "魚") != 0) {
            // データベースとの接続を切断．
            unset($db);
            die('変更失敗しました。categoryには"野菜"か"魚"を指定してください。');
        }

        if(strcmp($param_deliv, "普通") != 0 && strcmp($param_deliv, "冷蔵") != 0 
                                                    && strcmp($param_deliv, "冷凍") != 0) {
            // データベースとの接続を切断．
            unset($db);
            die('変更失敗しました。delivery_methには"普通"か"冷蔵"か"冷凍"を指定してください。');
        }

        // SQL文をセット
        $sql = "UPDATE $data 
                SET product_name = :product_name, product_desc = :product_desc, 
                    product_image = :product_image, recipe_url = :recipe_url, 
                    category = :category, price = :price, 
                    delivery_meth = :delivery_meth, weight = :weight, 
                    prefecture = :prefecture
                WHERE product_id = :product_id";

        $stmt = $db->prepare($sql);

        // パラメーターをセット
        $stmt->bindValue(':product_id', $param_proid, PDO::PARAM_STR);
        $stmt->bindValue(':product_name', $param_pname, PDO::PARAM_STR);
        $stmt->bindValue(':product_desc', $param_pdesc, PDO::PARAM_STR);
        $stmt->bindValue(':product_image', $param_pimg, PDO::PARAM_STR);
        $stmt->bindValue(':recipe_url', $param_reurl, PDO::PARAM_STR);
        $stmt->bindValue(':category', $param_cate, PDO::PARAM_STR);
        $stmt->bindValue(':price', $param_price, PDO::PARAM_INT);
        $stmt->bindValue(':delivery_meth', $param_deliv, PDO::PARAM_STR);
        $stmt->bindValue(':weight', $param_weigh, PDO::PARAM_INT);
        $stmt->bindValue(':prefecture', $param_pref, PDO::PARAM_INT);

        // dbにexecute
        $result = $stmt->execute();
        if (!$result) {
            // データベースとの接続を切断．
            unset($db);
            die('商品詳細情報の更新処理に失敗しました。');
        }
        echo '商品詳細情報の更新処理が完了しました';

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