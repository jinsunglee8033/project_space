@extends('layouts.dashboard')

@section('content')

    <section class="section">
        <div class="section-header">
            <h1>HOME</h1>
        </div>

        <div class="row">

            <?php if(auth()->user()->team == 'Admin' || auth()->user()->team == 'Executive') { ?>

            <?php } ?>

        </div>

    </section>




@endsection
