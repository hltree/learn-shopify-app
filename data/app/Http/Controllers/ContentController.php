<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContentController extends Controller
{

    protected array $viewParams = [
        'title' => null,
        'lead' => null,
        'sections' => [
            [
                // available html or plain
                'title' => '',
                // available html or plain
                'content' => ''
            ]
        ]
    ];

    public function index(string $pageName)
    {
        if (!method_exists($this, $pageName)) {
            echo __METHOD__ . "に${pageName}メソッドが存在しません";
            die();
        }

        $Method = new \ReflectionMethod($this, $pageName);
        if (!$Method->isProtected()) {
            echo 'メソッドのアクセス修飾子はprotectedにしてください';
            die();
        }

        $this->$pageName();
        return view('content.layout', $this->viewParams);
    }

    protected function step0(): void
    {
        $this->viewParams = array_merge($this->viewParams, [
            'title' => '',
            'lead' => '',
            'sections' => [
                [
                    'title' => 'ストレージにアクセスできるようにする',
                    'content' => <<<HTML
画像が正しく反映されない場合はストレージのアクセスを許可してください。まずはDockerのコンテナにアクセスします
<pre><code>
$ docker exec -it php bash
</code></pre>
コンテナ名がphpではない場合は適宜変更してください<br>
正しくアクセスできた場合は下記のようになるはずです
<pre><code>
root@85a296c85c1f:/var/www/html#
</code></pre>
その場で下記のコマンドを入力してください
<pre><code>
root@85a296c85c1f:/var/www/html# php artisan storage:link
</code></pre>
HTML

                ]
            ]
        ]);
    }

    protected function step1(): void
    {
        $this->viewParams = array_merge($this->viewParams, [
            'title' => 'アプリ開発を始める',
            'lead' => '
<p>このページにある手順を上から順に行えばアプリ開発に触れることができます。先に進む前に確認してほしいのは以下の2点だけです！</p>
<ul><li><a href="https://www.shopify.com/partners">https://www.shopify.com/partners</a>からパートナーズに登録しておいてください！</li>
<li>このページが正しく見えない場合は<a href="' . route('content.page', ['pageName' => 'step0']) . '">こちら</a>をみてください！</li>
</ul>
'
            ,
            'sections' => [
                [
                    'title' => '.envファイルを適切にする',
                    'content' => <<<HTML
data/.envファイルを開いて、下記の値を適切に変更しましょう
<pre><code>
APP_URL=https://236a-219-117-222-64.ngrok.io
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=database
DB_USERNAME=user
DB_PASSWORD=password
SHOP_URL=あなたのShopifyURL
SHOP_API_KEY=あなたのShopifyアプリのAPIキー
SHOP_SECRET_KEY=あなたのShopifyアプリのシークレットキー
SHOP_API_ALLOW_SCOPE=特別にアクセス権を変更する必要があれば設定してください（不要な場合は項目自体書かないで）
</code></pre>
APP_URLはポート番号がデフォルトの場合、localhost:4040にアクセスしたときに表示されるhttpsから始まるランダムなURLを入力しましょう<br>
<b>末尾のスラッシュは必要ありません！</b><br><br>
DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD は Docker の.envの設定を確認して、同じ値を入力しましょう。<br>
.envが存在しない場合は.env.exampleをみてください<br><br>
HTML
                ],
                [
                    'title' => 'ngrokで生成されたURLでアクセスしなおす',
                    'content' => <<<HTML
このページをlocalhostから閲覧している場合は.envに設定したAPP_URLからこのページにアクセスしなおしましょう<br>
特別な設定は必要ありません。ただアクセスするだけです
HTML
                ],
                [
                    'title' => 'データベースを最適化する',
                    'content' => <<<HTML
下記のコードでDockerコンテナにアクセスします。（コンテナ名がphpでない場合は適宜変更してください）<br>
<pre><code>
$ docker exec -it php bash
</code></pre>
アクセスできたら、下記コードでデータベースを最適化します。
<pre><code>
root@85a296c85c1f:/var/www/html# php artisan migrate
Migration table created successfully.
Migrating: 2014_10_12_000000_create_users_table
Migrated:  2014_10_12_000000_create_users_table (118.66ms)
Migrating: 2014_10_12_100000_create_password_resets_table
Migrated:  2014_10_12_100000_create_password_resets_table (104.12ms)
Migrating: 2019_08_19_000000_create_failed_jobs_table
Migrated:  2019_08_19_000000_create_failed_jobs_table (88.20ms)
Migrating: 2019_12_14_000001_create_personal_access_tokens_table
Migrated:  2019_12_14_000001_create_personal_access_tokens_table (148.62ms)
Migrating: 2022_04_18_002557_create_options_table
Migrated:  2022_04_18_002557_create_options_table (53.45ms)
</code></pre>

.envにてポートの変更を行っていなければ、localhost:8080でphpmyadminにアクセスできますので、テーブルが正しく作成できているかは念の為こちらでも確認しておきましょう！
HTML

                ],
                [
                    'title' => 'ストアを作成する',
                    'content' => <<<HTML
<a href="https://partners.shopify.com/organizations">https://partners.shopify.com/organizations</a>にアクセスして、左のナビゲーションから「Stores」をクリックしましょう。<br>
あなたのストア一覧表示されるはずです。それらは無視してアプリ開発用に新規ストアを開設します。中央右上辺りにある「Add Store」のボタンをクリックしましょう。<br>
色々と入力するフィールドが表示されるので、下記のように適当に入力します<br><br>
<img src="/storage/images/step0-img1.png" alt=""><br><br>
作成できたらストアのURLを控えておきましょう（上記の場合はxn-68jp0cyh0d6fa6htab.myshopify.comです）<br>
ここまででストアの作成は完了となります
HTML

                ],
                [
                    'title' => 'アプリを作成する',
                    'content' => <<<HTML
<a href="https://partners.shopify.com/organizations">https://partners.shopify.com/organizations</a>にアクセスして、左のナビゲーションから「Apps」をクリックしましょう。<br>
あなたの所有するアプリ一覧表示されるはずです。<br><br>
<img src="/storage/images/step1-img2.png" alt="" /><br><br>
中央右上辺りにある「Create App」のボタンをクリックしましょう。<br>
「Custom App」か「Public App」を選択できます。今回は<b>Public App</b>を選択します。<br><br>
App nameは何でも構いません。App URLは先ほど.envに入力したAPP_URLに、Redirection URLはAPP_URLに/authRedirectを付けたURLを入力しましょう。<br><br>
<img src="/storage/images/step1-img3.png" alt="" /><br><br>
入力できたら右上にあるCreate Appボタンをクリックします。<br>
しばらくするとアプリの設定ページへ自動的に移動します。ページ中頃にAPI Keysの項目を確認できると思います。<br>
これをdata/.envファイルのSHOP_API_KEYとSHOP_SECRET_KEYに設定します。<br><br>
<img src="/storage/images/step1-img4.png" alt="" /><br><br>
上記でしたら、
<pre><code>
SHOP_API_KEY=4dc35d3c0b3c7e95c0771ab17856f55c
SHOP_SECRET_KEY=****************（念の為伏せています）
</code></pre>
と入力しましょう。<br><br>
完了したらDockerコンテナにアクセスして下記コマンドを実行します。<br>
<pre><code>
root@85a296c85c1f:/var/www/html# php artisan config:clear
</code></pre>
</code></pre>
HTML
                ],
                [
                    'title' => 'ストアにアプリをインストールする',
                    'content' => <<<HTML
「アプリを作成する」のセクションで使用したアプリの設定ページに再度アクセスします。<br>
ページ中央のMore Actionsタブに「Test on development store」の項目があるので、これをクリックしましょう。<br><br>
<img src="/storage/images/step1-img5.png" alt="" /><br><br>
先ほど作成したストアが一覧にあると思いますので、クリックしてインストールします。<br><br>
<img src="/storage/images/step1-img6.png" alt="" />
HTML
                ],
                [
                    'title' => 'ストアにアプリを認証する',
                    'content' => <<<HTML
アプリを認証していきます。認証を通さないと何もできません。<br>
まずはAPP_URL + /sendAuthorize のURLにアクセスしましょう。<br><br>
<img src="/storage/images/step1-img6.png" alt="" /><br><br>
上記の画面が出ればOKです。install unlisted appのボタンをクリックしましょう！<br>
しばらく待つとShopifyのアプリ管理画面に移動し、APP_URLのページと同じ画面を確認できます！<br><br>
<img src="/storage/images/step1-img8.png" alt="" />
HTML
                ],
                [
                    'title' => 'アプリのレイアウトを変更する',
                    'content' => '
試しに適当なページを編集して、Shopify側に表示されているアプリページをリロードしてみてください。<br>
変更が反映されているはずです！<br><br>
これは単にngrokで生成したページを表示しているだけなので、デプロイするときはアプリ用ページを用意してShopifyに認証させるだけで良さそうですね！<br>
<a href="' . route('content.page', ['pageName' => 'step2']) . '">次のステップ</a>では、アプリからストアを実際にカスタマイズする方法をご紹介します。
'
                ]
            ]
        ]);
    }

    protected function step2(): void
    {
        $this->viewParams = array_merge($this->viewParams, [
            'title' => 'アプリからページを操作する',
            'lead' => '
<p><a href="' . route('content.page', ['pageName' => 'step1']) . '">ステップ1</a>ではアプリの作成とストアへのインストールを行いました。<br>
次はこのアプリを使って、実際にストアをカスタマイズしてみます。</p>
'
            ,
            'sections' => [
                [
                    'title' => 'アプリからページを新規作成する',
                    'content' => 'このステップではShopifyに新規ページを作成してみます。（ページとは、WordPressで例えると固定ページのことです！）<br>
まずは<a href="' . route('page.create') . '">こちら</a>にアクセスしましょう。<br><br>
<img src="/storage/images/step1-img9.png" alt="" /><br><br>
このようなフォームが表示されるはずです。<br>
適当にページタイトルとコンテンツを入力して送信してみましょう。<br><br>
<img src="/storage/images/step1-img10.png" alt="" /><br><br>
すると、このようなページに移動するはずです<br><br>
<img src="/storage/images/step1-img11.png" alt="" /><br><br>
こちら のリンクをクリックしましょう。送信したフォーム通りの内容でページが作成されているはずです
'
                ],
                [
                    'title' => 'アプリから作成したページを編集する',
                    'content' => '
                    先ほど作成したページを編集してみましょう。<br>
                    作成したページの<a href="' . route('page.list') . '">一覧ページ</a>にアクセスしてください。<br>
                    <img src="/storage/images/step2-img1.png" alt="" /><br><br>
                    <b>このアプリから投稿したページの一覧</b>が表示されると思いますので、先ほど投稿したページ横のeditボタンを押しましょう。<br>
                    新規投稿したときと同じフォームが表示されると思います。ここのタイトルを変更して、送信してみましょう。例ではタイトルを「がギグ」と入力してみます。<br><br>
                    <img src="/storage/images/step2-img2.png" alt="" /><br><br>
                    送信します。<br>
                    正常に処理されるとこのようになります。<br><br>
                    <img src="/storage/images/step2-img3.png" alt="" /><br><br>
                    ストアのページにも変更の反映を確認できます。<br><br>
                    <img src="/storage/images/step2-img4.png" alt="" /><br><br>
                    '
                ],
                [
                    'title' => 'アプリから作成したページを削除する',
                    'content' => '
                    先ほど編集したページを削除してみましょう！<br>
                    技術的には先に行ったステップと変わりありませんので、機能は作成していません。<br><br>
                    ご興味があれば実装してみてください！
                    '
                ]
            ]
        ]);
    }

    protected function step3(): void
    {
        $this->viewParams = array_merge($this->viewParams, [
            'title' => '注文をCSVに出力する',
            'lead' => '
<p><a href="' . route('content.page', ['pageName' => 'step2']) . '">ステップ2</a>ではアプリの作成とストアへのインストールを行いました。<br>
次はこのアプリを使って、注文を指定形式のCSVに出力してみましょう。サンプルとして、注文データを<a href="https://business.kuronekoyamato.co.jp/service/lineup/b2/index.html">ヤマト運輸 B2クラウド</a>に取り込むCSVに変換してみます。（完全なデータではないので、あくまでも参考程度にしてください）</p>
'
            ,
            'sections' => [
                [
                    'title' => '注文を作成する',
                    'content' => '
出力できる注文がないといけませんので、まずは注文を作成します。（既に複数の注文のある方はこの作業はスキップしても構いません）<br>
ストア管理画面のトップへ移動して、左サイドナビの「Orders」をクリックします。<br><br>
<img src="/storage/images/step3-img1.png" alt="" /><br><br>
中央の緑ボタン「Create order」をクリックしましょう。<br>
このような画面になるはずです<br><br>
<img src="/storage/images/step3-img2.png" alt="" /><br><br>
画面右側のCustomerカードの中にある「Search or create a customer」にフォーカスしてください。<br>
注文対象となる顧客のいる場合はその顧客を選択してください。いない場合は作成します。今回はいない場合で進行します<br>
「Create a new customer」をクリックします。<br><br>
<img src="/storage/images/step3-img3.png" alt="" /><br><br>
適当に入力して、保存します。<br>
今登録した顧客を選択した状態で「Add custom item」をクリックします。<br><br>
<img src="/storage/images/step3-img4.png" alt="" /><br><br>
適当に入力して、Doneします。<br><br>
<img src="/storage/images/step3-img5.png" alt="" /><br><br>
下部の「Collect payment」から「Mark as paid」（支払い済）をクリックします。<br><br>
<img src="/storage/images/step3-img6.png" alt="" /><br><br>
オーダーを作成するか確認画面のモーダルが出ると思うので、「Create Order」をクリックします。<br><br>
<img src="/storage/images/step3-img7.png" alt="" /><br><br>
注文として登録されました。注文には<a href="https://help.shopify.com/ja/manual/orders/order-status#part-2d2febdb0d61860a">さまざまなステータス</a>がありますので、同じ手順で複数パターン登録しておくとよいでしょう。
'
                ],
                [
                    'title' => '注文をCSVで出力する',
                    'content' => '
                    <a href="' . route('csv.index') .'">CSV出力のルート</a>に移動します。<br>
                    このようなフォームが出力されているはずです。<br><br>
                    <img src="/storage/images/step3-img8.png" alt="" /><br><br>
                    先に登録したデータは Unfulfilled（未発送）かつ未アーカイブでした。フォームにはこの条件で送信してみます。<br><br>
                    <img src="/storage/images/step3-img9.png" alt="" /><br><br>
                    正常に処理を完了するとCSVをダウンロードできます。今回はダウンロードしたCSVをNumbers（Appleのアプリ）で開いてみます。<br><br>
                    <img src="/storage/images/step3-img10.png" alt="" /><br><br>
                    注文に合ったデータで出力されていますね！<br>
                    配達日等の出力条件を整えないといけなかったり、郵便番号が数字と認識されて 0 になっていたり、文字コードを調整する必要があったりと調整事項はかなりありますが、
                    とりあえず出力できたのでOKとします。<br>
                    上記の調整を施したり、出力形式を変更してみたりなどぜひカスタマイズに挑戦してみてください！
                    '
                ]
            ]
        ]);
    }
}
