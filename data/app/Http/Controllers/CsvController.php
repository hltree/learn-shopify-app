<?php

namespace App\Http\Controllers;

use App\Models\Option;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use PHPShopify\ShopifySDK;

class CsvController extends Controller
{
    private const EXPORT_TYPE = [
        'YamatoTransport' => 'ヤマト運輸（B2クラウド）'
    ];
    private const TARGET_LABEL = [
        'unfulfilled' => '未発送',
//        'partial' => '一部発送済',
        'fulfilled' => '発送済'
    ];
    private const INCLUDE_ARCHIVE_ORDER = [
        'false' => '含めない',
        'true' => '含める'
    ];

    private $ShopifySDK = null;

    public function index()
    {
        return view('csv.index', [
            'EXPORT_TYPE' => self::EXPORT_TYPE,
            'TARGET_LABEL' => self::TARGET_LABEL,
            'INCLUDE_ARCHIVE_ORDER' => self::INCLUDE_ARCHIVE_ORDER
        ]);
    }

    public function export(Request $request)
    {
        $Validator = $this->validator($request->all());
        if ($Validator->fails()) {
            return new JsonResponse([
                'errors' => $Validator->messages()->toArray()
                ], 422);
        }

        $methodName = 'ExportCsvConvert_' . $request->get('export_type');
        if (!method_exists($this, $methodName)) die($methodName . 'というメソッドはないです');

        $orders = $this->getOrders($request->all());

        if (!is_array($orders) || 0 === count($orders)) throw ValidationException::withMessages(['order' => '出力できる注文がありません']);
        $this->$methodName($orders);
    }

    protected function validator(array $data): \Illuminate\Validation\Validator
    {
        $validations = [
            'export_type' => ['required', 'equal:' . implode('|', array_keys(self::EXPORT_TYPE))],
            'target_label' => ['required', 'equal:' . implode('|', array_keys(self::TARGET_LABEL))],
            'include_archive_order' => ['required', 'equal:' . implode('|', array_keys(self::INCLUDE_ARCHIVE_ORDER))]
        ];

        $messages = [
            'export_type.required' => '選択してください',
            'export_type.equal' => '不正な値が入ってしまったようです',
            'target_label.required' => '選択してください',
            'target_label.equal' => '不正な値が入ってしまったようです',
            'include_archive_order.required' => '選択してください',
            'include_archive_order.equal' => '不正な値が入ってしまったようです'
        ];

        return Validator::make($data, $validations, $messages);
    }

    /**
     * 60日以上経過したオーダーはShopify管理者（＝Shopify開発チーム？）に許可申請を提出する必要あり
     * @return array
     */
    protected function getOrders(array $requestAll): array
    {
        $Option = new Option();
        $accessToken = $Option->getAccessToken();

        if (!$accessToken) {
            die('アクセストークンがありません');
        }
        $this->ShopifySDK = new ShopifySDK([
            'ShopUrl' => config('app.shopUrl'),
            'AccessToken' => $accessToken
        ]);

        // 全件取得してしまう
        $fullData = $this->ShopifySDK->Order->get([
            'status' => 'any'
        ]);

        foreach ($fullData as $keyIndex => $order) {
            if ('false' === $requestAll['include_archive_order']) {
                if (!is_null($order['closed_at'])) unset($fullData[$keyIndex]);
            }

            // unfulfilled は null になった？のでnullに変換する
            $status = $requestAll['target_label'];
            if ('unfulfilled' === $status) $status = null;

            if ($status !== $order['fulfillment_status']) unset($fullData[$keyIndex]);
        }

        return $fullData;
    }

