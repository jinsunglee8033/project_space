@extends('layouts.dashboard')

@section('content')

    <section class="section">
        <div class="section-header">
            <h1>QA Request Status Board</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></div>
                <div class="breadcrumb-item">QA Request Status Board</div>
            </div>
        </div>

        <div class="section-body">

            @include('admin.qc_request._filter')
{{--            @include('admin.asset.flash')--}}

            <div class="row flex-nowrap" style="overflow-x: scroll; padding-top: 17px;">

                <div class="col-md-3">
                    <div class="card status_title">
                        <h5 class="status_name">ACTION REQUESTED</h5>
                    </div>

                    @foreach ($task_list_action_requested as $task)

                        <?php
                        $date_standard = $task->qc_date;
                        if(\Illuminate\Support\Carbon::parse($date_standard)->isToday()){
                            $late_css = 'border-left: 5px solid #fbd102; border-radius: 20px;';
                        }else if(\Illuminate\Support\Carbon::parse($date_standard)->isPast()){
                            $late_css = 'border-left: 5px solid #b91d19; border-radius: 20px;';
                        }else{
                            $late_css = ' ';
                        }
                        ?>

                        <div class="card" style="{{$late_css}}">
                            <a href="{{ url('admin/qc_request/'. $task->project_id .'/edit')}}" style="text-decoration: none;">
                                <div class="card-body" style="margin: 0px -5px -20px -5px;">
                                    <div class="media" style="padding-bottom: 0px;">
                                        <div class="form-group" style="width: 100%;">

                                            <div style="color: #8b8a8a; font-weight: 600; float:left; font-size: 13px;">
                                                <b>{{$task->team}}</b> | {{mb_strimwidth($task->brand, 0,15, '..')}}
                                            </div>
                                            <div style="float: right;">
                                                <figure class="avatar mr-2 avatar-sm text-white" style="background-color: #b6b6b6; font-size: 15px;"
                                                        data-toggle="tooltip" data-placement="top"
                                                        data-original-title="{{ $task->author_name }}"
                                                        data-initial="{{ substr($task->author_name, 0, 1) }}">
                                                </figure>
                                            </div>
                                            <div class="media-title" style="clear:both; font-size: large;">
                                                {{$task->name}}
                                            </div>
                                            <div class="text-md-left text-muted" style="margin-top: -8px;">
                                                {{$task->vendor_code}}
                                            </div>
                                            <div class="text-md-left" style="color: #000000;">
                                                {{ mb_strimwidth($task->materials, 0,50, '...') }}
                                            </div>
                                            <?php if(isset($task->result)) { ?>
                                            <?php if($task->result == 'Pass') {
                                                $bg_result = '#3acd2d';
                                            }else if($task->result == 'Reject'){
                                                $bg_result = '#f63d4d';
                                            }else{
                                                $bg_result = '#cecece';
                                            }
                                            ?>
                                            <div style="float: right; color: #333333;" >
                                                <button type="button" class="btn btn-icon" style="font-family: revert;
                                                            font-size: 13px; background-color: {{$bg_result}};
                                                            height: 26px; padding: 0px 7px 2px 10px; margin: -28px 0 0 0;
                                                            border-radius:1.25rem;">
                                                    {{ $task->result }}
                                                </button>
                                            </div>
                                            <?php } ?>
                                            <hr style="border-width: 1px 0px 0px 0px;border-style:solid;border-color: #e0e0e0;
                                                            height:1px;margin-top: 3px;margin-bottom: 4px;width:100%">
                                            <?php if($task->work_type == 'Vendor Audit') {
                                                $bg_type = '#D733FF';
                                            }else if($task->work_type == 'Onsite QC'){
                                                $bg_type = '#0C67EA';
                                            }else{
                                                $bg_type = '#FF9331';
                                            }
                                            ?>
                                            <div class="text-sm-left text-muted" style="float:left; margin-top: 3px;">
                                                <figure class="avatar sm-2 text-white" style="width: 15px; height: 15px; background-color: {{$bg_type}}; font-size: small;" data-initial=""></figure>
                                            </div>
                                            <div class="text-sm-left" style="float:left; padding-left: 10px; color: {{$bg_type}};">
                                                {{$task->work_type}} | {{$task->task_id}}
                                            </div>
                                            <div style="float: right; color: #333333;" >
                                                {{date('m/d/y', strtotime($task->qc_date))}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                    @endforeach
                </div>

                <div class="col-md-3">
                    <div class="card status_title">
                        <h5 class="status_name">IN PROGRESS</h5>
                    </div>

                    @foreach ($task_list_in_progress as $task)
                        <?php
                        $date_standard = $task->qc_date;
                        if(\Illuminate\Support\Carbon::parse($date_standard)->isToday()){
                            $late_css = 'border-left: 5px solid #fbd102; border-radius: 20px;';
                        }else if(\Illuminate\Support\Carbon::parse($date_standard)->isPast()){
                            $late_css = 'border-left: 5px solid #b91d19; border-radius: 20px;';
                        }else{
                            $late_css = ' ';
                        }
                        ?>
                        <div class="card" style="{{$late_css}}">
                            <a href="{{ url('admin/qc_request/'. $task->project_id .'/edit')}}" style="text-decoration: none;">
                                <div class="card-body" style="margin: 0px -5px -20px -5px;">
                                    <div class="media" style="padding-bottom: 0px;">
                                        <div class="form-group" style="width: 100%;">

                                            <div style="color: #8b8a8a; font-weight: 600; float:left; font-size: 13px;">
                                                <b>{{$task->team}}</b> | {{mb_strimwidth($task->brand, 0,15, '..')}}
                                            </div>
                                            <div style="float: right;">
                                                <figure class="avatar mr-2 avatar-sm text-white" style="background-color: #b6b6b6; font-size: 15px;"
                                                        data-toggle="tooltip" data-placement="top"
                                                        data-original-title="{{ $task->author_name }}"
                                                        data-initial="{{ substr($task->author_name, 0, 1) }}">
                                                </figure>
                                            </div>
                                            <div class="media-title" style="clear:both; font-size: large;">
                                                {{$task->name}}
                                            </div>
                                            <div class="text-md-left text-muted" style="margin-top: -8px;">
                                                {{$task->vendor_code}}
                                            </div>
                                            <div class="text-md-left" style="color: #000000;">
                                                {{ mb_strimwidth($task->materials, 0,50, '...') }}
                                            </div>
                                            <?php if(isset($task->result)) { ?>
                                            <?php if($task->result == 'Pass') {
                                                $bg_result = '#3acd2d';
                                            }else if($task->result == 'Reject'){
                                                $bg_result = '#f63d4d';
                                            }else{
                                                $bg_result = '#cecece';
                                            }
                                            ?>
                                            <div style="float: right; color: #333333;" >
                                                <button type="button" class="btn btn-icon" style="font-family: revert;
                                                            font-size: 13px; background-color: {{$bg_result}};
                                                            height: 26px; padding: 0px 7px 2px 10px; margin: -28px 0 0 0;
                                                            border-radius:1.25rem;">
                                                    {{ $task->result }}
                                                </button>
                                            </div>
                                            <?php } ?>
                                            <hr style="border-width: 1px 0px 0px 0px;border-style:solid;border-color: #e0e0e0;
                                                            height:1px;margin-top: 3px;margin-bottom: 4px;width:100%">
                                            <?php if($task->work_type == 'Vendor Audit') {
                                                $bg_type = '#D733FF';
                                            }else if($task->work_type == 'Onsite QC'){
                                                $bg_type = '#0C67EA';
                                            }else{
                                                $bg_type = '#FF9331';
                                            }
                                            ?>
                                            <div class="text-sm-left text-muted" style="float:left; margin-top: 3px;">
                                                <figure class="avatar sm-2 text-white" style="width: 15px; height: 15px; background-color: {{$bg_type}}; font-size: small;" data-initial=""></figure>
                                            </div>
                                            <div class="text-sm-left" style="float:left; padding-left: 10px; color: {{$bg_type}};">
                                                {{$task->work_type}} | {{$task->task_id}}
                                            </div>
                                            <div style="float: right; color: #333333;" >
                                                {{date('m/d/y', strtotime($task->qc_date))}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                    @endforeach
                </div>

                <div class="col-md-3">
                    <div class="card status_title">
                        <h5 class="status_name">ACTION REVIEW</h5>
                    </div>

                    @foreach ($task_list_action_review as $task)
                        <?php
                        $date_standard = $task->qc_date;
                        if(\Illuminate\Support\Carbon::parse($date_standard)->isToday()){
                            $late_css = 'border-left: 5px solid #fbd102; border-radius: 20px;';
                        }else if(\Illuminate\Support\Carbon::parse($date_standard)->isPast()){
                            $late_css = 'border-left: 5px solid #b91d19; border-radius: 20px;';
                        }else{
                            $late_css = ' ';
                        }
                        ?>
                        <div class="card" style="{{$late_css}}">
                            <a href="{{ url('admin/qc_request/'. $task->project_id .'/edit')}}" style="text-decoration: none;">
                                <div class="card-body" style="margin: 0px -5px -20px -5px;">
                                    <div class="media" style="padding-bottom: 0px;">
                                        <div class="form-group" style="width: 100%;">

                                            <div style="color: #8b8a8a; font-weight: 600; float:left; font-size: 13px;">
                                                <b>{{$task->team}}</b> | {{mb_strimwidth($task->brand, 0,15, '..')}}
                                            </div>
                                            <div style="float: right;">
                                                <figure class="avatar mr-2 avatar-sm text-white" style="background-color: #b6b6b6; font-size: 15px;"
                                                        data-toggle="tooltip" data-placement="top"
                                                        data-original-title="{{ $task->author_name }}"
                                                        data-initial="{{ substr($task->author_name, 0, 1) }}">
                                                </figure>
                                            </div>
                                            <div class="media-title" style="clear:both; font-size: large;">
                                                {{$task->name}}
                                            </div>
                                            <div class="text-md-left text-muted" style="margin-top: -8px;">
                                                {{$task->vendor_code}}
                                            </div>
                                            <div class="text-md-left" style="color: #000000;">
                                                {{ mb_strimwidth($task->materials, 0,50, '...') }}
                                            </div>
                                            <?php if(isset($task->result)) { ?>
                                            <?php if($task->result == 'Pass') {
                                                $bg_result = '#3acd2d';
                                            }else if($task->result == 'Reject'){
                                                $bg_result = '#f63d4d';
                                            }else{
                                                $bg_result = '#cecece';
                                            }
                                            ?>
                                            <div style="float: right; color: #333333;" >
                                                <button type="button" class="btn btn-icon" style="font-family: revert;
                                                            font-size: 13px; background-color: {{$bg_result}};
                                                            height: 26px; padding: 0px 7px 2px 10px; margin: -28px 0 0 0;
                                                            border-radius:1.25rem;">
                                                    {{ $task->result }}
                                                </button>
                                            </div>
                                            <?php } ?>
                                            <hr style="border-width: 1px 0px 0px 0px;border-style:solid;border-color: #e0e0e0;
                                                            height:1px;margin-top: 3px;margin-bottom: 4px;width:100%">
                                            <?php if($task->work_type == 'Vendor Audit') {
                                                $bg_type = '#D733FF';
                                            }else if($task->work_type == 'Onsite QC'){
                                                $bg_type = '#0C67EA';
                                            }else{
                                                $bg_type = '#FF9331';
                                            }
                                            ?>
                                            <div class="text-sm-left text-muted" style="float:left; margin-top: 3px;">
                                                <figure class="avatar sm-2 text-white" style="width: 15px; height: 15px; background-color: {{$bg_type}}; font-size: small;" data-initial=""></figure>
                                            </div>
                                            <div class="text-sm-left" style="float:left; padding-left: 10px; color: {{$bg_type}};">
                                                {{$task->work_type}} | {{$task->task_id}}
                                            </div>
                                            <div style="float: right; color: #333333;" >
                                                {{date('m/d/y', strtotime($task->qc_date))}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                    @endforeach
                </div>

                <div class="col-md-3">
                    <div class="card status_title">
                        <h5 class="status_name">ACTION COMPLETED</h5>
                    </div>

                    @foreach ($task_list_action_completed as $task)

                        <div class="card">
                            <a href="{{ url('admin/qc_request/'. $task->project_id .'/edit')}}" style="text-decoration: none;">
                                <div class="card-body" style="margin: 0px -5px -20px -5px;">
                                    <div class="media" style="padding-bottom: 0px;">
                                        <div class="form-group" style="width: 100%;">

                                            <div style="color: #8b8a8a; font-weight: 600; float:left; font-size: 13px;">
                                                <b>{{$task->team}}</b> | {{mb_strimwidth($task->brand, 0,15, '..')}}
                                            </div>
                                            <div style="float: right;">
                                                <figure class="avatar mr-2 avatar-sm text-white" style="background-color: #b6b6b6; font-size: 15px;"
                                                        data-toggle="tooltip" data-placement="top"
                                                        data-original-title="{{ $task->author_name }}"
                                                        data-initial="{{ substr($task->author_name, 0, 1) }}">
                                                </figure>
                                            </div>
                                            <div class="media-title" style="clear:both; font-size: large;">
                                                {{$task->name}}
                                            </div>
                                            <div class="text-md-left text-muted" style="margin-top: -8px;">
                                                {{$task->vendor_code}}
                                            </div>
                                            <div class="text-md-left" style="color: #000000;">
                                                {{ mb_strimwidth($task->materials, 0,50, '...') }}
                                            </div>
                                            <?php if(isset($task->result)) { ?>
                                            <?php if($task->result == 'Pass') {
                                                $bg_result = '#3acd2d';
                                            }else if($task->result == 'Reject'){
                                                $bg_result = '#f63d4d';
                                            }else{
                                                $bg_result = '#cecece';
                                            }
                                            ?>
                                            <div style="float: right; color: #333333;" >
                                                <button type="button" class="btn btn-icon" style="font-family: revert;
                                                            font-size: 13px; background-color: {{$bg_result}};
                                                            height: 26px; padding: 0px 7px 2px 10px; margin: -28px 0 0 0;
                                                            border-radius:1.25rem;">
                                                    {{ $task->result }}
                                                </button>
                                            </div>
                                            <?php } ?>
                                            <hr style="border-width: 1px 0px 0px 0px;border-style:solid;border-color: #e0e0e0;
                                                            height:1px;margin-top: 3px;margin-bottom: 4px;width:100%">
                                            <?php if($task->work_type == 'Vendor Audit') {
                                                $bg_type = '#D733FF';
                                            }else if($task->work_type == 'Onsite QC'){
                                                $bg_type = '#0C67EA';
                                            }else{
                                                $bg_type = '#FF9331';
                                            }
                                            ?>
                                            <div class="text-sm-left text-muted" style="float:left; margin-top: 3px;">
                                                <figure class="avatar sm-2 text-white" style="width: 15px; height: 15px; background-color: {{$bg_type}}; font-size: small;" data-initial=""></figure>
                                            </div>
                                            <div class="text-sm-left" style="float:left; padding-left: 10px; color: {{$bg_type}};">
                                                {{$task->work_type}} | {{$task->task_id}}
                                            </div>
                                            <div style="float: right; color: #333333;" >
                                                {{date('m/d/y', strtotime($task->qc_date))}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                    @endforeach
                </div>



            </div>
        </div>


        @endsection

        <script type="text/javascript">
            // 420 by 1 col
            function moveScrollLeft(){
                var _scrollX = $('.flex-nowrap').scrollLeft();
                $('.flex-nowrap').animate({
                    scrollLeft:_scrollX + 1630}, 500);
            }

            function moveScrollRight(){
                var _scrollX = $('.flex-nowrap').scrollLeft();
                $('.flex-nowrap').animate({
                    scrollLeft:_scrollX - 1630}, 500);
            }
        </script>

        <style type="text/css">

            .left {
                display: inline-block;
                width: 4em;
                height: 4em;
                border-color: #E9E9E9;
                border-radius: 50%;
                margin-right: 1.0em;
                background-color: #EFEFEF;
                border: 1px solid #ebebeb;
            }

            .left:after {
                content: '';
                display: inline-block;
                margin-top: 1.5em;
                margin-left: 1.5em;
                width: 1.0em;
                height: 1.0em;
                border-top: 0.3em solid #848484;
                border-right: 0.3em solid #848484;
                -moz-transform: rotate(-135deg);
                -webkit-transform: rotate(-135deg);
                transform: rotate(-135deg);
            }

            .left:hover {
                background-color: #fdfdfd;
                border-color: #848484;
                border: 1px solid #ebebeb;
            }

            .left:hover:after {
                border-top: 0.3em solid #2f2f2f;
                border-right: 0.3em solid #2f2f2f;
                -moz-transform: rotate(-135deg);
                -webkit-transform: rotate(-135deg);
                transform: rotate(-135deg);
            }

            .right {
                display: inline-block;
                width: 4em;
                height: 4em;
                border-color: #E9E9E9;
                border-radius: 50%;
                margin-left: -0.5em;
                background-color: #EFEFEF;
                border: 1px solid #ebebeb;
            }

            .right:after {
                content: '';
                display: inline-block;
                margin-top: 1.5em;
                margin-left: 1.3em;
                width: 1.0em;
                height: 1.0em;
                border-top: 0.3em solid #848484;
                border-right: 0.3em solid #848484;
                -moz-transform: rotate(45deg);
                -webkit-transform: rotate(45deg);
                transform: rotate(45deg);
            }

            .right:hover {
                background-color: #fdfdfd;
                border-color: #848484;
                border: 1px solid #ebebeb;
            }

            .right:hover:after {
                border-top: 0.3em solid #2f2f2f;
                border-right: 0.3em solid #2f2f2f;
                -moz-transform: rotate(45deg);
                -webkit-transform: rotate(45deg);
                transform: rotate(45deg);
            }

            .follow {
                right: 2%;
                z-index: 1;
            }

        </style>
