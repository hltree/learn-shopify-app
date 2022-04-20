@include('header')
<style>
    a {
        color: blue;
        text-decoration: underline;
    }
</style>
<p>ページが作成されました。<a href="{{ $pageUrl }}" target="_blank">こちら</a>から確認できます！</p>
<p><a href="{{ route('page.create') }}">戻る</a></p>
@include('footer')