    /**
     * @param array $orders
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     *
     * 形式はB2クラウドのcsvを参考にする
     * https://www.kuronekoyamato.co.jp/newb2/help/manual/manual_sosa/16_exchange/exchange_01.html
     */
    protected function ExportCsvConvert_YamatoTransport(array $orders): void
    {
        $shop = $this->ShopifySDK->Shop->get();

        $callback = function () use ($orders, $shop) {
            $stream = fopen('php://output', 'w');
            // csv header
            fputcsv($stream, [
                "お客様管理番号\n半角英数字50文字",
                "送り状種類
\n半角数字1文字
\n 0 : 発払い
\n 2 : コレクト
\n 3 : ＤＭ便
\n 4 : タイム
\n 5 : 着払い
\n 7 : ネコポス
\n 8 : 宅急便コンパクト
\n 9 : 宅急便コンパクトコレクト
\n
\n(※宅急便_必須項目)
\n(※ＤＭ便_必須項目)
\n(※ネコポス_必須項目)","クール区分
\n半角数字1文字
\n0または空白 : 通常
\n 1 : クール冷凍
\n 2 : クール冷蔵","伝票番号
\n半角数字12文字
\n
\n※B2クラウドにて付与", "出荷予定日
\n半角10文字
\n｢YYYY/MM/DD｣で入力してください。
\n
\n(※宅急便_必須項目)
\n(※ＤＭ便_必須項目)
\n(※ネコポス_必須項目)", "お届け予定日
\n半角10文字
\n｢YYYY/MM/DD｣で入力してください。
\n
\n※入力なしの場合、印字されません。
\n※「最短日」と入力可", "配達時間帯
\n半角4文字
\nタイム、ＤＭ便以外
\n 空白 : 指定なし
\n 0812 : 午前中
\n 1416 : 14～16時
\n 1618 : 16～18時
\n 1820 : 18～20時
\n 1921 : 19～21時
\n
\nタイム
\n 0010 : 午前10時まで
\n 0017 : 午後5時まで", "お届け先コード
\n半角英数字20文字", "お届け先電話番号
\n半角数字15文字ハイフン含む
\n
\n(※宅急便_必須項目)
\n(※ＤＭ便_必須項目)
\n(※ネコポス_必須項目)", "お届け先電話番号枝番
\n半角数字2文字", "お届け先郵便番号
\n半角数字8文字
\nハイフンなし7文字も可
\n
\n(※宅急便_必須項目)
\n(※ＤＭ便_必須項目)
\n(※ネコポス_必須項目)", "お届け先住所
\n全角/半角
\n都道府県（４文字）
\n市区郡町村（１２文字）
\n町・番地（１６文字）
\n
\n(※宅急便_必須項目)
\n(※ＤＭ便_必須項目)
\n(※ネコポス_必須項目)", "お届け先アパートマンション名
\n全角/半角
\n16文字/32文字 ", "お届け先会社・部門１
\n全角/半角
\n25文字/50文字 ","お届け先会社・部門２
\n全角/半角
\n25文字/50文字 ","お届け先名
\n全角/半角
\n16文字/32文字
\n
\n(※宅急便_必須項目)
\n(※ＤＭ便_必須項目)
\n(※ネコポス_必須項目)","お届け先名(ｶﾅ)
\n半角カタカナ 50文字 ","敬称
\n全角/半角 2文字/4文字
\nＤＭ便の場合に指定可能
\n【入力例】
\n様・御中・殿・行・係・宛・先生・なし","ご依頼主コード
\n半角英数字 20文字 ","ご依頼主電話番号
\n半角数字15文字ハイフン含む
\n
\n(※宅急便_必須項目)
\n(※ネコポス_必須項目)","ご依頼主電話番号枝番
\n半角数字 2文字 ","ご依頼主郵便番号
\n半角数字8文字
\nハイフンなし半角7文字も可
\n
\n(※宅急便_必須項目)
\n(※ネコポス_必須項目)","ご依頼主住所
\n全角/半角32文字/64文字
\n都道府県（４文字）
\n市区郡町村（１２文字）
\n町・番地（１６文字）
\n
\n(※宅急便_必須項目)
\n(※ネコポス_必須項目)","ご依頼主アパートマンション
\n全角/半角 16文字/32文字 ","ご依頼主名
\n全角/半角 16文字/32文字
\n
\n(※宅急便_必須項目)
\n(※ネコポス_必須項目)","ご依頼主名(ｶﾅ)
\n半角カタカナ 50文字","品名コード１
\n半角英数字 30文字 ","品名１
\n全角/半角 25文字/50文字
\n
\n(※宅急便_必須項目)
\n(※ネコポス_必須項目)","品名コード２
\n半角英数字 30文字","品名２
\n全角/半角 25文字/50文字 ","荷扱い１
\n全角/半角 10文字/20文字 ","荷扱い２
\n全角/半角 10文字/20文字 ","記事
\n全角/半角 22文字/44文字 ","ｺﾚｸﾄ代金引換額（税込)
\n半角数字 7文字
\n
\n※コレクトの場合は必須
\n300,000円以下　1円以上
\n※但し、宅急便コンパクトコレクトの場合は
\n30,000円以下　　1円以上","内消費税額等
\n半角数字 7文字
\n
\n※コレクトの場合は必須
\n※コレクト代金引換額（税込)以下","止置き
\n半角数字 1文字
\n0 : 利用しない
\n1 : 利用する ","営業所コード
\n半角数字 6文字
\n
\n※止置きを利用する場合は必須 ","発行枚数
\n半角数字 2文字
\n
\n※発払いのみ指定可能","個数口表示フラグ
\n半角数字 1文字
\n1 : 印字する
\n2 : 印字しない
\n3 : 枠と口数を印字する
\n
\n※宅急便コンパクト、宅急便コンパクトコレクトは対象外","請求先顧客コード
\n半角数字12文字
\n
\n(※宅急便_必須項目)
\n(※ネコポス_必須項目)","請求先分類コード
\n空白または半角数字3文字
\n","運賃管理番号
\n半角数字2文字
\n
\n(※宅急便_必須項目)
\n(※ネコポス_必須項目)","クロネコwebコレクトデータ登録
\n半角数字 1文字
\n0 : 無し
\n1 : 有り ","クロネコwebコレクト加盟店番号
\n半角英数字 9文字
\n
\n※クロネコwebコレクトデータ有りの場合は必須 ","クロネコwebコレクト申込受付番号１
\n半角英数字 23文字
\n
\n※クロネコwebコレクトデータ有りの場合は必須 ","クロネコwebコレクト申込受付番号２
\n半角英数字 23文字","クロネコwebコレクト申込受付番号３
\n半角英数字 23文字","お届け予定ｅメール利用区分
\n半角数字 1文字
\n0 : 利用しない
\n1 : 利用する ","お届け予定ｅメールe-mailアドレス
\n半角英数字＆記号 60文字
\n
\n※お届け予定eメールを利用する場合は必須 ","入力機種
\n半角数字 1文字
\n1 : ＰＣ
\n2 : 携帯電話
\n
\n※お届け予定eメールを利用する場合は必須","お届け予定ｅメールメッセージ
\n全角 74文字
\n
\n
\n※お届け予定eメールを利用する場合は必須","お届け完了ｅメール利用区分
\n半角数字 1文字
\n0 : 利用しない
\n1 : 利用する ","お届け完了ｅメールe-mailアドレス
\n半角英数字 60文字
\n
\n※お届け完了eメールを利用する場合は必須 ","お届け完了ｅメールメッセージ
\n全角 159文字
\n
\n※お届け完了eメールを利用する場合は必須 ","クロネコ収納代行利用区分
\n半角数字１文字","予備
\n半角数字１文字","収納代行請求金額(税込)
\n半角数字７文字","収納代行内消費税額等
\n半角数字７文字","収納代行請求先郵便番号
\n半角数字＆ハイフン8文字","収納代行請求先住所
\n全角/半角　32文字/64文字
\n都道府県（４文字）
\n市区郡町村（１２文字）
\n町・番地（１６文字）","収納代行請求先住所（アパートマンション名）
\n全角/半角　16文字/32文字","収納代行請求先会社・部門名１
\n全角/半角　25文字/50文字","収納代行請求先会社・部門名２
\n全角/半角　25文字/50文字","収納代行請求先名(漢字)
\n全角/半角　16文字/32文字","収納代行請求先名(カナ)
\n半角カタカナ50文字","収納代行問合せ先名(漢字)
\n全角/半角　16文字/32文字","収納代行問合せ先郵便番号
\n半角数字＆ハイフン8文字","収納代行問合せ先住所
\n全角/半角　32文字/64文字
\n都道府県（４文字）
\n市区郡町村（１２文字）
\n町・番地（１６文字）","収納代行問合せ先住所（アパートマンション名）
\n全角/半角　16文字/32文字","収納代行問合せ先電話番号
\n半角数字＆ハイフン15文字","収納代行管理番号
\n半角英数字20文字","収納代行品名
\n全角/半角　25文字/50文字","収納代行備考
\n全角/半角　14文字/28文字","複数口くくりキー
\n半角英数字20文字
\n
\n※「出荷予定個数」が2以上で「個数口枠の印字」で 「3 : 枠と口数を印字する」を選択し、且つ「複数口くくりキー」が空白の場合は、送り状発行時に「B2」という文言を自動補完する。","検索キータイトル1
\n全角/半角
\n10文字/20文字 ","検索キー1
\n半角英数字
\n20文字","検索キータイトル2
\n全角/半角
\n10文字/20文字 ","検索キー2
\n半角英数字
\n20文字","検索キータイトル3
\n全角/半角
\n10文字/20文字 ","検索キー3
\n半角英数字
\n20文字","検索キータイトル4
\n全角/半角
\n10文字/20文字 ","検索キー4
\n半角英数字
\n20文字","検索キータイトル5
\n
\n※入力時は不要。出力時に自動反映。
\n※「ユーザーID」という文言を送り状発行時に固定で自動補完する。","検索キー5
\n
\n※入力時は不要。出力時に自動反映。
\n※送り状発行時のユーザーIDを固定で自動補完する。","予備","予備","投函予定メール利用区分
\n半角数字
\n1文字
\n0 : 利用しない
\n1 : 利用する PC宛て
\n2 : 利用する モバイル宛て","投函予定メールe-mailアドレス
\n半角英数字＆記号
\n60文字","投函予定メールメッセージ
\n全角/半角
\n74文字/148文字
\n
\n※半角カタカナ及び半角スペースは使えません。","投函完了メール（お届け先宛）利用区分
\n半角数字
\n1文字
\n0 : 利用しない
\n1 : 利用する PC宛て
\n2 : 利用する モバイル宛て","投函完了メール（お届け先宛）e-mailアドレス
\n半角英数字＆記号
\n60文字","投函完了メール（お届け先宛）メールメッセージ
\n全角/半角
\n159文字/318文字
\n
\n※半角カタカナ及び半角スペースは使えません。","投函完了メール（ご依頼主宛）利用区分
\n半角数字
\n1文字
\n0 : 利用しない
\n1 : 利用する PC宛て
\n2 : 利用する モバイル宛て","投函完了メール（ご依頼主宛）e-mailアドレス
\n半角英数字＆記号
\n60文字","投函完了メール（ご依頼主宛）メールメッセージ
\n全角/半角
\n159文字/318文字
\n
\n※半角カタカナ及び半角スペースは使えません。"
            ]);
            foreach ($orders as $order) {
                fputcsv($stream, [
                    $order['id'], // お客様管理番号（注文番号にする）
                    0, // 送り状の種類。今は判定する条件がないので、0の発払いを入れておきます
                    '', // クール区分
                    '', // 伝票番号。B2上で付与されます
                    '', // 出荷予定日 YYYY/MM/DD で入力する必要ありだが、デフォルトでは出荷予定日時を選択できないので、カスタムする必要ある
                    '', // お届け予定日 同上
                    '', // 配達時間帯 同上。フォーマットはヘッダーを参照して
                    '', // お届け先コード B2管理用の任意の値。なくてもいい
                    $order['phone'], // お届け先電話番号
                    '', // お届け先電話番号枝番 https://b-faq.kuronekoyamato.co.jp/app/answers/detail/a_id/916/~/b2%E3%82%AF%E3%83%A9%E3%82%A6%E3%83%89%E3%81%A7%E3%80%81%E9%80%81%E3%82%8A%E7%8A%B6%E3%81%AE%E5%85%A5%E5%8A%9B%E9%A0%85%E7%9B%AE%E3%81%AE-%E3%80%8C%E6%9E%9D%E7%95%AA%E3%80%8D-%E3%81%A8%E3%81%AF%E4%BD%95%E3%81%A7%E3%81%99%E3%81%8B%E3%80%82
                    isset($order['shipping_address']) ? $order['shipping_address']['zip'] : '', // お届け先郵便番号
                    isset($order['shipping_address']) ?
                        $order['shipping_address']['province'] . ' ' . $order['shipping_address']['city'] . ' ' . $order['shipping_address']['address1']
                        : '', // お届け先住所
                    isset($order['shipping_address']) ? $order['shipping_address']['address2'] : '', // お届け先アパートマンション名
                    isset($order['shipping_address']) ? $order['shipping_address']['company'] : '', // お届け先会社・部門１
                    isset($order['shipping_address']) ? $order['shipping_address']['zip'] : '', // お届け先会社・部門２
                    isset($order['shipping_address']) ? $order['shipping_address']['name'] : '', // お届け先名
                    '', // お届け先名(ｶﾅ)
                    '', // 敬称
                    '', // ご依頼主コード お届け先コードと同じ扱い
                    $shop['email'], // ご依頼主電話番号
                    '', // ご依頼主電話番号枝番
                    $shop['zip'], // ご依頼主郵便番号
                    $shop['province'] . ' ' . $shop['city'] . ' ' . $shop['address1'], // ご依頼主住所
                    $shop['address2'], // ご依頼主アパートマンション
                    $shop['name'], // ご依頼主名
                    '', // ご依頼主名(ｶﾅ)
                    '', // 品名コード１
                    '', // 品名１
                    '', // 品名コード２
                    '', // 品名２
                    '', // 荷扱い１
                    '', // 荷扱い２
                    '', // 記事
                    '', // ｺﾚｸﾄ代金引換額（税込) 300,000円以下　1円以上
                    '', // 内消費税額等
                    '', // 止置き
                    '', // 営業所コード
                    '', // 発行枚数
                    '', // 個数口表示フラグ
                    '', // 請求先顧客コード
                    '', // 請求先分類コード
                    '', // 運賃管理番号
                    '', // クロネコwebコレクトデータ登録
                    '', // クロネコwebコレクト加盟店番号
                    '', // クロネコwebコレクト申込受付番号１
                    '', // クロネコwebコレクト申込受付番号２
                    '', // クロネコwebコレクト申込受付番号３
                    '', // お届け予定ｅメール利用区分
                    '', // お届け予定ｅメールe-mailアドレス
                    '', // 入力機種
                    '', // お届け予定ｅメールメッセージ
                    '', // お届け完了ｅメール利用区分
                    '', // お届け完了ｅメールe-mailアドレス
                    '', // お届け完了ｅメールメッセージ
                    '', // クロネコ収納代行利用区分
                    '', // 予備
                    '', // 収納代行請求金額(税込)
                    '', // 収納代行内消費税額等
                    '', // 収納代行請求先郵便番号
                    '', // 収納代行請求先住所
                    '', // 収納代行請求先住所（アパートマンション名）
                    '', // 収納代行請求先会社・部門名１
                    '', // 収納代行請求先会社・部門名２
                    '', // 収納代行請求先名(漢字)
                    '', // 収納代行請求先名(カナ)
                    '', // 収納代行問合せ先名(漢字)
                    '', // 収納代行問合せ先郵便番号
                    '', // 収納代行問合せ先住所
                    '', // 収納代行問合せ先住所（アパートマンション名）
                    '', // 収納代行問合せ先電話番号
                    '', // 収納代行管理番号
                    '', // 収納代行品名
                    '', // 収納代行備考
                    '', // 複数口くくりキー
                    '', // 検索キータイトル1
                    '', // 検索キー1
                    '', // 検索キータイトル2
                    '', // 検索キー2
                    '', // 検索キータイトル3
                    '', // 検索キー3
                    '', // 検索キータイトル4
                    '', // 検索キー4
                    '', // 検索キータイトル5 (入力しないこと)
                    '', // 検索キー5 (入力しないこと)
                    '', // 予備
                    '', // 予備
                    '', // 投函予定メール利用区分
                    '', // 投函予定メールe-mailアドレス
                    '', // 投函予定メールメッセージ
                    '', // 投函完了メール（お届け先宛）利用区分
                    '', // 投函完了メール（お届け先宛）e-mailアドレス
                    '', // 投函完了メール（お届け先宛）メールメッセージ
                    '', // 投函完了メール（ご依頼主宛）利用区分
                    '', // 投函完了メール（ご依頼主宛）e-mailアドレス
                    '' // 投函完了メール（ご依頼主宛）メールメッセージ
                ]);
            }
            fclose($stream);
        };

        $filename = sprintf('order-%s.csv', date('Ymd'));
        $header = [
            'Content-Type' => 'application/octet-stream',
        ];

        response()->streamDownload($callback, $filename, $header)->send();
    }
}
