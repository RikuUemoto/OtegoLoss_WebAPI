# OtegoLoss_WebAPI

## Comment
* DeleteComment.php -- コメントテーブルからコメントを削除するWebAPI
* InsertComment.php -- コメントテーブルにコメントを追加するWebAPI

## Product
* DeleteProduct.php -- 商品テーブルから商品を削除するWebAPI
* InsertProduct.php -- 商品テーブルに商品を追加するWebAPI
* ListingHistory.php -- 出品履歴に必要な情報を返すWebAPI
* ProductDetails.php -- 商品詳細画面に必要な商品詳細情報を返すWebAPI

## Product&Purchase
* InsertPurchase.php -- 購入テーブルに購入情報を追加するWebAPI
* PurchaseHistory.php -- 購入履歴に必要な情報を返すWebAPI
* ListingDetails.php -- 出品履歴の商品詳細情報と配送状況を返すWebAPI

## Purchase
* PurchaseDelistatus.php -- （アカウント削除前）購入IDと配送状況を返すWebAPI
* UpdateDelistatus.php -- 購入テーブルの配送状況をtrueに更新するWebAPI

## Purchase&Delivery_address
* ShippingInfo.php -- 配送手続きに必要な配送先情報を返すWebAPI

## User
* InsertAcount.php -- アカウント情報をユーザテーブルに追加するWebAPI
* DeleteAcount.php -- ユーザテーブルからアカウント情報を削除するWebAPI
* ReturnPassFromEmail.php -- メールアドレスからパスワードを返すWebAPI
* ReturnUidFromWeight.php -- ユーザIDから重量を返す(ロス削減)WebAPI