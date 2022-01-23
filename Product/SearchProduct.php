<?php
/*
    作成者：植元 陸
    最終更新日：2022/1/19
    目的：  指定した検索・並び替え条件を満たす商品情報を返す
    入力：  product_name, category, price, delivery_meth, listing_date,
    　　　　weight, prefecture, user_name, sort_condition
    http通信例：
    https://ec2-13-114-108-27.ap-northeast-1.compute.amazonaws.com/SearchProduct.php?
    sort_item=price&sort_order=desc&product_name=i&category=魚&delivery_meth=冷凍
    prefecture=39&user_name=ab&lweight=0&hweight=1000&lprice=2000&hprice=3000

    その他：
*/

//json形式ファイルのheader
header("Content-Type: application/json; charset=utf-8");

// DBとの連携
try{
    $db = new PDO('mysql:dbname=software;host=localhost;charset=utf8','root','root');
    //echo "接続OK";
    // データベース
    $data_pro = "product";
    $data_usr = "user";

    // 1つ以上検索条件が入力されていた場合
    if(($_GET["product_name"] != '') || ($_GET["category"] != '')
        || ($_GET["lprice"] != '') || ($_GET["hprice"] != '')
        || ($_GET["delivery_meth"] != '')
        /*|| ($_GET["date_ago"] != '')*/ || ($_GET["lweight"] != '')
        || ($_GET["hweight"] != '') || ($_GET["prefecture"] != '') || ($_GET["user_name"] != '') ) {

        // 各クエリストリングをエスケープ(xss対策)
        $param_pname= htmlspecialchars($_GET["product_name"]);
        $param_cate = htmlspecialchars($_GET["category"]);
        $param_lprice = htmlspecialchars($_GET["lprice"]);
        $param_hprice = htmlspecialchars($_GET["hprice"]);
        $param_deliv = htmlspecialchars($_GET["delivery_meth"]);
        //$param_dateago = htmlspecialchars($_GET["date_ago"]);
        $param_lweight = htmlspecialchars($_GET["lweight"]);
        $param_hweight = htmlspecialchars($_GET["hweight"]);
        $param_pref = htmlspecialchars($_GET["prefecture"]);
        $param_uname = htmlspecialchars($_GET["user_name"]);
        $param_sitem = htmlspecialchars($_GET["sort_item"]);
        $param_sorder = htmlspecialchars($_GET["sort_order"]);

        /* $sqlに実行するSQL文を作成していく */
        $sql = "SELECT product_id, product_name, product_image, price, seller_id
                    FROM $data_pro, $data_usr WHERE seller_id = user_id 
                    AND purchased = false ";
        
        /* 検索条件によってSQL文を作成 */
        // 商品名による検索
        if($_GET["product_name"] != '') {
            $sql = $sql."AND product_name LIKE N'%$param_pname%' ";
        }

        // カテゴリによる検索
        if($_GET["category"] != '') {
            $sql = $sql."AND category = N'$param_cate' ";
        }

        // 金額による検索
        if($_GET["lprice"] != '') {
            if($param_hprice == '') {
                $sql = $sql."AND price > $param_lprice" ;
            } else {
                $sql = $sql."AND price BETWEEN ($param_lprice + 1) AND $param_hprice ";
            }
        }
        
        // 配達方法による検索
        if($_GET["delivery_meth"] != '') {
            $sql = $sql."AND delivery_meth = N'$param_deliv' ";
        }

        // 出品日時による検索
        /*
        
        */

        // 重さによる検索
        if($_GET["lweight"] != '') {
            if($param_hweight == '') {
                $sql = $sql."AND weight > $param_lweight" ;
            } else {
                $sql = $sql."AND weight BETWEEN ($param_lweight + 1) AND $param_hweight ";
            }
        }

        // 地域による検索
        if($_GET["prefecture"] != '') {
            $sql = $sql."AND prefecture = $param_pref ";
        }

        // 出品者名による検索
        if($_GET["user_name"] != '') {
            $sql = $sql."AND user_name LIKE N'%$param_uname%' ";
        }

        // 並び替え（指定できるのはprice, weight, listing_date）
        if($_GET["sort_item"] != '') {
            $sql = $sql."ORDER BY $param_sitem $param_sorder ";
        }

        // ここまででSQL文の作成が完了
        $sql = $sql.';';


        // sql文の実行，結果の取得
        $arr["status"] = "yes";
        $stmt = $db->query($sql);
        
        $arr = $stmt -> fetchAll(PDO::FETCH_ASSOC);

    } else {
        // 1つも検索条件が入力されていなかった場合，入力されていないことを伝える
        // statusを見てフロント側でホーム画面に表示する最新20個でも表示すればいい
        $arr['status'] = 'no';
        echo "検索条件が入力されていません．";
    }

    // 配列をjson形式にデコードして出力, 第二引数は、整形するためのオプション
    print json_encode($arr, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch(PDOException $e) {
    echo "error".$e->getMessage();
}

// データベースとの接続を切断．
unset($db);

?>