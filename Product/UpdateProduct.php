<?php
/*
    作成者：植元 陸
    最終更新日：2022/1/18
    目的：  商品テーブルの商品詳細情報を更新
    入力：  product_id, product_name, product_desc, product_image, recipe_url, 
    　　　　category, price, delivery_meth, weight, prefecture
    http通信例：
    http://localhost/OtegoLoss_WebAPI/product/UpdateProduct.php?product_id=g0015003
    &product_name=僕の名前&product_image=fvsdlvjsk&category=%E9%AD%9A&price=3000&
    delivery_meth=%E5%86%B7%E5%87%8D&weight=500&prefecture=39&seller_id=u0000004
    
    $_POST：
    product_desc=
    recipe_url=

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
    if(isset($_POST["product_id"]) && isset($_POST["product_name"]) 
        && isset($_POST["product_desc"]) && isset($_POST["product_image"]) 
        && isset($_POST["recipe_url"]) && isset($_POST["category"]) && isset($_POST["price"]) 
        && isset($_POST["delivery_meth"]) && isset($_POST["weight"]) && isset($_POST["prefecture"])) {

        // 各クエリストリングをエスケープ(xss対策)
        $param_proid= htmlspecialchars($_POST["product_id"]);
        $param_pname= htmlspecialchars($_POST["product_name"]);
        $param_pimg = htmlspecialchars($_POST["product_image"]);      
        $param_cate = htmlspecialchars($_POST["category"]);
        $param_price = htmlspecialchars($_POST["price"]);
        $param_deliv = htmlspecialchars($_POST["delivery_meth"]);
        $param_weigh = htmlspecialchars($_POST["weight"]);
        $param_pref = htmlspecialchars($_POST["prefecture"]);
        $param_reurl = $_POST['recipe_url'];

        // recipe_urlは任意
        if ($_POST['recipe_url'] == '') {
            $param_reurl = NULL;
        }

        /* 変更する対象が存在するかどうか確認 */
        $sql = "SELECT * FROM product WHERE product_id = :product_id";
        // クエリ(問い合わせ)
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':product_id', $param_proid, PDO::PARAM_STR);
        $stmt->execute();
        $count = $stmt->rowCount();
        if ($count == 0) {
            // データベースとの接続を切断．
            print_r($stmt->errorinfo());
            unset($db);
            die('product_idが'.$param_proid.'の商品は見つかりませんでした。');
        }
        echo 'product_idが'.$param_proid.'の商品が'.$count.'件見つかりました。';

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
        $stmt->bindValue(':product_desc', $_POST["product_desc"], PDO::PARAM_STR);
        $stmt->bindValue(':product_image', $param_pimg, PDO::PARAM_STR);
        $stmt->bindValue(':recipe_url', $param_reurl, PDO::PARAM_STR);
        $stmt->bindValue(':category', $param_cate, PDO::PARAM_STR);
        $stmt->bindValue(':price', $param_price, PDO::PARAM_INT);
        $stmt->bindValue(':delivery_meth', $param_deliv, PDO::PARAM_STR);
        $stmt->bindValue(':weight', $param_weigh, PDO::PARAM_INT);
        $stmt->bindValue(':prefecture', $param_pref, PDO::PARAM_STR);

        // dbにexecute
        $result = $stmt->execute();
        if (!$result) {
            // データベースとの接続を切断．
            print_r($stmt->errorinfo());
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