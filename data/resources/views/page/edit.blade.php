@include('header')
<h2>ページ編集</h2>
@if (session('successUpdate'))
    <div class="success">
        {{ session('successUpdate') }}
    </div>
@endif
<form method="POST" action="{{ route('page.update', ['pageId' => $pageId]) }}">
    @csrf
    @method('PUT')

    <label for="title">Page Title</label>
    <input id="title" class="@error('title') is-invalid @enderror" name="title" value="{{ $page['title'] }}"/>
    @error('title')
    <div class="alert alert-danger">{{ $message }}</div>
    @enderror

    <label for="textarea">Page Content</label>
    <textarea id="textarea" class="@error('content') is-invalid @enderror"
              name="content">{{ $page['body_html'] }}</textarea>
    @error('content')
    <div class="alert alert-danger">{{ $message }}</div>
    @enderror

    <button type="submit">送信</button>
</form>
@if ($editLogs)
    <div class="table">
        <table>
            <tr>
                <th>Page Title</th>
                <th>Update Time</th>
            </tr>
            @foreach($editLogs as $editLog)
                <tr>
                    <td>{{ $editLog['title'] }}</td>
                    <td>{{ \Carbon\Carbon::create($editLog['updated_at'])->toDateTimeString() }}</td>
                </tr>
            @endforeach
        </table>
    </div>
@endif
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

    table {
        width: 100%;
        border-collapse: collapse;
        border-spacing: 0;
    }

    .table {
        margin-top: 40px;
        height: 150px;
        overflow: scroll;
    }

    table th, table td {
        padding: 10px 0;
        text-align: center;
    }

    table tr:nth-child(odd) {
        background-color: #eee
    }

    .success {
        color: #155724;
        background-color: #d4edda;
        position: relative;
        padding: 0.75rem 1.25rem;
        margin-bottom: 1rem;
        border: 1px solid #c3e6cb;
        border-radius: 0.25rem;
    }
</style>
@include('footer')
