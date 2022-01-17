
<?php
/*
    作成者：坂口 白磨
    最終更新日：2022/1/
    目的：  生産者登録テーブルに生産者を追加
    http通信例：
    http://localhost/software_engineering/Producer/InsertProducer.php?user_id=&tel_number=&bank_name=&bank_branch_name=&bank_number=&bank_account_name=&id_image=
    
    
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


    // URL後の各クエリストリングをGET
    if(isset($_GET["user_id"]) 
    && isset($_GET["tel_number"]) 
    && isset($_GET["bank_name"]) 
    && isset($_GET["bank_branch_name"])
    && isset($_GET["bank_number"])
    && isset($_GET["bank_account_name"]) 
    && isset($_GET["id_image"]) 
    
    
    ) {

        // 各クエリストリングをエスケープ(xss対策)
        $param_user_id= htmlspecialchars($_GET["user_id"]); 
        $param_tel_number= htmlspecialchars($_GET["tel_number"]); 
        $param_bank_name = htmlspecialchars($_GET["bank_name"]);
        $param_bank_branch_name = htmlspecialchars($_GET["bank_branch_name"]);
        $param_bank_number = htmlspecialchars($_GET["bank_number"]);
        $param_bank_account_name = htmlspecialchars($_GET["bank_account_name"]); 
        $param_id_image = htmlspecialchars($_GET["id_image"]); 
        
        
        // SQL文をセット
        $sql = "INSERT INTO producer VALUES (:user_id,:tel_number,:bank_name,:bank_branch_name,:bank_number,:bank_account_name,:id_image,false,NULL)";
        $stmt = $db->prepare($sql);


        // パラメーターをセット
        $stmt->bindValue(':user_id', $param_user_id, PDO::PARAM_STR);
        $stmt->bindValue(':tel_number', $param_tel_number, PDO::PARAM_STR);
        $stmt->bindValue(':bank_name', $param_bank_name, PDO::PARAM_STR);
        $stmt->bindValue(':bank_branch_name', $param_bank_branch_name, PDO::PARAM_STR);
        $stmt->bindValue(':bank_number', $param_bank_number, PDO::PARAM_STR);
        $stmt->bindValue(':bank_account_name', $param_bank_account_name, PDO::PARAM_STR);
        $stmt->bindValue(':id_image', $param_id_image, PDO::PARAM_STR);
        
        
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