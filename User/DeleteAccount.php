<?php
/*
    作成者：松尾　匠馬
    最終更新日：2022/1/12
    目的：  ユーザテーブルからアカウント情報を削除
    入力：  user_id
    http通信例：
    http://localhost/OtegoLoss_WebAPI/User/DeleteAccount.php?user_id=u0000001
    
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


    // URL後のクエリストリング"user_id"をGET
    if(isset($_GET["user_id"])) {

        // クエリストリングをエスケープ(xss対策)
        $param_userid = htmlspecialchars($_GET["user_id"]);

        // 削除対象のユーザが出品した商品IDを取得するためのSQL文
        $sql_deleteproid = "SELECT product_id FROM product WHERE seller_id = :user_id";


        /* 削除する対象が存在するかどうか確認 */
        $sql = "SELECT * FROM user WHERE user_id = :user_id";
        // クエリ(問い合わせ)
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $param_userid, PDO::PARAM_STR);
        $stmt->execute();
        $count = $stmt->rowCount();
        if ($count == 0) {
            unset($db);
            die('user_idが'.$param_userid.'のアカウントは見つかりませんでした。');
        }
        echo 'user_idが'.$param_userid.'のアカウントが'.$count.'件見つかりました。';


         /* 購入した商品が完全に届いていなければ削除不可 */
         $sql = "SELECT delivery_status FROM purchase WHERE purchase_id = :user_id";
         // クエリ(問い合わせ)
         $stmt = $db->prepare($sql);
         $stmt->bindValue(':user_id', $param_userid, PDO::PARAM_STR);
         $stmt->execute();
         
         $arr = $stmt -> fetchAll(PDO::FETCH_ASSOC);

         foreach ($arr as $value) {
            if ($value == 0) {
                unset($db);
                die('user_idが'.$param_userid.'のアカウントが購入した商品は完全に届いていません。');
            }           
         }
         unset($value); // 最後の要素への参照を解除します
         echo 'user_idが'.$param_userid.'のアカウントが購入した商品は完全に届いています。';
    

          /* 出品した商品が完全に届いていなければ削除不可 */
          $sql = "SELECT delivery_status FROM purchase, product 
                    WHERE product.product_id = purchase.product_id
                    AND seller_id = :user_id";
          // クエリ(問い合わせ)
          $stmt = $db->prepare($sql);
          $stmt->bindValue(':user_id', $param_userid, PDO::PARAM_STR);
          $stmt->execute();
          
          $arr = $stmt -> fetchAll(PDO::FETCH_ASSOC);
 
          foreach ($arr as $value) {
             if ($value == 0) {
                 unset($db);
                 die('user_idが'.$param_userid.'のアカウントが出品した商品は完全に届いていません。');
             }           
          }
          unset($value); // 最後の要素への参照を解除します
          echo 'user_idが'.$param_userid.'のアカウントが出品した商品は完全に届いています。';

         

        /* お問い合わせテーブルでそのユーザIDに関する問い合わせ情報を削除（アカウント情報を削除するための準備） */
        $sql = "DELETE FROM question WHERE user_id = :user_id";
        $stmt = $db->prepare($sql);

        // パラメーターをセット
        $stmt->bindValue(':user_id', $param_userid, PDO::PARAM_STR);
        // dbにexecute
        $result = $stmt->execute();
        if (!$result) {
            print_r($stmt->errorinfo());
            unset($db);
            die('お問い合わせテーブル上でユーザID '.$param_userid.'の問い合わせ情報を削除できませんでした。');
        }
        echo 'お問い合わせテーブル上でユーザID '.$param_userid.'の問い合わせ情報を削除しました。';


        /* お気に入りテーブルのユーザIDに関するお気に入りテーブルの情報をすべて削除（アカウント情報を削除するための準備） */
        $sql = "DELETE FROM favorite WHERE user_id = :user_id OR favorite_user_id = :user_id";   
        $stmt = $db->prepare($sql);

        // パラメーターをセット
        $stmt->bindValue(':user_id', $param_userid, PDO::PARAM_STR);
        // dbにexecute
        $result = $stmt->execute();
        if (!$result) {
            print_r($stmt->errorinfo());
            unset($db);
            die('お気に入りテーブル上でユーザID '.$param_userid.'のお気に入り情報を削除できませんでした。');
        }
        echo 'お気に入りテーブル上でユーザID '.$param_userid.'のお気に入り情報を削除しました。';


        /* レビューテーブルのユーザIDに関するレビューテーブルの情報をすべて削除（アカウント情報を削除するための準備） */
        $sql = "DELETE FROM review WHERE user_id = :user_id OR review_user_id = :user_id";   
        $stmt = $db->prepare($sql);

        // パラメーターをセット
        $stmt->bindValue(':user_id', $param_userid, PDO::PARAM_STR);
        // dbにexecute
        $result = $stmt->execute();
        if (!$result) {
            print_r($stmt->errorinfo());
            unset($db);
            die('レビューテーブル上でユーザID '.$param_userid.'のレビュー情報を削除できませんでした。');
        }
        echo 'レビューテーブル上でユーザID '.$param_userid.'のレビュー情報を削除しました。';

        
        /* 生産者登録のユーザIDに関する生産者登録の情報をすべて削除（アカウント情報を削除するための準備） */
        $sql = "DELETE FROM producer WHERE user_id = :user_id";   
        $stmt = $db->prepare($sql);

        // パラメーターをセット
        $stmt->bindValue(':user_id', $param_userid, PDO::PARAM_STR);
        // dbにexecute
        $result = $stmt->execute();
        if (!$result) {
            print_r($stmt->errorinfo());
            unset($db);
            die('生産者登録テーブル上でユーザID '.$param_userid.'の生産者登録の情報を削除できませんでした。');
        }
        echo '生産者登録テーブル上でユーザID '.$param_userid.'の生産者登録の情報を削除しました。';
        

        /* アカウント通報テーブルのユーザIDに関するアカウント通報の情報をすべて削除（アカウント情報を削除するための準備） */
        $sql = "DELETE FROM report_user WHERE user_id = :user_id OR reported_id = :user_id";   
        $stmt = $db->prepare($sql);

        // パラメーターをセット
        $stmt->bindValue(':user_id', $param_userid, PDO::PARAM_STR);
        // dbにexecute
        $result = $stmt->execute();
        if (!$result) {
            print_r($stmt->errorinfo());
            unset($db);
            die('アカウント通報テーブル上でユーザID '.$param_userid.'のアカウント通報の情報を削除できませんでした。');
        }
        echo 'アカウント通報テーブル上でユーザID '.$param_userid.'のアカウント通報の情報を削除しました。';



        /* 商品通報テーブルのユーザIDに関する商品通報の情報をすべて削除（アカウント情報を削除するための準備） */
        $sql = "DELETE FROM report_product WHERE user_id = :user_id OR reported_id IN ($sql_deleteproid)";   
        $stmt = $db->prepare($sql);

        // パラメーターをセット
        $stmt->bindValue(':user_id', $param_userid, PDO::PARAM_STR);
        // dbにexecute
        $result = $stmt->execute();
        if (!$result) {
            print_r($stmt->errorinfo());
            unset($db);
            die('商品通報テーブル上でユーザID '.$param_userid.'の商品通報の情報を削除できませんでした。');
        }
        echo '商品通報テーブル上でユーザID '.$param_userid.'の商品通報の情報を削除しました。';


         /* コメントテーブルのユーザIDに関するコメントの情報をすべて削除（アカウント情報を削除するための準備） */
         $sql = "DELETE FROM comment WHERE user_id = :user_id OR product_id IN ($sql_deleteproid)";   
        $stmt = $db->prepare($sql);

        // パラメーターをセット
        $stmt->bindValue(':user_id', $param_userid, PDO::PARAM_STR);
        // dbにexecute
        $result = $stmt->execute();
        if (!$result) {
            print_r($stmt->errorinfo());
            unset($db);
            die('コメントテーブル上でユーザID '.$param_userid.'のコメントの情報を削除できませんでした。');
        }
        echo 'コメントテーブル上でユーザID '.$param_userid.'のコメントの情報を削除しました。';

    

         /* 購入テーブルのユーザIDに関する購入の情報をすべて削除（アカウント情報を削除するための準備） */
        $sql = "DELETE FROM purchase WHERE purchaser_id = :user_id OR product_id IN ($sql_deleteproid)";   
        $stmt = $db->prepare($sql);

        // パラメーターをセット
        $stmt->bindValue(':user_id', $param_userid, PDO::PARAM_STR);
        // dbにexecute
        $result = $stmt->execute();
        if (!$result) {
            print_r($stmt->errorinfo());
            unset($db);
            die('購入テーブル上でユーザID '.$param_userid.'の購入の情報を削除できませんでした。');
        }
        echo '購入テーブル上でユーザID '.$param_userid.'の購入の情報を削除しました。';


        /* クレカテーブルのユーザIDに関するクレカの情報をすべて削除（アカウント情報を削除するための準備） */
         $sql = "DELETE FROM credit_card WHERE user_id = :user_id";   
         $stmt = $db->prepare($sql);
 
         // パラメーターをセット
         $stmt->bindValue(':user_id', $param_userid, PDO::PARAM_STR);
         // dbにexecute
         $result = $stmt->execute();
         if (!$result) {
             print_r($stmt->errorinfo());
             unset($db);
             die('クレカテーブル上でユーザID '.$param_userid.'のクレカの情報を削除できませんでした。');
         }
         echo 'クレカテーブル上でユーザID '.$param_userid.'のクレカの情報を削除しました。';


        /* 配送先テーブルのユーザIDに関する配送先の情報をすべて削除（アカウント情報を削除するための準備） */
        $sql = "DELETE FROM delivery_address WHERE user_id = :user_id";   
        $stmt = $db->prepare($sql);

        // パラメーターをセット
        $stmt->bindValue(':user_id', $param_userid, PDO::PARAM_STR);
        // dbにexecute
        $result = $stmt->execute();
        if (!$result) {
            print_r($stmt->errorinfo());
            unset($db);
            die('配送先テーブル上でユーザID '.$param_userid.'の配送先の情報を削除できませんでした。');
        }
        echo '配送先テーブル上でユーザID '.$param_userid.'の配送先の情報を削除しました。';





         /* 商品テーブルのユーザID(出品者ID)に関する商品の情報をすべて削除（アカウント情報を削除するための準備） */
        $sql = "DELETE FROM product WHERE seller_id = :user_id";   
        $stmt = $db->prepare($sql);

        // パラメーターをセット
        $stmt->bindValue(':user_id', $param_userid, PDO::PARAM_STR);
        // dbにexecute
        $result = $stmt->execute();
        if (!$result) {
            print_r($stmt->errorinfo());
            unset($db);
            die('商品テーブル上でユーザID '.$param_userid.'の商品の情報を削除できませんでした。');
        }
        echo '商品テーブル上でユーザID '.$param_userid.'の商品の情報を削除しました。';



         
        


        /*　アカウント情報を削除　*/
        $sql = "DELETE FROM user WHERE user_id = :user_id";
        $stmt = $db->prepare($sql);

        // パラメーターをセット
        $stmt->bindValue(':user_id', $param_userid, PDO::PARAM_STR);

        // dbにexecute
        $result = $stmt->execute();
        if (!$result) {
            unset($db);
            die('アカウントの削除に失敗しました。');
        }
        echo 'アカウントの削除が完了しました';

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