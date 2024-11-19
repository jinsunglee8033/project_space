@extends('layouts.dashboard')

@section('content')
<section class="section">

  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-header">
          <h4>Result</h4>
        </div>
        <div class="card-body">
{{--          <canvas id="myChart" height="158"></canvas>--}}
        <?php
            foreach ($info as $item) {


        ?>
            <div>
               {{ var_dump($item) }}
            </div>
        <?php
            }
        ?>

        </div>
      </div>
    </div>
  </div>
</section>
@endsection
