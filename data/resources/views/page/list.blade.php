@include('header')
<style>
    .max-w-6xl {
        width: 100%;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        border-spacing: 0;
    }

    table th, table td {
        padding: 10px 0;
        text-align: center;
    }

    table tr:nth-child(odd) {
        background-color: #eee
    }
</style>
<table>
    <tr>
        <th>Page Title</th>
        <th>Link</th>
        <th>Edit</th>
    </tr>
    @if ($pages)
        @foreach($pages as $page)
            <tr>
                <td>{{ $page->getAttribute('title') }}</td>
                <td>
                    <a href="{{ 'https://' . config('app.shopUrl') . '/admin/pages/' . $page->getAttribute('page_id') }}">{{ 'https://' . config('app.shopUrl') . '/admin/pages/' . $page->getAttribute('page_id') }}</a>
                </td>
                <td><a href="{{ route('page.edit', ['pageId' => $page->getAttribute('page_id')]) }}">edit</a></td>
            </tr>
        @endforeach
    @endif
</table>
@include('footer')
