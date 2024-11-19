@if ($errors->any())
    <div class="alert alert-warning">
        <div class="alert-title">Whoops!</div>
        @lang('general.validation_error_message')
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success" style="position: fixed; top:20px; z-index: 10; right: 20px; width: 42%;">{{ session('success') }}</div>

    <script>
    $("document").ready(function(){
        setTimeout(function(){
            $("div.alert").slideUp();
            }, 5000 ); // 5 secs
    });
    </script>
@endif

@if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif
