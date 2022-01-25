<?php
/*
    作成者：植元 陸
    最終更新日：2022/1/24
    目的：  商品テーブルに商品を追加
    入力：  product_name, product_desc, product_image, recipe_url, category, price,
    　　　　delivery_meth, weight, prefecture, seller_id

    http通信例：
    http://localhost/OtegoLoss_WebAPI/Product/InsertProduct.php?product_name=僕の名前&product_image=fvsdlvjsk&
    category=%E9%AD%9A&price=3000&delivery_meth=%E5%86%B7%E5%87%8D&weight=500&prefecture=39&seller_id=u0000004
    
    $_POST：
    product_desc=
    recipe_url=

    その他：product_imageは画像パスを格納，画像自体はサーバのローカルディスク上とか
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
    if(isset($_POST["product_name"]) && isset($_POST["product_desc"]) 
        && isset($_POST["product_image"]) && isset($_POST["category"]) 
        && isset($_POST["price"]) && isset($_POST["delivery_meth"]) && isset($_POST['recipe_url']) 
        && isset($_POST["weight"]) && isset($_POST["prefecture"]) && isset($_POST["seller_id"])) {

        // 各クエリストリングをエスケープ(xss対策)
        $param_pname= htmlspecialchars($_POST["product_name"]);
        $param_pimg = htmlspecialchars($_POST["product_image"]);      
        $param_cate = htmlspecialchars($_POST["category"]);
        $param_price = htmlspecialchars($_POST["price"]);
        $param_deliv = htmlspecialchars($_POST["delivery_meth"]);
        $param_weigh = htmlspecialchars($_POST["weight"]);
        $param_pref = htmlspecialchars($_POST["prefecture"]);
        $param_selid = htmlspecialchars($_POST["seller_id"]);
        $param_reurl = $_POST['recipe_url'];

        // recipe_urlは任意
        if ($_POST['recipe_url'] == '') {
            $param_reurl = NULL;
        }

        // 日本語文字列を使うCHECK制約
        if(strcmp($param_cate, "野菜") != 0 && strcmp($param_cate, "魚") != 0) {
            // データベースとの接続を切断．
            unset($db);
            die('登録失敗しました。categoryには"野菜"か"魚"を指定してください。');
        }

        if(strcmp($param_deliv, "普通") != 0 && strcmp($param_deliv, "冷蔵") != 0 
                                                    && strcmp($param_deliv, "冷凍") != 0) {
            // データベースとの接続を切断．
            unset($db);
            die('登録失敗しました。delivery_methには"普通"か"冷蔵"か"冷凍"を指定してください。');
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
        $stmt->bindValue(':product_desc', $_POST["product_desc"], PDO::PARAM_STR);
        $stmt->bindValue(':product_image', $param_pimg, PDO::PARAM_STR);
        $stmt->bindValue(':recipe_url', $param_reurl, PDO::PARAM_STR);
        $stmt->bindValue(':category', $param_cate, PDO::PARAM_STR);
        $stmt->bindValue(':price', $param_price, PDO::PARAM_INT);
        $stmt->bindValue(':delivery_meth', $param_deliv, PDO::PARAM_STR);
        $stmt->bindValue(':weight', $param_weigh, PDO::PARAM_INT);
        $stmt->bindValue(':prefecture', $param_pref, PDO::PARAM_STR);
        $stmt->bindValue(':seller_id', $param_selid, PDO::PARAM_STR);

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