<?php

namespace App\Http\Controllers;

use App\Models\CreatedPageLog;
use App\Models\EditedPageLog;
use App\Models\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PHPShopify\ShopifySDK;

class PageController extends Controller
{
    public function create()
    {
        return view('page.create');
    }

    public function new(Request $request)
    {
        $this->validator($request->all())->validate();

        $Option = new Option();
        $accessToken = $Option->getAccessToken();

        if (!$accessToken) {
            die('アクセストークンがありません');
        }

        $Shopify = new ShopifySDK([
            'ShopUrl' => config('app.shopUrl'),
            'AccessToken' => $accessToken
        ]);

        $pageInfo = array(
            'title' => $request->get('title'),
            'body_html' => $request->get('content'),
        );

        $pageArray = $Shopify->Page->post($pageInfo);
        if (is_array($pageArray)) {
            CreatedPageLog::create([
                'title' => $pageArray['title'],
                'page_id' => $pageArray['id'],
                'shop_id' => $pageArray['shop_id'],
                'handle' => $pageArray['handle'],
                'body_html' => $pageArray['body_html'],
                'author' => $pageArray['author'],
                'published_at' => $pageArray['published_at'],
                'template_suffix' => $pageArray['template_suffix'],
                'admin_graphql_api_id' => $pageArray['admin_graphql_api_id']
            ]);
        }

        return view('page.created', [
            'pageUrl' => 'https://' . config('app.shopUrl') . '/admin/pages/' . $pageArray['id']
        ]);
    }

    public function list()
    {
        $pages = CreatedPageLog::all();

        return view('page.list', [
            'pages' => $pages
        ]);
    }

    public function edit(string $pageId = '')
    {
        $castPageId = (int)$pageId;

        $PageLogs = CreatedPageLog::where('page_id', $castPageId);
        if ($PageLogs->doesntExist()) die('ページIDが不正です');

        $Option = new Option();
        $accessToken = $Option->getAccessToken();

        if (!$accessToken) {
            die('アクセストークンがありません');
        }
        $Shopify = new ShopifySDK([
            'ShopUrl' => config('app.shopUrl'),
            'AccessToken' => $accessToken
        ]);

        $pageLog = $PageLogs->get()->take(1)[0]->toArray();

        /**
         * @returns array
        */
        $pageArray = $Shopify->Page($castPageId)->get();
        $fetchPageArray = array_merge($pageLog, $pageArray);

        $editLogs = [];
        $EditedPageLogs = EditedPageLog::where('page_id', $castPageId);
        if ($EditedPageLogs->exists()) {
            $editLogs = $EditedPageLogs->orderBy('updated_at', 'desc')->get();
        }

        return view('page.edit', [
            'page' => $fetchPageArray,
            'pageId' => $pageId,
            'editLogs' => $editLogs
        ]);
    }

    public function update(Request $request, string $pageId)
    {
        $this->validator($request->all())->validate();

        $castPageId = (int)$pageId;

        $PageLogs = CreatedPageLog::where('page_id', $castPageId);
        if ($PageLogs->doesntExist()) die('ページIDが不正です');

        $Option = new Option();
        $accessToken = $Option->getAccessToken();

        if (!$accessToken) {
            die('アクセストークンがありません');
        }
        $Shopify = new ShopifySDK([
            'ShopUrl' => config('app.shopUrl'),
            'AccessToken' => $accessToken
        ]);


        $Shopify->Page($castPageId)->put([
            'title' => $request->get('title'),
            'body_html' => $request->get('content')
        ]);

        /**
         * @returns array
         */
        $fetchPageArray = $Shopify->Page($castPageId)->get();
        $fetchPageArray['page_id'] = $fetchPageArray['id'];
        unset($fetchPageArray['created_at'], $fetchPageArray['updated_at'], $fetchPageArray['id']);

        EditedPageLog::create($fetchPageArray);

        return redirect(route('page.edit', ['pageId' => $pageId]))->with('successUpdate', 'アップデートに成功しました！');
    }

    protected function validator(array $data): \Illuminate\Validation\Validator
    {
        $validations = [
            'content' => ['required'],
            'title' => ['required']
        ];

        $messages = [
            'content.required' => '未入力です',
            'title.required' => '未入力です'
        ];

        return Validator::make($data, $validations, $messages);
    }
}
