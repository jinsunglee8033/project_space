@extends('layouts.dashboard')

@section('content')

    <section class="section">
        <div class="section-header">
            <h1>RA Request Status Board</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></div>
                <div class="breadcrumb-item">RA Request Status Board</div>
            </div>
        </div>

        <div class="section-body">

            @include('admin.ra_request._filter_board')
{{--            @include('admin.asset.flash')--}}

            <div class="row flex-nowrap" style="overflow-x: scroll; padding-top: 17px;">

                <div class="col-md-3">
                    <div class="card status_title">
                        <h5 class="status_name">ACTION REQUESTED</h5>
                    </div>

                    @foreach ($task_list_action_requested as $task)

                        <?php if(isset($task->registration_due_date)) {
                            $card_bg = 'background-color: antiquewhite';
                        }else{
                            $card_bg = ' ';
                        }
                        ?>

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

                        <div class="card" style="{{$card_bg}} {{$late_css}};">
                            <a href="{{ url('admin/ra_request/'. $task->project_id .'/edit#'.$task->ra_request_type_id)}}" style="text-decoration: none;">
                                <div class="card-body" style="margin: 0px -5px -20px -5px;">
                                    <div class="media" style="padding-bottom: 0px;">
                                        <div class="form-group" style="width: 100%;">

                                            <div style="color: #8b8a8a; font-weight: 600; float:left; font-size: 13px;">
                                                <b>{{$task->team}}</b> | {{mb_strimwidth($task->brand, 0,15, '..')}}
                                            </div>
                                            <?php if(isset($task->assignee_name)){ ?>
                                            <div style="float: right;">
                                                <figure class="avatar mr-2 avatar-sm text-white"
                                                        style="background-color: #848484; font-size: 15px; margin-left: -15px; z-index: 1; border: 1px solid white;"
                                                        data-toggle="tooltip" data-placement="top"
                                                        data-original-title="{{ $task->assignee_name }}"
                                                        data-initial="{{ substr($task->assignee_name, 0, 1) }}">
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
                                            <?php if(isset($task->registration_due_date)) { ?>
                                            <div style="float: right; color: #333333;" >
                                                <button type="button" class="btn btn-icon" style="font-family: revert;
                                                            font-size: 13px; background-color: #cecece;
                                                            height: 26px; padding: 0px 7px 2px 10px; margin: -20px 0 0 0;
                                                            border-radius:1.25rem;" data-toggle="tooltip" data-placement="top"
                                                        data-original-title="Est. Registration Due Date">
                                                    {{ date('m/d/y', strtotime($task->registration_due_date)) }}
                                                </button>
                                            </div>
                                            <?php } ?>
                                            <hr style="border-width: 1px 0px 0px 0px;border-style:solid;border-color: #e0e0e0;
                                                            height:1px;margin-top: 15px;margin-bottom: 10px;width:100%">
                                            <?php if($task->request_type == 'formula_review'){
                                                $bg_type = '#0BC27F';
                                            }else if($task->request_type == 'label_review'){
                                                $bg_type = '#4682B4';
                                            }else if($task->request_type == 'us_launch'){
                                                $bg_type = '#D733FF';
                                            }else if($task->request_type == 'canada_launch'){
                                                $bg_type = '#0C67EA';
                                            }else if($task->request_type == 'eu_launch'){
                                                $bg_type = '#FF9331';
                                            }else if($task->request_type == 'uk_launch'){
                                                $bg_type = '#FF332E';
                                            }else{
                                                $bg_type = '#0BC27F';
                                            }
                                            ?>
                                            <div class="text-sm-left text-muted" style="float:left; margin-top: 3px;">
                                                <figure class="avatar sm-2 text-white" style="width: 15px;
                                                        height: 15px; background-color: {{$bg_type}};
                                                        font-size: small;" data-initial="">
                                                </figure>
                                            </div>

                                            <div style="float:left; padding-left: 10px; color: {{$bg_type}};">
                                                {{ strtoupper($task->request_type) }} | {{$task->ra_request_type_id}}
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
                                                <?php }else{ ?>
                                                    <?php if(isset($task->due_date)) { ?>
                                                    <b>{{ date('m/d/y', strtotime($task->due_date)) }}</b>
                                                    <?php } else { ?>
                                                    <b>TDB</b>
                                                    <?php } ?>
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

                        <?php if(isset($task->registration_due_date)) {
                            $card_bg = 'background-color: antiquewhite';
                        }else{
                            $card_bg = ' ';
                        }
                        ?>

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

                        <div class="card" style="{{$card_bg}} {{$late_css}};">
                            <a href="{{ url('admin/ra_request/'. $task->project_id .'/edit#'.$task->ra_request_type_id)}}" style="text-decoration: none;">
                                <div class="card-body" style="margin: 0px -5px -20px -5px;">
                                    <div class="media" style="padding-bottom: 0px;">
                                        <div class="form-group" style="width: 100%;">

                                            <div style="color: #8b8a8a; font-weight: 600; float:left; font-size: 13px;">
                                                <b>{{$task->team}}</b> | {{mb_strimwidth($task->brand, 0,15, '..')}}
                                            </div>
                                            <?php if(isset($task->assignee_name)){ ?>
                                            <div style="float: right;">
                                                <figure class="avatar mr-2 avatar-sm text-white"
                                                        style="background-color: #848484; font-size: 15px; margin-left: -15px; z-index: 1; border: 1px solid white;"
                                                        data-toggle="tooltip" data-placement="top"
                                                        data-original-title="{{ $task->assignee_name }}"
                                                        data-initial="{{ substr($task->assignee_name, 0, 1) }}">
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
                                            <?php if(isset($task->registration_due_date)){?>
                                            <div class="text-md-left" style="margin-top: -8px; color:#b91d19;">
                                                Est. Registration Date : {{date('m/d/y', strtotime($task->registration_due_date))}}
                                            </div>
                                            <?php } ?>
                                            <hr style="border-width: 1px 0px 0px 0px;border-style:solid;border-color: #e0e0e0;
                                                        style=background-color: #4b0cb3;
                                                            height:1px;margin-top: 15px;margin-bottom: 10px;width:100%">
                                            <?php if($task->request_type == 'formula_review'){
                                                $bg_type = '#0BC27F';
                                            }else if($task->request_type == 'label_review'){
                                                $bg_type = '#4682B4';
                                            }else if($task->request_type == 'us_launch'){
                                                $bg_type = '#D733FF';
                                            }else if($task->request_type == 'canada_launch'){
                                                $bg_type = '#0C67EA';
                                            }else if($task->request_type == 'eu_launch'){
                                                $bg_type = '#FF9331';
                                            }else if($task->request_type == 'uk_launch'){
                                                $bg_type = '#FF332E';
                                            }else{
                                                $bg_type = '#0BC27F';
                                            }
                                            ?>
                                            <div class="text-sm-left text-muted" style="float:left; margin-top: 3px;">
                                                <figure class="avatar sm-2 text-white" style="width: 15px;
                                                        height: 15px; background-color: {{$bg_type}};
                                                        font-size: small;" data-initial="">
                                                </figure>
                                            </div>
                                            <div style="float:left; padding-left: 10px; color: {{$bg_type}};">
                                                {{ strtoupper($task->request_type) }} | {{$task->ra_request_type_id}}
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
                                                    <?php }else{ ?>
                                                    <?php if(isset($task->due_date)) { ?>
                                                    <b>{{ date('m/d/y', strtotime($task->due_date)) }}</b>
                                                    <?php } else { ?>
                                                    <b>TDB</b>
                                                    <?php } ?>
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
                            <a href="{{ url('admin/ra_request/'. $task->project_id .'/edit#'.$task->ra_request_type_id)}}" style="text-decoration: none;">
                                <div class="card-body" style="margin: 0px -5px -20px -5px;">
                                    <div class="media" style="padding-bottom: 0px;">
                                        <div class="form-group" style="width: 100%;">

                                            <div style="color: #8b8a8a; font-weight: 600; float:left; font-size: 13px;">
                                                <b>{{$task->team}}</b> | {{mb_strimwidth($task->brand, 0,15, '..')}}
                                            </div>
                                            <?php if(isset($task->assignee_name)){ ?>
                                            <div style="float: right;">
                                                <figure class="avatar mr-2 avatar-sm text-white"
                                                        style="background-color: #848484; font-size: 15px; margin-left: -15px; z-index: 1; border: 1px solid white;"
                                                        data-toggle="tooltip" data-placement="top"
                                                        data-original-title="{{ $task->assignee_name }}"
                                                        data-initial="{{ substr($task->assignee_name, 0, 1) }}">
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
                                            <?php if(isset($task->registration_due_date)) { ?>
                                            <div style="float: right; color: #333333;" >
                                                <button type="button" class="btn btn-icon" style="font-family: revert;
                                                            font-size: 13px; background-color: #cecece;
                                                            height: 26px; padding: 0px 7px 2px 10px; margin: -20px 0 0 0;
                                                            border-radius:1.25rem;"
                                                        data-toggle="tooltip" data-placement="top"
                                                        data-original-title="Est. Registration Due Date">
                                                    {{ date('m/d/y', strtotime($task->registration_due_date)) }}
                                                </button>
                                            </div>
                                            <?php } ?>
                                            <hr style="border-width: 1px 0px 0px 0px;border-style:solid;border-color: #e0e0e0;
                                                            height:1px;margin-top: 15px;margin-bottom: 10px;width:100%">
                                            <?php if($task->request_type == 'formula_review'){
                                                $bg_type = '#0BC27F';
                                            }else if($task->request_type == 'label_review'){
                                                $bg_type = '#4682B4';
                                            }else if($task->request_type == 'us_launch'){
                                                $bg_type = '#D733FF';
                                            }else if($task->request_type == 'canada_launch'){
                                                $bg_type = '#0C67EA';
                                            }else if($task->request_type == 'eu_launch'){
                                                $bg_type = '#FF9331';
                                            }else if($task->request_type == 'uk_launch'){
                                                $bg_type = '#FF332E';
                                            }else{
                                                $bg_type = '#0BC27F';
                                            }
                                            ?>
                                            <div class="text-sm-left text-muted" style="float:left; margin-top: 3px;">
                                                <figure class="avatar sm-2 text-white" style="width: 15px;
                                                        height: 15px; background-color: {{$bg_type}};
                                                        font-size: small;" data-initial="">
                                                </figure>
                                            </div>
                                            <div style="float:left; padding-left: 10px; color: {{$bg_type}};">
                                                {{ strtoupper($task->request_type) }} | {{$task->ra_request_type_id}}
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
                                                    <?php }else{ ?>
                                                    <?php if(isset($task->due_date)) { ?>
                                                    <b>{{ date('m/d/y', strtotime($task->due_date)) }}</b>
                                                    <?php } else { ?>
                                                    <b>TDB</b>
                                                    <?php } ?>
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
                            <a href="{{ url('admin/ra_request/'. $task->project_id .'/edit#'.$task->ra_request_type_id)}}" style="text-decoration: none;">
                                <div class="card-body" style="margin: 0px -5px -20px -5px;">
                                    <div class="media" style="padding-bottom: 0px;">
                                        <div class="form-group" style="width: 100%;">

                                            <div style="color: #8b8a8a; font-weight: 600; float:left; font-size: 13px;">
                                                <b>{{$task->team}}</b> | {{mb_strimwidth($task->brand, 0,15, '..')}}
                                            </div>
                                            <?php if(isset($task->assignee_name)){ ?>
                                            <div style="float: right;">
                                                <figure class="avatar mr-2 avatar-sm text-white"
                                                        style="background-color: #848484; font-size: 15px; margin-left: -15px; z-index: 1; border: 1px solid white;"
                                                        data-toggle="tooltip" data-placement="top"
                                                        data-original-title="{{ $task->assignee_name }}"
                                                        data-initial="{{ substr($task->assignee_name, 0, 1) }}">
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

                                            <?php if(isset($task->registration_number)) { ?>
                                            <div class="text-md-left" style="margin-top: -8px; color: #b91d19;">
                                                Registration # : {{$task->registration_number}}
                                            </div>
                                            <?php } ?>

                                            <?php if(isset($task->registration_due_date)) { ?>
                                            <div style="float: right; color: #333333;" >
                                                <button type="button" class="btn btn-icon" style="font-family: revert;
                                                            font-size: 13px; background-color: #cecece;
                                                            height: 26px; padding: 0px 7px 2px 10px; margin: -20px 0 0 0;
                                                            border-radius:1.25rem;"
                                                        data-toggle="tooltip" data-placement="top"
                                                        data-original-title="Est. Registration Due Date">
                                                    {{ date('m/d/y', strtotime($task->registration_due_date)) }}
                                                </button>
                                            </div>
                                            <?php } ?>
                                            <hr style="border-width: 1px 0px 0px 0px;border-style:solid;border-color: #e0e0e0;
                                                            height:1px;margin-top: 15px;margin-bottom: 10px;width:100%">
                                            <?php if($task->request_type == 'formula_review'){
                                                $bg_type = '#0BC27F';
                                            }else if($task->request_type == 'label_review'){
                                                $bg_type = '#4682B4';
                                            }else if($task->request_type == 'us_launch'){
                                                $bg_type = '#D733FF';
                                            }else if($task->request_type == 'canada_launch'){
                                                $bg_type = '#0C67EA';
                                            }else if($task->request_type == 'eu_launch'){
                                                $bg_type = '#FF9331';
                                            }else if($task->request_type == 'uk_launch'){
                                                $bg_type = '#FF332E';
                                            }else{
                                                $bg_type = '#0BC27F';
                                            }
                                            ?>
                                            <div class="text-sm-left text-muted" style="float:left; margin-top: 3px;">
                                                <figure class="avatar sm-2 text-white" style="width: 15px;
                                                        height: 15px; background-color: {{$bg_type}};
                                                        font-size: small;" data-initial="">
                                                </figure>
                                            </div>
                                            <div style="float:left; padding-left: 10px; color: {{$bg_type}};">
                                                {{ strtoupper($task->request_type) }} | {{$task->ra_request_type_id}}
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
                                                    <?php }else{ ?>
                                                    <?php if(isset($task->due_date)) { ?>
                                                    <b>{{ date('m/d/y', strtotime($task->due_date)) }}</b>
                                                    <?php } else { ?>
                                                    <b>TDB</b>
                                                    <?php } ?>
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
