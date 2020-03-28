# 活動レポアップ用

define.php 定義ファイル

indexb2.php 送信フォーム

index2b2.php 画像処理、Wordpressへの投稿処理

初版作成後6年経過して、目についた所を少し手直し

* レスポンシブ対応
* 確認画面追加
* サイトのセキュア化対応(htts化に伴う投稿処理)
* 画像処理のオプション追加(*要Imagick)
* 名前指定して、プロフの読み込み、プロフアイコンの自動作成
* 処理中の際のローディング画面表示
* その他バグ修正、コードの整理

*Imagick 大抵のレンタルサーバには既に入ってるはず

(CentOSの場合)

> sudo yum -y install ImageMagick-devel  
> sudo yum -y install php-devel
> sudo pecl install Imagick  

...

インストールが完了後、
> sudo vi /etc/php.ini # extension=imagick.so をファイル内に追記。

最後にサーバを再起動
> sudo apachectl restart


