# OtegoLoss_WebAPI

## Comment
* DeleteComment.php -- コメントテーブルからコメントを削除するWebAPI
* InsertComment.php -- コメントテーブルにコメントを追加するWebAPI
* ListingComment.php -- 商品に寄せられたコメント(ユーザIDとコメント内容)をすべて表示する

## Credit_card
* InsertCredit.php -- 決済情報をクレカテーブルに追加するWebAPI
* DeleteCredit.php -- クレカテーブルから決済情報を削除するWebAPI
* ListingCredit.php -- クレカ情報をすべて表示する(返す)WebAPI

## Delivery_address
* InsertDelivery.php -- 配送先情報を配送先テーブルに追加するWebAPI
* DeleteDelivery.php -- 配送先テーブルから配送先情報を削除するWebAPI
* ListingDelivery.php -- user_idをもとに配送先テーブルから配送先一覧を返すWebAPI

## Favorite
* InsertFavorite.php -- お気に入り情報をお気に入りテーブルに追加するWebAPI
* DeleteFavorite.php -- お気に入りテーブルからお気に入り情報を削除するWebAPI
* favorite.php -- お気に入り情報を返すWebAPI
* CheckFavorite.php -- ある出品者を、お気に入り登録しているか確認するWebAPI

## Producer
* InsertProducer.php -- 生産者情報を生産者テーブルに追加するWebAPI

## Product
* DeleteProduct.php -- 商品テーブルから商品を削除するWebAPI
* InsertProduct.php -- 商品テーブルに商品を追加するWebAPI
* ListingHistory.php -- 出品履歴に必要な情報を返すWebAPI
* ProductDetails.php -- 商品詳細画面に必要な商品詳細情報を返すWebAPI
* UpdateProduct.php -- 商品テーブルの商品詳細情報を更新するWebAPI
* HomeProduct.php -- 商品詳細画面に必要な商品詳細情報を返すWebAPI
* SearchProduct.php -- 指定した検索・並び替え条件を満たす商品情報を返すWebAPI

## Product&Purchase
* InsertPurchase.php -- 購入テーブルに購入情報を追加するWebAPI
* PurchaseHistory.php -- 購入履歴に必要な情報を返すWebAPI
* ListingDetails.php -- 出品履歴の商品詳細情報と配送状況を返すWebAPI
* AddWeight.php -- ユーザテーブルのgrpss_weightに購入した商品の重量を加算する

## Purchase
* PurchaseDelistatus.php -- （アカウント削除前）購入IDと配送状況を返すWebAPI
* UpdateDelistatus.php -- 購入テーブルの配送状況をtrueに更新するWebAPI

## Purchase&Delivery_address
* ShippingInfo.php -- 配送手続きに必要な配送先情報を返すWebAPI

## Report
* InsertReport.php -- 商品通報テーブル、アカウント通報テーブルに通報に関する情報を追加するWebAPI

## Review
* InsertReview.php -- レビューをレビューテーブルに追加するWebAPI
* Review.php -- レビューテーブルの生産者ID(ユーザID)からレビュー情報を返すWebAPI


## User
* InsertAccount.php -- アカウント情報をユーザテーブルに追加するWebAPI
* DeleteAccount.php -- ユーザテーブルからアカウント情報を削除するWebAPI
* ReturnPassFromEmail.php -- メールアドレスからパスワードを返すWebAPI
* ReturnUidFromWeight.php -- ユーザIDから重量を返す(ロス削減)WebAPI
* UpdateProfile.php -- ユーザIDで変更したプロフィール情報(プロフィール画像、プロフィールメッセージ、ユーザ名)をテーブルに更新するWebAPI
* UserProfile.php -- ユーザテーブルのユーザIDからユーザ情報(ユーザID以外すべて)を返すWebAPI


