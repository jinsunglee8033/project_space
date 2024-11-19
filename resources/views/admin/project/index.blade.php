@extends('layouts.dashboard')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Project NPD</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item active">Project Manager</div>
        </div>
    </div>
    <div class="section-body">

        @include('admin.project.flash')
        @include('admin.project._filter')

        <div class="row" style="margin-top: 15px;">

            @foreach ($projects as $project)

                <?php $card_bg = '';
                        if($project->status == 'pending' || $project->status == 'review'){
                            $card_bg = "background-color: #f9e5e5";
                        }
                ?>
                <div class="col-md-6">
                    <div class="card" style="{{$card_bg}}">
                        <div class="card-header">
                            <h4>{{ $project->name }}
                                <span class="float-right">
                                <a  href="javascript:void(0);"
                                    class="close"
                                    data-id=""
                                    data-project-id="{{ $project->id }}"
                                    onclick="delete_project($(this));">
                                <i class="fa fa-times"></i>
                                </a>
                                </span>
                            </h4>
                            <br>

                            <div class="col-sm-12" style="padding: 0px 0px 0px 2px; font-size: 1.2vmin; font-weight: bold; color: #5796CBFF;">

                                <?php
                                $start_date = $project->created_at;
                                $end_date = $project->launch_date;

                                $start = new DateTime($start_date);
                                $end = new DateTime($end_date);
                                $today = new DateTime();

                                $end->modify('+1 day'); // 종료일을 포함하도록 조정
                                $today->modify('+1 day');

                                $interval = new DateInterval('P1D');
                                $date_period = new DatePeriod($start, $interval, $end);
                                $date_period_1 = new DatePeriod($start, $interval, $today);
                                $date_period_2 = new DatePeriod($today, $interval, $end);

                                $weekday_count = 0;
                                foreach ($date_period as $date) {
                                    if ($date->format('N') < 6) { // 월요일(1)에서 금요일(5)까지 체크
                                        $weekday_count++;
                                    }
                                }

                                $weekday_count_1 = 0;
                                foreach ($date_period_1 as $date) {
                                    if ($date->format('N') < 6) { // 월요일(1)에서 금요일(5)까지 체크
                                        $weekday_count_1++;
                                    }
                                }

                                $weekday_count_2 = 0;
                                foreach ($date_period_2 as $date) {
                                    if ($date->format('N') < 6) { // 월요일(1)에서 금요일(5)까지 체크
                                        $weekday_count_2++;
                                    }
                                }

                                if($weekday_count == 0){
                                    $total_progress = 100;
                                }else{
                                    $total_progress = round($weekday_count_1 / $weekday_count * 100);
                                    if($total_progress >= 100){
                                        $total_progress = 100;
                                    }
                                }
                                ?>

