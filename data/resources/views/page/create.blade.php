@include('header')
<h2>新規ページ</h2>
<form method="POST" action="{{ route('page.new') }}">
    @csrf

    <label for="title">Page Title</label>
    <input id="title" class="@error('title') is-invalid @enderror" name="title" />
    @error('title')
    <div class="alert alert-danger">{{ $message }}</div>
    @enderror

    <label for="textarea">Page Content</label>
    <textarea id="textarea" class="@error('content') is-invalid @enderror" name="content"></textarea>
    @error('content')
    <div class="alert alert-danger">{{ $message }}</div>
    @enderror

    <button type="submit">送信</button>
</form>
<style>
    form {
        background: beige;
        padding: 20px 30px;
    }

    label {
        display: block;
        margin-bottom: 10px;
    }

    textarea {
        height: 400px;
        width: 500px;
    }

    button[type="submit"] {
        background: #fff;
        border: 1px solid currentColor;
        cursor: pointer;
        display: block;
        margin-left: auto;
        margin-right: auto;
        margin-top: 5px;
        width: 200px;
    }
</style>
@include('footer')
