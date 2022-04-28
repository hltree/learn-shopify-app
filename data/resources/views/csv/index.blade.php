@include('header')
<style>
    button {
        padding: 1.3em 3em;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 2.5px;
        font-weight: 500;
        color: #000;
        background-color: #fff;
        border: none;
        border-radius: 45px;
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease 0s;
        cursor: pointer;
        outline: none;
    }

    button:hover {
        background-color: #2EE59D;
        box-shadow: 0 15px 20px rgba(46, 229, 157, 0.4);
        color: #fff;
        transform: translateY(-7px);
    }

    button:active {
        transform: translateY(-1px);
    }

    form {
        margin-top: 40px;
    }

    legend {
        font-weight: 700;
    }

    .alert.alert-danger {
        color: #721c24;
        background-color: #f8d7da;
        position: relative;
        padding: 0.75rem 1.25rem;
        margin-bottom: 1rem;
        border: 1px solid #f5c6cb;
        border-radius: 0.25rem
    }

    a {
        color: blue;
        text-decoration: underline;
    }

    a:hover {
        text-decoration: none;
    }

    span[data-error-export] {
        visibility: hidden;
    }
</style>
<p>配送業者のシステムに取込み可能な形式でCSV出力します！ヤマト運輸（B2クラウド）は<a
        href="https://www.kuronekoyamato.co.jp/newb2/help/manual/manual_sosa/16_exchange/exchange_01.html">こちら</a>を参考にしています！
</p>
<form action="{{ route('csv.export') }}" method="post">
    @csrf
    @error('order')
    <div class="error alert alert-danger">{{ $message }}</div>
    @enderror
    <span data-error-export="order"></span>
    <fieldset data->
        <legend>出力形式</legend>
        @foreach ($EXPORT_TYPE as $key => $value)
            <input type="radio" id="export_type_{{ $key }}" name="export_type" value="{{ $key }}">
            <label for="export_type_{{ $key }}">{{ $value }}</label>
        @endforeach
        @error('export_type')
        <div class="error alert alert-danger">{{ $message }}</div>
        @enderror
        <span data-error-export="export_type"></span>
    </fieldset>
    <fieldset>
        <legend>対象ラベル</legend>
        @foreach ($TARGET_LABEL as $key => $value)
            <input type="radio" id="target_label_{{ $key }}" name="target_label" value="{{ $key }}">
            <label for="target_label_{{ $key }}">{{ $value }}</label>
        @endforeach
        @error('target_label')
        <div class="error alert alert-danger">{{ $message }}</div>
        @enderror
        <span data-error-export="target_label"></span>
    </fieldset>
    <fieldset>
        <legend>アーカイブされた注文を出力対象に含めますか？</legend>
        @foreach ($INCLUDE_ARCHIVE_ORDER as $key => $value)
            <input type="radio" id="include_archive_order_{{ $key }}" name="include_archive_order" value="{{ $key }}">
            <label for="include_archive_order_{{ $key }}">{{ $value }}</label>
        @endforeach
        @error('include_archive_order')
        <div class="error alert alert-danger">{{ $message }}</div>
        @enderror
        <span data-error-export="include_archive_order"></span>
    </fieldset>
    <fieldset>
        <button type="submit" name="submitter">ダウンロードする</button>
    </fieldset>
</form>
<script>
    var form = document.querySelector('form')
    var bt = document.querySelector('button[name=submitter]')
    if (form && bt) {
        bt.addEventListener('click', function (e) {
            e.preventDefault()
            Swal.fire({
                title: '{{ __('Please wait.') }}',
                html: '',
                timerProgressBar: false,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading()
                }
            })
            var Request = new XMLHttpRequest()
            Request.onreadystatechange = function () {
                if (Request.readyState === XMLHttpRequest.DONE) {
                    var jsonResponse = false
                    try {
                        var response = JSON.parse(Request.responseText)
                        jsonResponse = true
                    } catch (exception) {
                        console.log('not json')
                    }

                    document.querySelectorAll('.error').forEach(function (erEl) {
                        erEl.remove()
                    })

                    if (jsonResponse && response.errors) {
                        for (var [key, error] of Object.entries(response.errors)) {
                            if (0 < document.querySelectorAll('*[data-error-export="' + key + '"]').length) {
                                var el = document.createElement('div')
                                el.classList.add('error', 'alert', 'alert-danger')
                                el.textContent = error
                                document.querySelectorAll('*[data-error-export="' + key + '"]').forEach(function (oldEl) {
                                    oldEl.after(el)
                                })
                            }
                        }
                    } else {
                        form.submit()
                    }

                    Swal.close()
                }
            }
            Request.open('POST', form.action)
            Request.send(new FormData(form))
        })
    }
</script>
@include('footer')
