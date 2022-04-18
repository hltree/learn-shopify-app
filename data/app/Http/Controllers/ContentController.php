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
                    'content' => <<<HTML
試しに適当なページを編集して、Shopify側に表示されているアプリページをリロードしてみてください。<br>
変更が反映されているはずです！<br><br>
これは単にngrokで生成したページを表示しているだけなので、デプロイするときはアプリ用ページを用意してShopifyに認証させるだけで良さそうですね！
HTML
                ],
                [
                    'title' => 'アプリからページを新規作成する',
                    'content' => <<<HTML
HTML
                ]
            ]
        ]);
    }
}
