@extends('layouts.dashboard')

@section('content')

    <section class="section">
        <div class="section-header">
            <h1>NPD Design Request Status Board</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></div>
                <div class="breadcrumb-item">NPD Design Request Status Board</div>
            </div>
        </div>

        <div class="section-body">

            @include('admin.npd_design_request._filter_board')
{{--            @include('admin.asset.flash')--}}

            <div class="row flex-nowrap" style="overflow-x: scroll; padding-top: 17px;">

                <div class="col-md-3">
                    <div class="card status_title">
                        <h5 class="status_name">TO DO</h5>
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
                            <a href="{{ url('admin/npd_design_request/'. $task->project_id .'/edit#'.$task->npd_design_request_type_id)}}" style="text-decoration: none;">
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
                                            <div class="text-md-left text-muted" style="margin-top: -8px;">
                                                {{$task->request_type}} | {{$task->design_group}}
                                            </div>
                                            <hr style="border-width: 1px 0px 0px 0px;border-style:solid;border-color: #e0e0e0;
                                                            height:1px;margin-top: 15px;margin-bottom: 10px;width:100%">
                                            <?php if($task->priority == 'Urgent') {
                                                $bg_type = '#D733FF';
                                            }else if($task->priority == 'Normal'){
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
                                                    {{ $task->npd_design_request_type_id }}
                                                </figure>
                                            </div>
                                            <div class="text-sm-left text-muted" style="float:left; padding-left: 10px; margin-top: 5px;">
                                                <?php if($task->due_date_urgent){ ?>
                                                <b style="color: #b91d19;">{{ date('m/d/y', strtotime($task->due_date_urgent))}}</b>
                                                <?php }else{ ?>
                                                {{ date('m/d/y', strtotime($task->due_date))}}
                                                <?php } ?>
                                            </div>
                                            <div style="float: right; color: #333333; margin-top: 6px;">
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
                            <a href="{{ url('admin/npd_design_request/'. $task->project_id .'/edit#'.$task->npd_design_request_type_id)}}" style="text-decoration: none;">
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
                                            <div class="text-md-left text-muted" style="margin-top: -8px;">
                                                {{$task->request_type}} | {{$task->design_group}}
                                            </div>
                                            <hr style="border-width: 1px 0px 0px 0px;border-style:solid;border-color: #e0e0e0;
                                                            height:1px;margin-top: 15px;margin-bottom: 10px;width:100%">
                                            <?php if($task->priority == 'Urgent') {
                                                $bg_type = '#D733FF';
                                            }else if($task->priority == 'Normal'){
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
                                                    {{ $task->npd_design_request_type_id }}
                                                </figure>
                                            </div>
                                            <div class="text-sm-left text-muted" style="float:left; padding-left: 10px; margin-top: 5px;">
                                                <?php if($task->due_date_urgent){ ?>
                                                <b style="color: #b91d19;">{{ date('m/d/y', strtotime($task->due_date_urgent))}}</b>
                                                <?php }else{ ?>
                                                {{ date('m/d/y', strtotime($task->due_date))}}
                                                <?php } ?>
                                            </div>
                                            <div style="float: right; color: #333333; margin-top: 6px;">
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
                            <a href="{{ url('admin/npd_design_request/'. $task->project_id .'/edit#'.$task->npd_design_request_type_id)}}" style="text-decoration: none;">
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
                                            <div class="text-md-left text-muted" style="margin-top: -8px;">
                                                {{$task->request_type}} | {{$task->design_group}}
                                            </div>
                                            <hr style="border-width: 1px 0px 0px 0px;border-style:solid;border-color: #e0e0e0;
                                                            height:1px;margin-top: 15px;margin-bottom: 10px;width:100%">
                                            <?php if($task->priority == 'Urgent') {
                                                $bg_type = '#D733FF';
                                            }else if($task->priority == 'Normal'){
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
                                                    {{ $task->npd_design_request_type_id }}
                                                </figure>
                                            </div>
                                            <div class="text-sm-left text-muted" style="float:left; padding-left: 10px; margin-top: 5px;">
                                                <?php if($task->status == 'update_required'){ ?>
                                                <b style="color: #19b941;">{{ strtoupper(str_replace('_', ' ', $task->status)) }}</b> |
                                                <?php }else{ ?>
                                                    <b style="color: #b91d19;">{{ strtoupper(str_replace('_', ' ', $task->status)) }}</b> |
                                                <?php } ?>
                                                <?php if($task->due_date_urgent){ ?>
                                                <b style="color: #b91d19;">{{ date('m/d/y', strtotime($task->due_date_urgent))}}</b>
                                                <?php }else{ ?>
                                                {{ date('m/d/y', strtotime($task->due_date))}}
                                                <?php } ?>
                                            </div>
                                            <div style="float: right; color: #333333; margin-top: 6px;">
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
                            <a href="{{ url('admin/npd_design_request/'. $task->project_id .'/edit#'.$task->npd_design_request_type_id)}}" style="text-decoration: none;">
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
                                            <div class="text-md-left text-muted" style="margin-top: -8px;">
                                                {{$task->request_type}} | {{$task->design_group}}
                                            </div>
                                            <hr style="border-width: 1px 0px 0px 0px;border-style:solid;border-color: #e0e0e0;
                                                            height:1px;margin-top: 15px;margin-bottom: 10px;width:100%">
                                            <?php if($task->priority == 'Urgent') {
                                                $bg_type = '#D733FF';
                                            }else if($task->priority == 'Normal'){
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
                                                    {{ $task->npd_design_request_type_id }}
                                                </figure>
                                            </div>
                                            <div class="text-sm-left text-muted" style="float:left; padding-left: 10px; margin-top: 5px;">
                                                <?php if($task->due_date_urgent){ ?>
                                                <b style="color: #b91d19;">{{ date('m/d/y', strtotime($task->due_date_urgent))}}</b>
                                                <?php }else{ ?>
                                                {{ date('m/d/y', strtotime($task->due_date))}}
                                                <?php } ?>
                                            </div>
                                            <div style="float: right; color: #333333; margin-top: 6px;">
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


