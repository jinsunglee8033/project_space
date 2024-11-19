@extends('layouts.dashboard')

@section('content')

    <section class="section">
        <div class="section-header">
            <h1>NPD PO Request Status Board</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></div>
                <div class="breadcrumb-item">NPD PO Request Status Board</div>
            </div>
        </div>

        <div class="section-body">

            @include('admin.npd_po_request._filter')
{{--            @include('admin.asset.flash')--}}

            <div class="row flex-nowrap" style="overflow-x: scroll; padding-top: 17px;">

                <div class="col-md-3">
                    <div class="card status_title">
                        <h5 class="status_name">ACTION REQUESTED</h5>
                    </div>

                    @foreach ($task_list_action_requested as $task)

                        <?php
                        if($task->revision_cnt > 0){
                            $date_standard = $task->due_date_revision;
                        }else if(isset($task->due_date_urgent)){
                            $date_standard = $task->due_date_urgent;
                        }else{
                            $date_standard = $task->due_date;
                        }
                        if(\Illuminate\Support\Carbon::parse($date_standard)->isToday()){
                            $late_css = 'border-left: 5px solid #fbd102; border-radius: 20px;';
                        }else if(\Illuminate\Support\Carbon::parse($date_standard)->isPast()){
                            $late_css = 'border-left: 5px solid #b91d19; border-radius: 20px;';
                        }else{
                            $late_css = ' ';
                        }
                        ?>

                        <div class="card" style="{{$late_css}}">
                            <a href="{{ url('admin/npd_po_request/'. $task->project_id .'/edit#'.$task->task_id)}}" style="text-decoration: none;">
                                <div class="card-body" style="margin: 0px -5px -20px -5px;">
                                    <div class="media" style="padding-bottom: 0px;">
                                        <div class="form-group" style="width: 100%;">

                                            <div style="color: #8b8a8a; font-weight: 600; float:left; font-size: 13px;">
                                                <b>{{$task->team}}</b> | {{mb_strimwidth($task->brand, 0,15, '..')}}
                                            </div>
                                            <?php if(isset($task->buyer_name)){ ?>
                                            <div style="float: right;">
                                                <figure class="avatar mr-2 avatar-sm text-white"
                                                        style="background-color: #848484; font-size: 15px; margin-left: -15px; z-index: 1; border: 1px solid white;"
                                                        data-toggle="tooltip" data-placement="top"
                                                        data-original-title="{{ $task->buyer_name }}"
                                                        data-initial="{{ substr($task->buyer_name, 0, 1) }}">
                                                </figure>
                                            </div>
                                            <?php } ?>
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
{{--                                            <div class="text-md-left text-muted" style="margin-top: -8px;">--}}
{{--                                                {{$task->brand}}--}}
{{--                                            </div>--}}
                                            <hr style="border-width: 1px 0px 0px 0px;border-style:solid;border-color: #e0e0e0;
                                                            height:1px;margin-top: 15px;margin-bottom: 10px;width:100%">
                                            <?php if($task->price_set_up == 'Final Price') {
                                                $bg_type = '#D733FF';
                                            }else if($task->price_set_up == 'Temporary Price (Approved by Division Leader)'){
                                                $bg_type = '#0C67EA';
                                            }else{
                                                $bg_type = '#008000';
                                            }
                                            ?>
                                            <div class="text-sm-left text-muted" style="float:left; margin-top: 3px;">
                                                <figure class="avatar sm-2 text-white"
                                                        style="width: 35px; height: 35px;
                                                                background-color: {{$bg_type}};
                                                                padding: 10px 0 0 0;
                                                                margin: -5px 0 0 0;
                                                                display: flex;
                                                                flex-wrap: nowrap;
                                                                justify-content: space-evenly;
                                                                font-size: small;" data-initial="">
                                                    {{ $task->task_id }}
                                                </figure>
                                            </div>
                                            <div class="text-sm-left text-muted" style="float:left; padding-left: 10px; margin-top: 5px;">
                                                <?php if($task->due_date_urgent){ ?>
                                                <b style="color: #b91d19;">{{ date('m/d/y', strtotime($task->due_date_urgent))}}</b>
                                                <?php }else{ ?>
                                                {{ date('m/d/y', strtotime($task->due_date))}}
                                                <?php } ?>
                                            </div>
                                            <div style="float: right; color: #333333;" >
                                                <?php if($task->revision_cnt > 0) { ?>
                                                    <button type="button" class="btn btn-icon" style="font-family: revert;
                                                            font-size: 13px; background-color: #ffc107;
                                                            height: 26px; padding: 0px 7px 2px 10px; margin: -3px 0 0 0;
                                                            border-radius:1.25rem;">
                                                        {{ date('m/d/y', strtotime($task->due_date_revision)) }}
                                                        <span class="badge badge-transparent" style="color: #b91d19;">{{ $task->revision_cnt }}</span>
                                                    </button>
                                                <?php } ?>
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
                        if($task->revision_cnt > 0){
                            $date_standard = $task->due_date_revision;
                        }else if(isset($task->due_date_urgent)){
                            $date_standard = $task->due_date_urgent;
                        }else{
                            $date_standard = $task->due_date;
                        }
                        if(\Illuminate\Support\Carbon::parse($date_standard)->isToday()){
                            $late_css = 'border-left: 5px solid #fbd102; border-radius: 20px;';
                        }else if(\Illuminate\Support\Carbon::parse($date_standard)->isPast()){
                            $late_css = 'border-left: 5px solid #b91d19; border-radius: 20px;';
                        }else{
                            $late_css = ' ';
                        }
                        ?>

                        <div class="card" style="{{$late_css}}">
                            <a href="{{ url('admin/npd_po_request/'. $task->project_id .'/edit#'.$task->task_id)}}" style="text-decoration: none;">
                                <div class="card-body" style="margin: 0px -5px -20px -5px;">
                                    <div class="media" style="padding-bottom: 0px;">
                                        <div class="form-group" style="width: 100%;">

                                            <div style="color: #8b8a8a; font-weight: 600; float:left; font-size: 13px;">
                                                <b>{{$task->team}}</b> | {{mb_strimwidth($task->brand, 0,15, '..')}}
                                            </div>
                                            <div style="float: right;">
                                                <figure class="avatar mr-2 avatar-sm text-white"
                                                        style="background-color: #848484; font-size: 15px; margin-left: -15px; z-index: 1; border: 1px solid white;"
                                                        data-toggle="tooltip" data-placement="top"
                                                        data-original-title="{{ $task->buyer_name }}"
                                                        data-initial="{{ substr($task->buyer_name, 0, 1) }}">
                                                </figure>
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
{{--                                            <div class="text-md-left text-muted" style="margin-top: -8px;">--}}
{{--                                                {{$task->brand}}--}}
{{--                                            </div>--}}
                                            <hr style="border-width: 1px 0px 0px 0px;border-style:solid;border-color: #e0e0e0;
                                                            height:1px;margin-top: 15px;margin-bottom: 10px;width:100%">
                                            <?php if($task->price_set_up == 'Final Price') {
                                                $bg_type = '#D733FF';
                                            }else if($task->price_set_up == 'Temporary Price (Approved by Division Leader)'){
                                                $bg_type = '#0C67EA';
                                            }else{
                                                $bg_type = '#008000';
                                            }
                                            ?>
                                            <div class="text-sm-left text-muted" style="float:left; margin-top: 3px;">
                                                <figure class="avatar sm-2 text-white"
                                                        style="width: 35px; height: 35px;
                                                                background-color: {{$bg_type}};
                                                                padding: 10px 0 0 0;
                                                                margin: -5px 0 0 0;
                                                                display: flex;
                                                                flex-wrap: nowrap;
                                                                justify-content: space-evenly;
                                                                font-size: small;" data-initial="">
                                                    {{ $task->task_id }}
                                                </figure>
                                            </div>
                                            <div class="text-sm-left text-muted" style="float:left; padding-left: 10px; margin-top: 5px;">
                                                <?php if($task->due_date_urgent){ ?>
                                                <b style="color: #b91d19;">{{ date('m/d/y', strtotime($task->due_date_urgent))}}</b>
                                                <?php }else{ ?>
                                                {{ date('m/d/y', strtotime($task->due_date))}}
                                                <?php } ?>
                                            </div>
                                            <div style="float: right; color: #333333; margin: -1px 0 0 0;" >
                                                <?php if($task->revision_cnt > 0) { ?>
                                                    <button type="button" class="btn btn-icon" style="font-family: revert;
                                                            font-size: 13px; background-color: #ffc107;
                                                            height: 26px; padding: 0px 7px 2px 10px; margin: -3px 0 0 0;
                                                            border-radius:1.25rem;">
                                                        {{ date('m/d/y', strtotime($task->due_date_revision)) }}
                                                        <span class="badge badge-transparent" style="color: #b91d19;">{{ $task->revision_cnt }}</span>
                                                    </button>
                                                <?php } ?>
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
                        if($task->revision_cnt > 0){
                            $date_standard = $task->due_date_revision;
                        }else if(isset($task->due_date_urgent)){
                            $date_standard = $task->due_date_urgent;
                        }else{
                            $date_standard = $task->due_date;
                        }
                        if(\Illuminate\Support\Carbon::parse($date_standard)->isToday()){
                            $late_css = 'border-left: 5px solid #fbd102; border-radius: 20px;';
                        }else if(\Illuminate\Support\Carbon::parse($date_standard)->isPast()){
                            $late_css = 'border-left: 5px solid #b91d19; border-radius: 20px;';
                        }else{
                            $late_css = ' ';
                        }
                        ?>

                        <div class="card" style="{{$late_css}}">
                            <a href="{{ url('admin/npd_po_request/'. $task->project_id .'/edit#'.$task->task_id)}}" style="text-decoration: none;">
                                <div class="card-body" style="margin: 0px -5px -20px -5px;">
                                    <div class="media" style="padding-bottom: 0px;">
                                        <div class="form-group" style="width: 100%;">

                                            <div style="color: #8b8a8a; font-weight: 600; float:left; font-size: 13px;">
                                                <b>{{$task->team}}</b> | {{mb_strimwidth($task->brand, 0,15, '..')}}
                                            </div>
                                            <div style="float: right;">
                                                <figure class="avatar mr-2 avatar-sm text-white"
                                                        style="background-color: #848484; font-size: 15px; margin-left: -15px; z-index: 1; border: 1px solid white;"
                                                        data-toggle="tooltip" data-placement="top"
                                                        data-original-title="{{ $task->buyer_name }}"
                                                        data-initial="{{ substr($task->buyer_name, 0, 1) }}">
                                                </figure>
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
{{--                                            <div class="text-md-left text-muted" style="margin-top: -8px;">--}}
{{--                                                {{$task->brand}}--}}
{{--                                            </div>--}}
                                            <hr style="border-width: 1px 0px 0px 0px;border-style:solid;border-color: #e0e0e0;
                                                            height:1px;margin-top: 15px;margin-bottom: 10px;width:100%">
                                            <?php if($task->price_set_up == 'Final Price') {
                                                $bg_type = '#D733FF';
                                            }else if($task->price_set_up == 'Temporary Price (Approved by Division Leader)'){
                                                $bg_type = '#0C67EA';
                                            }else{
                                                $bg_type = '#008000';
                                            }
                                            ?>
                                            <div class="text-sm-left text-muted" style="float:left; margin-top: 3px;">
                                                <figure class="avatar sm-2 text-white"
                                                        style="width: 35px; height: 35px;
                                                                background-color: {{$bg_type}};
                                                                padding: 10px 0 0 0;
                                                                margin: -5px 0 0 0;
                                                                display: flex;
                                                                flex-wrap: nowrap;
                                                                justify-content: space-evenly;
                                                                font-size: small;" data-initial="">
                                                    {{ $task->task_id }}
                                                </figure>
                                            </div>
                                            <div class="text-sm-left text-muted" style="float:left; padding-left: 10px; margin-top: 5px;">
                                                <?php if($task->due_date_urgent){ ?>
                                                <b style="color: #b91d19;">{{ date('m/d/y', strtotime($task->due_date_urgent))}}</b>
                                                <?php }else{ ?>
                                                {{ date('m/d/y', strtotime($task->due_date))}}
                                                <?php } ?>
                                            </div>
                                            <div style="float: right; color: #333333;" >
                                                <?php if($task->revision_cnt > 0) { ?>
                                                    <button type="button" class="btn btn-icon" style="font-family: revert;
                                                            font-size: 13px; background-color: #ffc107;
                                                            height: 26px; padding: 0px 7px 2px 10px; margin: -3px 0 0 0;
                                                            border-radius:1.25rem;">
                                                        {{ date('m/d/y', strtotime($task->due_date_revision)) }}
                                                        <span class="badge badge-transparent" style="color: #b91d19;">{{ $task->revision_cnt }}</span>
                                                    </button>
                                                <?php } ?>
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
                            <a href="{{ url('admin/npd_po_request/'. $task->project_id .'/edit#'.$task->task_id)}}" style="text-decoration: none;">
                                <div class="card-body" style="margin: 0px -5px -20px -5px;">
                                    <div class="media" style="padding-bottom: 0px;">
                                        <div class="form-group" style="width: 100%;">

                                            <div style="color: #8b8a8a; font-weight: 600; float:left; font-size: 13px;">
                                                <b>{{$task->team}}</b> | {{mb_strimwidth($task->brand, 0,15, '..')}}
                                            </div>
                                            <div style="float: right;">
                                                <figure class="avatar mr-2 avatar-sm text-white"
                                                        style="background-color: #848484; font-size: 15px; margin-left: -15px; z-index: 1; border: 1px solid white;"
                                                        data-toggle="tooltip" data-placement="top"
                                                        data-original-title="{{ $task->buyer_name }}"
                                                        data-initial="{{ substr($task->buyer_name, 0, 1) }}">
                                                </figure>
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
                                            <?php if(isset($task->po)) { ?>
                                            <div class="text-md-left" style="margin-top: -8px; color: #b91d19;">
                                                PO # : {{$task->po}}
                                            </div>
                                            <?php } ?>
                                            <hr style="border-width: 1px 0px 0px 0px;border-style:solid;border-color: #e0e0e0;
                                                            height:1px;margin-top: 15px;margin-bottom: 10px;width:100%">
                                            <?php if($task->price_set_up == 'Final Price') {
                                                $bg_type = '#D733FF';
                                            }else if($task->price_set_up == 'Temporary Price (Approved by Division Leader)'){
                                                $bg_type = '#0C67EA';
                                            }else{
                                                $bg_type = '#008000';
                                            }
                                            ?>
                                            <div class="text-sm-left text-muted" style="float:left; margin-top: 3px;">
                                                <figure class="avatar sm-2 text-white"
                                                        style="width: 35px; height: 35px;
                                                                background-color: {{$bg_type}};
                                                                padding: 10px 0 0 0;
                                                                margin: -5px 0 0 0;
                                                                display: flex;
                                                                flex-wrap: nowrap;
                                                                justify-content: space-evenly;
                                                                font-size: small;" data-initial="">
                                                    {{ $task->task_id }}
                                                </figure>
                                            </div>
                                            <div class="text-sm-left text-muted" style="float:left; padding-left: 10px; margin-top: 5px;">
                                                <?php if($task->due_date_urgent){ ?>
                                                <b style="color: #b91d19;">{{ date('m/d/y', strtotime($task->due_date_urgent))}}</b>
                                                <?php }else{ ?>
                                                {{ date('m/d/y', strtotime($task->due_date))}}
                                                <?php } ?>
                                            </div>
                                            <div style="float: right; color: #333333;" >
                                                <?php if($task->revision_cnt > 0) { ?>
                                                <button type="button" class="btn btn-icon" style="font-family: revert;
                                                            font-size: 13px; background-color: #ffc107;
                                                            height: 26px; padding: 0px 7px 2px 10px; margin: -3px 0 0 0;
                                                            border-radius:1.25rem;">
                                                    {{ date('m/d/y', strtotime($task->due_date_revision)) }}
                                                    <span class="badge badge-transparent" style="color: #b91d19;">{{ $task->revision_cnt }}</span>
                                                </button>
                                                <?php } ?>
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
