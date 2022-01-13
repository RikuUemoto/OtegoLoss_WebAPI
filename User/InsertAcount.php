<?php
/*
    作成者：松尾　匠馬
    最終更新日：2022/1/12
    目的：アカウント情報をテーブルに追加
    入力：user_password, user_name, user_mail, (user_profile_image),
    　　　(user_profile_message), gross_weight
    ※ ()はNULL可

    http通信例：
    http://localhost/OtegoLoss_WebAPI/User/InsertAcount.php?user_password=abcdefghijk
    &user_name=tanaka&user_mail=test@kochi-tech.ac.jp&gross_weight=100
    &user_profile_image=aaaaaaaaaaaaaaaaaaa
    &user_profile_message=私は高知県出身の農家です。

    http://localhost/OtegoLoss_WebAPI/User/InsertAcount.php?user_password=abcdefghijk&
    user_name=tanaka&user_mail=test@kochi-tech.ac.jp&gross_weight=100
    &user_profile_image=aaaaaaaaaaaaaaaaaaa
    &user_profile_message=%E7%A7%81%E3%81%AF%E9%AB%98%E7%9F%A5%E7%9C%8C%E5%87%BA%E8%BA%AB%E3%81%AE%E8%BE%B2%E5%AE%B6%E3%81%A7%E3%81%99%E3%80%82

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

    /* 最新のユーザIDを取得 */
    $table = "SELECT CAST(RIGHT(user_id, 7) AS signed) AS user_id
                FROM user 
                ORDER BY CAST(RIGHT(user_id, 7) AS signed) DESC LIMIT 1";

    // クエリ(問い合わせ)
    $sql = $db->query($table);
    $user_num = $sql->fetchAll(PDO::FETCH_ASSOC);
    print($user_num[0]['user_id']);


    // URL後の各クエリストリングをGET
    if(isset($_GET["user_password"]) && isset($_GET["user_name"]) 
        && isset($_GET["user_mail"]) && isset($_GET["gross_weight"])) {


            // 各クエリストリングをエスケープ(xss対策)
            $param_upassword = htmlspecialchars($_GET["user_password"]);
            $param_uname = htmlspecialchars($_GET["user_name"]);
            $param_umail = htmlspecialchars($_GET["user_mail"]);
            $param_gweight = htmlspecialchars($_GET["gross_weight"]);
        
            // user_profile_imageは任意
            if (isset($_GET["user_profile_image"])) {
                $param_uprofileimg = htmlspecialchars($_GET["user_profile_image"]);
            } else {
                $param_uprofileimg = '';
            }

            // user_profile_messageは任意
            if (isset($_GET["user_profile_message"])) {
                $param_uprofilemes = htmlspecialchars($_GET["user_profile_message"]);
            } else {
                $param_uprofilemes = '';
            }

            // SQL文をセット
            $sql = "INSERT INTO user VALUES (:user_id, :user_password, :user_name, :user_mail, 
                    :user_profile_image, :user_profile_message, false, :gross_weight)";
             $stmt = $db->prepare($sql);

            // idを自動追加する
            // 現在の最新IDの番号を取得
            $new_unum = (int) $user_num[0]['user_id'];
            echo $new_unum;
            echo gettype($new_unum);

            // 新しいIDに1プラス
            $new_unum += 1;
            echo $new_unum;

            // ユーザIDとしてふさわしい形（uXXXXXXX）にする．
            $new_userid = 'u'.str_pad(strval($new_unum), 7, '0', STR_PAD_LEFT);

            // パラメーターをセット
            $stmt->bindValue(':user_id', $new_userid, PDO::PARAM_STR);
            $stmt->bindValue(':user_password', $param_upassword, PDO::PARAM_STR);
            $stmt->bindValue(':user_name', $param_uname, PDO::PARAM_STR);
            $stmt->bindValue(':user_mail', $param_umail, PDO::PARAM_STR);
            $stmt->bindValue(':user_profile_image', $param_uprofileimg, PDO::PARAM_STR);
            $stmt->bindValue(':user_profile_message', $param_uprofilemes, PDO::PARAM_STR);
            $stmt->bindValue(':gross_weight', $param_gweight, PDO::PARAM_INT);

            // dbにexecute
            $result = $stmt->execute();
            if (!$result) {
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