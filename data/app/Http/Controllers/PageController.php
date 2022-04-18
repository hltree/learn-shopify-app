<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PageController extends Controller
{
    public function create()
    {
        return view('page.create');
    }

    public function new(Request $request)
    {
        $this->validator($request->all())->validate();

        $pageInfo = array(
            'title' => $request->get('title'),
            'body_html' => $request->get('content'),
        );

        $pages = $this->ShopifySDK->Page->post($pageInfo);
        dd($pages);
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