{{--                                <div class="progress" style="margin: -15px 0 -10px 0; height: 1.3rem;">--}}
{{--                                    <div class="progress-bar" role="progressbar"--}}
{{--                                         data-width="{{$total_progress}}%"--}}
{{--                                         aria-valuenow="50"--}}
{{--                                         aria-valuemin="0"--}}
{{--                                         style="width: 5%; font-size: 0.9rem; color: #000000; background-color: #5796cb;">--}}
{{--                                        <b>Duration : {{$weekday_count}} days | Remaining : {{$weekday_count_2}} days ( {{ $total_progress }}% )</b>--}}
{{--                                    </div>--}}
{{--                                </div>--}}

                                <div class="progress" style="margin: -15px 0 -10px 0; height: 1.3rem; position: relative; background-color: #616161;">
                                    <div class="progress-bar-striped progress-bar-animated"
                                         role="progressbar"
                                         data-width="{{$total_progress}}%"
                                         style="width: 5%; background-color: #0BC27F;">
                                    </div>
                                    <span class="progress-text"
                                          style="font-size:1.2em;
                                          position: absolute; width: 100%;
                                          text-align: center; left: 0;
                                          top: 50%; transform: translateY(-50%);">
                                        <span style="color:#ffffff;border-radius:4px;">
                                            Duration : {{$weekday_count}} days | Remaining : {{$weekday_count_2}} days
                                        </span>
                                    </span>
                                </div>

                            </div>


                        </div>
                        <div class="card-body" style="display: flex; padding-top:0px;">

                            <div class="col-sm-4" style="padding: 20px 0px 0px 0px; margin: 5px 0 0 0;">

                                    <div>
                                        <h6>
                                            <span class="badge badge-dark" style="padding: 5px 12px; margin: 0 0 -3px -1px;">{{ $project->project_type }}</span>
                                        </h6>
                                    </div>
                                    <div>
                                        <b data-toggle="tooltip" data-placement="left" title="" data-original-title="Person In Charge">PIC:</b>
                                        {{ $project->author->first_name }} {{ $project->author->last_name }}
                                    </div>

                                    <div>
                                        <b data-toggle="tooltip" data-placement="left" title="" data-original-title="Created Date">CD:</b>
                                        {{ date('m/d/y', strtotime($project->created_at)) }}
                                    </div>
{{--                                    <div>--}}
{{--                                        <b data-toggle="tooltip" data-placement="left" title="" data-original-title="Target Receiving Date">TD:</b>--}}
{{--                                        {{ date('m/d/y', strtotime($project->target_date)) }}--}}
{{--                                    </div>--}}
                                    <div>
                                        <b data-toggle="tooltip" data-placement="left" title="" data-original-title="Launch Date">LD:</b>
                                        {{ date('m/d/y', strtotime($project->launch_date)) }}
                                    </div>
                                    <div>
                                        <b>Project:</b>
                                        # {{ $project->id }}
                                    </div>
                                    <div>
                                        <b>Team:</b>
                                        {{ $project->team}}
                                    </div>
                                    <div>
                                        <b>Brand:</b>
                                        {{ $project->brand}}
                                    </div>

                                <?php if($project->status == 'pending'){ ?>
                                <div style="padding-top: 15px; ">
                                    <i class="fa fa-spinner fa-pulse fa-2x fa-fw" style="margin: 0 0 0 0;"></i><label style="font-size: large; color: #b91d19; margin: 0 0 0 10px;">Pending</label>
                                </div>
                                <?php }else if($project->status == 'review'){ ?>
                                <div style="padding-top: 15px; ">
                                    <a href="{{ url('admin/project/'. $project->id .'/edit') }}">
                                        <button type="button" class="btn btn-open">Review</button>
                                    </a>
                                </div>
                                <?php }else{ ?>
                                <div style="padding-top: 15px; ">
                                    <a href="{{ url('admin/project/'. $project->id .'/edit') }}">
                                        <button type="button" class="btn btn-open">Open</button>
                                    </a>
                                </div>
                                <?php } ?>
                            </div>
                            <div class="col-sm-8">

                                <div class="row" style="font-size: 12px;">

                                    <div class="col-sm-4" style="text-align: center;font-size: 0.9rem;font-weight: bold; background-color: #eeeeee; color: black;">
                                        REQUEST
                                    </div>
                                    <div class="col-sm-8" style="text-align: center;font-size: 0.9rem;font-weight: bold; background-color: white; color: black;">
                                        PROGRESS
                                    </div>

                                    <?php $assets = \App\Repositories\Admin\ProjectTaskIndexRepository::get_tasks_npd($project->id); ?>

                                    <?php if(!empty($assets)){
                                        foreach ($assets as $key =>$val){
                                            $skip_bar = '';
                                            if($val == 'action_requested'){
                                                $bg_css = 'bg-primary';
                                                $progress = '50%';
                                                $text = 'In Progress';
                                                $tast_bar_color = '#fbd102';
                                                $bar_text_color = '#fff';
                                                $active_task_color = 'black';
                                            }else if($val == 'in_progress'){
                                                $bg_css = 'bg-primary';
                                                $progress = '50%';
                                                $text = 'In Progress';
                                                $tast_bar_color = '#fbd102';
                                                $bar_text_color = '#fff';
                                                $active_task_color = 'black';
                                            }else if($val == 'action_review'){
                                                $bg_css = 'bg-primary';
                                                $progress = '50%';
                                                $text = 'In Progress';
                                                $tast_bar_color = '#fbd102';
                                                $bar_text_color = '#fff';
                                                $active_task_color = 'black';
                                            }else if($val == 'action_completed'){
                                                $bg_css = 'bg-secondary';
                                                $progress = '100%';
                                                $text = 'Completed';
                                                $tast_bar_color = '#7e7e7e';
                                                $bar_text_color = '#fff';
                                                $active_task_color = 'black';
                                            }else if($val == 'TBD'){
                                                $bg_css = 'bg-third';
                                                $progress = '0%';
                                                $text = 'TBD';
                                                $tast_bar_color = '#c6c6c6';
                                                $bar_text_color = '#b4b4b4';
                                                $active_task_color = 'gray';
                                            }else if($val == 'action_skip'){
                                                $bg_css = 'bg-secondary';
                                                $progress = '100%';
                                                $text = 'Skipped';
                                                $tast_bar_color = '#a3a3a3';
                                                $bar_text_color = '#fff';
                                                $active_task_color = 'black';
                                                $skip_bar = 'progress-bar-striped';
                                            }else{
                                                $bg_css = 'bg-primary';
                                                $progress = '0%';
                                                $tast_bar_color = '#404040FF';
                                                $bar_text_color = '#fff';
                                                $active_task_color = 'gray';
                                            }
                                    ?>

                                    <div class="col-sm-4" style="padding: 2px 0px 0px 2px;font-size: 0.8rem;font-weight: bold; color:{{ $active_task_color }}; text-align: left;">
                                        <?php
                                        $asset_type = $key;
                                        if($asset_type == 'product_information'){
                                            $asset_type = 'Product Info';
                                        }elseif ($asset_type == 'mm_request'){
                                            $asset_type = 'NPD MM';
                                        }elseif ($asset_type == 'ra_request'){
                                            $asset_type = 'NPD RA';
                                        }elseif ($asset_type == 'legal_request'){
                                            $asset_type = 'NPD Legal';
                                        }elseif ($asset_type == 'display_request'){
                                            $asset_type = 'NPD Display';
                                        }elseif ($asset_type == 'npd_planner_request'){
                                            $asset_type = 'NPD Planner';
                                        }elseif ($asset_type == 'npd_po_request'){
                                            $asset_type = 'NPD PO';
                                        }elseif ($asset_type == 'pe_request'){
                                            $asset_type = 'DISPLAY & PE';
                                        }elseif ($asset_type == 'npd_design_request'){
                                            $asset_type = 'NPD Design';
                                        }elseif ($asset_type == 'qc_request'){
                                            $asset_type = 'NPD QA';
                                        }
                                        ?>
                                        {{  mb_strimwidth(ucwords(str_replace('_', ' ', $asset_type)), 0,22, '...') }}
                                    </div>



                                    <div class="col-sm-8" style="padding: 2px 0px 0px 2px;">
                                        <div class="progress" style="margin: 0 0 5px 0;">
                                            <div class="progress-bar {{$skip_bar}}" role="progressbar"
                                                 data-width="{{$progress}}"
                                                 style="width: 10%; font-size: 0.8rem;
                                                         background-color: {{ $tast_bar_color }};
                                                         color: {{$bar_text_color}};">
                                                {{ $text }}
                                            </div>
                                        </div>
                                    </div>

                                    <?php
                                            }
                                        }
                                    ?>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        {{ $projects->appends(['q' => !empty($filter['q']) ? $filter['q'] : ''])->links() }}
    </div>
</section>

    <script>
        function delete_project(el) {
            if (confirm("Are you sure to DELETE this project?") == true) {
                let p_id = $(el).attr('data-project-id');
                $.ajax({
                    url: "<?php echo url('/admin/project/projectRemove'); ?>"+"/"+p_id,
                    type: "GET",
                    datatype: "json",
                    success: function(response) {
                        if(response == 'success'){
                            $(el).parent().parent().parent().parent().parent().fadeOut( "slow", function() {
                                $(el).parent().parent().parent().parent().parent().remove();
                            });
                        }else{
                            alert(response);
                        }
                    },
                })
            }
        }
    </script>

@endsection
