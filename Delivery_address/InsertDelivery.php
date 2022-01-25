<?php
/*
    作成者：坂口 白磨
    最終更新日：2022/1/18
    目的：  配送先情報を配送先デーブルに追加
            
    http通信例：
    http://localhost/software_engineering/Delivery_address/InsertDelivery.php?user_id=a0000001&real_name=KouNishimuta&telephone_number=07012345678&postal_code=4563219&address=高知県香美市土佐山田町369
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
    $data = "delivery_address";
    // URL後の各クエリストリングをGET
    if(isset($_POST["user_id"]) && 
       isset($_POST["real_name"]) && 
       isset($_POST["telephone_number"]) && 
       isset($_POST["postal_code"]) && 
       isset($_POST["address"])  ) {

        // 各クエリストリングをエスケープ(xss対策)
        $param_user_id = htmlspecialchars($_POST["user_id"]);
        $param_real_name = htmlspecialchars($_POST["real_name"]);
        $param_telephone_number = htmlspecialchars($_POST["telephone_number"]);      
        $param_postal_code = htmlspecialchars($_POST["postal_code"]);
        
        /* 最新の配送先IDを取得 */
        $sql = "SELECT d_address_id FROM $data WHERE user_id = :user_id ORDER BY d_address_id DESC LIMIT 1";
        // クエリ(問い合わせ)
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $param_user_id, PDO::PARAM_STR);
        $result = $stmt->execute();
        if (!$result) {
            // データベースとの接続を切断．
            unset($db);
            die('最新配送先IDの取得に失敗しました。');
        }
        echo 'user_idが'.$param_user_id.'の商品の最新配送先ID取得に成功しました';

        $d_address_id = $stmt->fetchAll(PDO::FETCH_ASSOC);
        print($d_address_id[0]["d_address_id"]);
        
        // SQL文をセット
        $sql = "INSERT INTO delivery_address VALUES (:d_address_id,:user_id,:real_name,:telephone_number,:postal_code,:address)";
        $stmt = $db->prepare($sql);

        // idを自動追加する
        // 現在の最新IDの番号を取得
        $new_d_address_id = (int) $d_address_id[0]['d_address_id'];
        echo $new_d_address_id;
        echo gettype($new_d_address_id);

        // 新しいIDに1プラス
        $new_d_address_id += 1;
        echo $new_d_address_id;

        
        // パラメーターをセット
        $stmt->bindValue(':d_address_id', $new_d_address_id, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $param_user_id, PDO::PARAM_STR);
        $stmt->bindValue(':real_name', $param_real_name, PDO::PARAM_STR);
        $stmt->bindValue(':telephone_number', $param_telephone_number, PDO::PARAM_STR);
        $stmt->bindValue(':postal_code', $param_postal_code, PDO::PARAM_STR);
        $stmt->bindValue(':address', $_POST['address'], PDO::PARAM_STR);
        
        
        // dbにexecute
        $result = $stmt->execute();
        if (!$result) {
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
