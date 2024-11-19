@extends('layouts.dashboard')

@section('content')

    <?php
    if (!empty($project)){
        if($project->status == 'archived'){
            $status = 'archived';
            $second = 'Project Archives';
            $third = 'Show Project';
        }else if($project->status == 'deleted'){
            $status = 'deleted';
            $second = 'Project Deleted';
            $third = 'Deleted Project';
        }else{
            $status = 'active';
            $second = 'Project Manage';
            $third = 'Update Project';
        }
    }else{
        $status = 'active';
        $second = 'NPD Project Manage';
        $third = 'Create NPD Project';
    }

    ?>

{{--    <script src="https://cloud.tinymce.com/5/tinymce.min.js?apiKey=u0nqw6bdnhchs7yky70br91x6sl6ja1nc4hor8asbmv2ie3a"></script>--}}


    <style>
        .create_note::before {
            white-space: pre;
        }
    </style>

    <style>

        .asset--grid-row{
            --f: arial;
            --f-size: 15px;
            --light-steal-blue: #dee7ea;
            --prim: #ecdbe8;
            --hosta-flower: #dcdde7;
            font-family: var(--f);
            font-size: var(--f-size);
            background: #fff;
            border-radius: 20px;
            padding: 10px 20px;
            --copy-writer-bg: var(--light-steal-blue);
            --asset-creator-bg: var(--prim);
            --assignee-bg: var(--hosta-flower);
            display: grid;
            gap: 20px;
            grid-template-columns: 200px 1fr 38px;
        }

        .asset--grid-row li{
            padding: 0px 2px;
        }

        .asset--grid-row *{
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .project-info{
            border-right: 1px solid #ccc;
            margin-right: -60px;
        }

        .project-members-list span{
            border-radius: 5px;
            padding: 3px 6px;
        }

        .copy-writer-bg{
            background-color: var(--copy-writer-bg);
        }

        .asset-creator-bg{
            background-color: var(--asset-creator-bg);
        }

        .assignee-bg{
            background-color: var(--assignee-bg);
        }

        .project-action-icons{
            display: flex;
            justify-content: flex-end;
        }
        .project-icons{
            display: flex;
            align-items: center;
        }
        .inner_box {
            margin: 0px 15px 0px 15px;
        }

    </style>

    <section class="section">
        @include('admin.project.flash')
        <div class="section-header">
            <h1>Project NPD</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ url('admin/project') }}">{{ $second }}</a></div>
                <div class="breadcrumb-item active">{{ $third }}</div>
            </div>
        </div>

        <div class="section-body">
            <h2 class="section-title">{{ $third }}</h2>
            <div class="row">
                <div class="col-lg-7">
                    @if (empty($project))
                        <form method="POST" action="{{ route('project.store') }}" enctype="multipart/form-data">
                    @else
                        <form method="POST" action="{{ route('project.update', $project->id) }}" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="{{ $project->id }}" />
                            @method('POST')
                    @endif
                    @csrf
                            <div class="card">
                                <div class="card-header">
                                    <h4>{{ $third }} {{ !empty($project->id) ? '#'.$project->id : ''}}</h4>
                                    @if ( ($third == 'Update Project') && (auth()->user()->role == 'admin') )
                                        <div class="text-right">
                                            <button class="btn btn-primary" id="send_archive" onclick="send_archive_project({{ $project->id }})">Send Archive</button>
                                        </div>
                                    @elseif ( ($third == 'Deleted Project') && (auth()->user()->role == 'admin') )
                                        <div class="text-right">
                                            <button class="btn btn-primary" id="send_active" onclick="send_active_project({{ $project->id }})">Send Active Back</button>
                                        </div>
                                    @endif
                                </div>
                                <div class="card-body">
                                    <div class="col">

                                        <div class="form-group">
                                            @if($author_name != null)
                                                <p style="float: right">Project Creator &nbsp
                                                    <span style="color:#000000; font-size: medium;background-color: #ecdbe8;border-radius: 6px;">
                                                    &nbsp{{ $author_name }}&nbsp
                                                    </span>
                                                </p>
                                            @endif
                                            <label>Team: <b style="color: #b91d19">*</b></label>
                                                <input type="text" name="team" readonly
                                                       class="form-control @error('team') is-invalid @enderror @if (!$errors->has('team') && old('team')) is-valid @endif"
                                                       value="{{ old('team', !empty($project) ? $project->team : $team) }}" required>
                                                @error('team')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            @error('team')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label>Brand: <b style="color: #b91d19">*</b></label>
                                            <select class="form-control @error('brand') is-invalid @enderror @if (!$errors->has('brand') && old('brand')) is-valid @endif"
                                                    name="brand" required>
                                                <option value="">Select</option>
                                                @if(empty(old('brand')))
                                                    @foreach ($brands as $value)
                                                        <option value="{{ $value->name }}" {{ $value->name == $brand ? 'selected' : '' }}>
                                                            {{ $value->name }}
                                                        </option>
                                                    @endforeach
                                                @else
                                                    @foreach ($brands as $value)
                                                        <option value="{{ $value->name }}" {{ $value->name == old('brand') ? 'selected' : '' }}>
                                                            {{ $value->name }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            @error('brand')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label>Project Name: <b style="color: #b91d19">*</b></label>
                                            <input type="text" name="name"
                                                   class="form-control @error('name') is-invalid @enderror @if (!$errors->has('name') && old('name')) is-valid @endif"
                                                   value="{{ old('name', !empty($project) ? $project->name : null) }}" required>
                                            @error('name')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label>Description: </label>
                                            {!! Form::textarea('description', !empty($project) ? $project->description : null, ['class' => 'form-control summernote']) !!}
                                            @error('description')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Project Type: <b style="color: #b91d19">*</b></label>
                                                    <select class="form-control @error('project_type') is-invalid @enderror @if (!$errors->has('project_type') && old('project_type')) is-valid @endif"
                                                            name="project_type" required>
                                                        <option value="">Select</option>
                                                        @if(empty(old('project_type')))
                                                            @foreach ($project_types as $value)
                                                                <option value="{{ $value }}" {{ $value == $project_type ? 'selected' : '' }}>
                                                                    {{ $value }}
                                                                </option>
                                                            @endforeach
                                                        @else
                                                            @foreach ($project_types as $value)
                                                                <option value="{{ $value }}" {{ $value == old('project_type') ? 'selected' : '' }}>
                                                                    {{ $value }}
                                                                </option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                    @error('project_type')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Project Year: <b style="color: #b91d19">*</b></label>
                                                    <select class="form-control @error('project_year') is-invalid @enderror @if (!$errors->has('project_year') && old('project_year')) is-valid @endif"
                                                            name="project_year" required>
                                                        <option value="">Select</option>
                                                        @if(empty(old('project_year')))
                                                            @foreach ($project_year_list as $value)
                                                                <option value="{{ $value }}" {{ $value == $project_year ? 'selected' : '' }}>
                                                                    {{ $value }}
                                                                </option>
                                                            @endforeach
                                                        @else
                                                            @foreach ($project_year_list as $value)
                                                                <option value="{{ $value }}" {{ $value == old('project_year') ? 'selected' : '' }}>
                                                                    {{ $value }}
                                                                </option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                    @error('project_year')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>International Sales Plan: <b style="color: #b91d19">*</b></label>
                                                    <select class="form-control @error('international_sales_plan') is-invalid @enderror @if (!$errors->has('international_sales_plan') && old('international_sales_plan')) is-valid @endif"
                                                            name="international_sales_plan" required>
                                                        <option value="">Select</option>
                                                        @if(!empty($project))
                                                            @foreach ($sales_plan_list as $value)
                                                                <option value="{{ $value }}" {{ $value == $project->international_sales_plan ? 'selected' : '' }}>
                                                                    {{ $value }}
                                                                </option>
                                                            @endforeach
                                                        @else
                                                            @foreach ($sales_plan_list as $value)
                                                                <option value="{{ $value }}" {{ $value == old('international_sales_plan') ? 'selected' : '' }}>
                                                                    {{ $value }}
                                                                </option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                    @error('international_sales_plan')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Sale Available Date: <b style="color: #b91d19">(Select from the calendar)</b></label>
                                                    <input type="text" name="sale_available_date" id="sale_available_date" placeholder="Sale Available Date" autocomplete="off"
                                                           class="form-control datepicker @error('sale_available_date') is-invalid @enderror @if (!$errors->has('sale_available_date') && old('sale_available_date')) is-valid @endif"
                                                           value="{{ old('sale_available_date', !empty($project) ? $project->sale_available_date : null) }}">
                                                    @error('sale_available_date')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Launch Date: <b style="color: #b91d19">*</b> <b style="color: #b91d19">(Select from the calendar)</b></label>
                                                    <input type="text" name="launch_date" id="launch_date" placeholder="Launch Date" autocomplete="off" required
                                                           class="form-control datepicker @error('launch_date') is-invalid @enderror @if (!$errors->has('launch_date') && old('launch_date')) is-valid @endif"
                                                           value="{{ old('launch_date', !empty($project) ? $project->launch_date : null) }}">
                                                    @error('launch_date')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Launch Date (LA): <b style="color: #b91d19">(Select from the calendar)</b></label>
                                                    <input type="text" name="target_date" id="target_date" placeholder="Launch Date (LA)" autocomplete="off"
                                                           class="form-control datepicker @error('target_date') is-invalid @enderror @if (!$errors->has('target_date') && old('target_date')) is-valid @endif"
                                                           value="{{ old('target_date', !empty($project) ? $project->target_date : null) }}">
                                                    @error('target_date')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                    <?php if(!empty($launch_date_history_text)) { ?>
                                        <div class="media-description text-muted" style="padding: 15px;">
                                            {!! $launch_date_history_text !!}
                                        </div>
                                    <?php } ?>

                                    </div>
                                </div>
                                <div class="card-footer text-right">
                                    <?php if((!empty($project)) && ($project->status == 'pending')){ ?>
                                        <?php if( auth()->user()->role == 'Admin' || auth()->user()->role == 'Team Lead' ) {?>
                                            <input type="button"
                                                   value="Revision"
                                                   style="font-size: medium;"
                                                   data-toggle="modal"
                                                   data-target="#revision_reason_project_{{$project->id}}"
                                                   class="btn btn-lg btn-revision submit"/>

                                            <input type="button"
                                                   value="Approve"
                                                   onclick="approve_project({{$project->id}})"
                                                   style="font-size: medium;"
                                                   class="btn btn-lg btn-info submit"/>
                                        <?php } ?>
                                    <?php }else if((!empty($project)) && ($project->status == 'review') && (auth()->user()->role != 'Team Lead') ){ ?>
                                        <label style="font-size: medium; font-weight:100; font-family: sans-serif; color: #b91d19; padding: 0 0 0 20px;"><b>* All changes must be saved before clicking any action buttons.</b></label>
                                        <br>
                                        <input type="button"
                                               value="Resubmit"
                                               onclick="resubmit_project({{$project->id}})"
                                               style="font-size: medium;"
                                               class="btn btn-lg btn-resubmit submit"/>
                                    <?php } ?>
                                    <?php if ($status == 'active') { ?>
                                        <button class="btn btn-lg btn-create" style="font-size: medium;">{{ empty($project) ? __('Create') : __('Save') }}</button>
                                    <?php } ?>

                                </div>
                            </div>
                        </form>

                    @if(!empty($tasks))

                        <?php foreach ($tasks as $task): ?>

                            <div class="card assets_existing">
                                <?php

                                if($task->status == 'action_requested'){
                                    $task_status = 'in_progress';
                                    $left_border_color = '#fbd102';
                                }else if($task->status == 'in_progress'){
                                    $task_status = 'in_progress';
                                    $left_border_color = '#fbd102';
                                }else if($task->status == 'action_review'){
                                    $task_status = 'in_progress';
                                    $left_border_color = '#fbd102';
                                }else if($task->status == 'action_completed'){
                                    $task_status = 'action_completed';
                                    $left_border_color = '#7e7e7e';
                                }else if($task->status == 'action_skip'){
                                    $task_status = 'Skipped';
                                    $left_border_color = 'white';
                                }else {
                                    $task_status = 'TBD';
                                    $left_border_color = 'white';
                                }

                                ?>
                                <div class="clearfix" id="{{$task->id}}" style="border-left: 10px solid {{ $left_border_color }}; border-radius: 20px;">

                                    <div class="asset--grid-row">

                                        <div class="project-info">
                                            <ul class="project-info-list">
                                                <?php
                                                    $task_type = $task->type;
                                                    if($task_type == 'qc_request'){
                                                        $task_type = 'qa_request';
                                                    }elseif ($task_type == 'qra_request'){
                                                        $task_type = 'ra_request';
                                                    }elseif ($task_type == 'pe_request'){
                                                        $task_type = 'display_&_pe_request';
                                                    }
                                                ?>
                                                <li><strong style="font-size: medium;">{{ strtoupper(ucwords(str_replace('_', ' ', $task_type))) }} #{{ $task->id }}</strong> </li>
                                                <?php if ($left_border_color == 'white') $left_border_color = '#6c757d'; ?>
                                                <li><strong>Status: </strong> <b style="color: {{ $left_border_color }};">{{ ucwords(str_replace('_', ' ', $task_status)) }}</b></li>
                                            </ul>
                                        </div>

                                        <ul class="project-members-list" style="padding-left: 70px;">

                                            <li><strong>Creator : </strong> <span class="asset-creator-bg">{{ $task->author_name }}</span></li>
{{--                                            <li><strong>Copy :</strong>--}}
{{--                                                <span class="copy-writer-bg">asdfsa</span>--}}
{{--                                            </li>--}}
                                        </ul>

                                        <?php if($task->type == 'product_information'){ ?>
                                        <div class="col-md-12">
                                            <div class="project-action-icons">
                                                <ul class="project-icons">
                                                    <li>
                                                        <a  href="javascript:void(0);"
                                                        class="close"
                                                        data-id=""
                                                        data-task-id="{{ $task->id }}"
                                                        data-task-type="{{ $task->type }}"
                                                        onclick="delete_task($(this));">
                                                        <i class="fa fa-times"></i>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <?php } ?>

                                        <?php if($task->status == 'action_skip'){ ?>
                                        <div class="col-md-12">
                                            <div class="project-action-icons">
                                                <ul class="project-icons">
                                                    <li>
                                                        <a  href="javascript:void(0);"
                                                            class="close"
                                                            data-id=""
                                                            data-task-id="{{ $task->id }}"
                                                            data-task-type="{{ $task->type }}"
                                                            onclick="remove_skipped_task($(this));">
                                                            <i class="fa fa-undo"></i>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <?php } ?>

                                    </div>

                                    <?php if($task->status != 'action_skip'){ ?>
                                    <div style="text-align: center;">
                                        <i id="arrow-{{$task->id}}" class="dropdown fa-lg fa fa-angle-down" onclick="click_arrow(this, {{$task->id}})"></i>
                                    </div>
                                    <?php } ?>

                                    <div id="asset-id-{{$task->id}}" class="box-body form_creator" data-asset-id="{{ $task->id }}" style="display: none">
                                        <section>
                                            <div class="inner_box">
                                                <?php if($task->status != 'action_skip'){ ?>
                                                <?php $data = [$task->detail, $task->files, $task->status, $task->ra_detail, $task->legal_detail, $task->design_detail, $task->pe_detail, $task->mm_detail, $task->planner_detail]; ?>
                                                @include('admin.project.task.'.$task->type, $data)
                                                <?php } ?>
                                            </div>
                                        </section>
                                    </div>

                                </div>
                            </div>

                        <?php endforeach; ?>

                    @endif

                    @if(!empty($project))

                        <div class="clearfix" id="asset_selector" style="display: block;">
                        <div class="card box asset box-primary">
                            <?php if($project->status == 'active'){ ?>
                            <div class="card-header" id="task_select_setup" style="height: 500px;">

                                <div class="row" style="display: flex; justify-content: center; padding: 5px 0 0 0;">
                                    <div id="add_task_btn">
                                        <a class="btn-lg btn-secondary add-row" style="color: #050000; background-color: #cdd3d8; border-radius: 1.25rem;" onclick="click_task_add_btn()">INITIATE A NEW REQUEST</a>
                                    </div>
                                </div>

                                <div id="task_new" class="box-body form_creator" data-asset-id="" style="display: none;">
                                    <section>
                                        <div class="inner_box">
                                            <div class="form-group">
                                                <label style="color: #000000; font-size: 1.2rem;">Request Type: </label>
                                                <span id="task_type_name" class="task_type_name"></span>
                                                <span class="float-right">
                                                    <a href="{{ url('admin/project/'. $project->id .'/edit') }}">
                                                        <i class="fa fa-times" style="font-size: 1.5rem;"></i>
                                                    </a>
                                                </span>

                                                <select name="add_task_type" id="add_task_type"
                                                        class="form-control form-select form-select-lg mb-3"
                                                        onchange="change_task_type()">
                                                    <option value="">Select</option>
                                                    @foreach ($inactive_task_list as $key => $val)
                                                        <option value="{{$key}}">{{$val}}</option>
                                                    @endforeach
                                                </select>

                                            </div>

                                            <div id="new_mm_request" style="display: none;">
                                                @include('admin.project.task.new.mm_request')
                                            </div>
                                            <div id="new_npd_planner_request" style="display: none;">
                                                @include('admin.project.task.new.npd_planner_request')
                                            </div>
                                            <div id="new_legal_request" style="display: none;">
                                                @include('admin.project.task.new.legal_request')
                                            </div>
                                            <div id="new_ra_request" style="display: none;">
                                                @include('admin.project.task.new.ra_request')
                                            </div>
                                            <div id="new_npd_po_request" style="display: none;">
                                                @include('admin.project.task.new.npd_po_request')
                                            </div>
                                            <div id="new_npd_design_request" style="display: none;">
                                                @include('admin.project.task.new.npd_design_request')
                                            </div>
                                            <div id="new_pe_request" style="display: none;">
                                                @include('admin.project.task.new.pe_request')
                                            </div>
                                            <div id="new_qc_request" style="display: none;">
                                                @include('admin.project.task.new.qc_request')
                                            </div>
                                            <div id="new_product_information" style="display: none;">
                                                @include('admin.project.task.new.product_information')
                                            </div>

                                        </div>
                                    </section>
                                </div>

                            </div>
                            <?php } ?>
                        </div>
                    </div>
                    @endif

                </div>

                @if(!empty($project))
                <div class="col-lg-5">
                    <div class="card">
                        <div class="card-header">
                            <h4>CORRESPONDENCE</h4>
                            <div class=" text-right">
                                <button class="btn btn-primary" id="add_note_btn" onclick="click_add_note_btn()">Add Note</button>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="col">
                                <section id="add_note" class="notes" style="display: none;">

                                    <div class="write note">
                                        <form method="POST" action="{{ route('project.project_add_note') }}" enctype="multipart/form-data">
                                            @csrf
{{--                                            <select name="exist_assets" id="exist_assets" style="font-size: large; width: 50%; background-color: #c4c4c4" onchange="select_note_asset()">--}}
{{--                                                <option value="">Select Asset</option>--}}
{{--                                                <?php foreach ($assets as $asset): ?>--}}
{{--                                                <option value="{{ucwords(str_replace('_', ' ', $asset->a_type))}} #{{ $asset->a_id }}">{{ucwords(str_replace('_', ' ', $asset->a_type))}} #{{ $asset->a_id }}</option>--}}
{{--                                                <?php endforeach; ?>--}}
{{--                                            </select>--}}
                                            <input type="hidden" name="p_id" value="{{ $project->id }}">
                                            <input type="hidden" id="email_list" name="email_list" value="">
                                            <input type="hidden" name="p_title" value="{{ $project->name }}">

                                            <textarea id="create_note" name="create_note" class="wysiwyg"></textarea>
                                            <div id="at_box" style="display: none">
                                                <input class="form-control" onkeydown="return event.key !== 'Enter';" placeholder="Name" type="text"/>
                                            </div>
                                            <div class=" text-right">
                                                <button type="button" class="btn btn-primary" onclick="click_cancel_note_btn()">Cancel</button>
                                                <button type="submit" class="btn btn-primary">Send Note</button>
                                            </div>
                                        </form>
                                    </div>
                                </section>

                                <div class="form-group">
                                    @foreach ($correspondences as $correspondence)

                                        <?php if(!empty($correspondence->users)) { ?>
                                            <?php $role = $correspondence->users->role ?>
                                            <?php $team = $correspondence->users->team ?>
                                            <?php $first_name = $correspondence->users->first_name . ' ' . $correspondence->users->last_name ?>
                                        <?php }else{  ?>
                                        <?php $role = '-' ?>
                                        <?php $team = '-' ?>
                                        <?php $first_name = 'Not Exist User' ?>
                                        <?php } ?>

                                        <?php $color_role = strtolower(add_underscores($role)); ?>
                                        <div class="note">
                                            <ul class="list-unstyled list-unstyled-border list-unstyled-noborder">
                                                <li class="media">
                                                    <div class="media-body">
                                                        <div class="media-title-note {{$color_role}}" >
                                                            <div class="media-right"><div class="text-time">{{ date('m/d/y g:i A', strtotime($correspondence->created_at)) }}</div></div>
                                                            <div class="media-title mb-1">{{ $first_name }}</div>
                                                            <div class="text-time">{{ $team }} | {{ $role }}</div>
                                                        </div>
                                                        <div class="media-description text-muted" style="padding: 15px;">
                                                            {!! $correspondence->note !!}
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>

                                    @endforeach

                                </div>

                            </div>
                        </div>
                    </div>
                </div>


                    <div class="modal fade"
                         id="revision_reason_project_{{$project->id}}"
                         tabindex="-1"
                         data-backdrop="false"
                         role="dialog"
                         aria-labelledby="myModalLabel">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <form method="POST" action="{{ route('project.revision_reason') }}" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="project_id" value="{{$project->id}}">
                                    <div class="modal-header">
                                        <h4 class="modal-title" id="myModalLabel">Revision Reason</h4>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <h6><b>Select the appropriate reason and provide any additional details in the note before saving.</b></h6>
                                            <div class="columns" style="column-count: 1;">
                                                @foreach($revision_reason_list as $val)
                                                    <div class="col-md">
                                                        <div class="form-check">
                                                            <input  type="radio" name="revision_reason" required
                                                                    id="{{$val}}" value="{{$val}}"
                                                            >
                                                            <label class="form-check-label " for="{{ $val }}">
                                                                {{ $val}}
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <label>Note:</label>
                                            <textarea class="form-control" id="revision_reason_note" name="revision_reason_note" style="height: 100px;"></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-dark" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-revision">Save Revision Reason</button>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>


                @endif

            </div>
        </div>

    </section>


    <script type="text/javascript">
        const queryString = window.location.href;
        if(queryString.includes('#')) {
            var asset_id = queryString.split('#').pop();
            $('#asset-id-'+asset_id).show();
            $('#arrow-'+asset_id).removeClass('fa-angle-down');
            $('#arrow-'+asset_id).addClass('fa-angle-up');
        }
    </script>

    <script>

        function check_retailer(){
            if($('#project_brand').val() == 10){
                $(".retailer_box").show();
            }else{
                $(".retailer_box").hide();
                $("#retailer").val("");
            }
        }

        function click_task_add_btn(){

            $("#add_task_btn").hide();
            $("#task_new").show();

        }

        function click_add_note_btn(){
            $("#add_note_btn").hide();
            $("#add_note").slideDown();

        }

        function click_cancel_note_btn(){
            $("#add_note_btn").show();
            $("#add_note").slideUp();
        }

        function select_note_asset(){
            tinymce.get("create_note").execCommand('mceInsertContent', false, $("#exist_assets").val());
        }


        function change_task_type(){

            add_task_type = $('#add_task_type option:selected').val();

            if(add_task_type == 'mm_request'){
                $("#task_type_name").text('MM REQUEST');
                $("#new_mm_request").show();
                $("#new_npd_planner_request").hide();
                $("#new_legal_request").hide();
                $("#new_ra_request").hide();
                $("#new_npd_po_request").hide();
                $("#new_npd_design_request").hide();
                $("#new_pe_request").hide();
                $("#new_qc_request").hide();
                $("#new_product_information").hide();
            }
            if(add_task_type == 'npd_planner_request'){
                $("#task_type_name").text('NPD Planner Request');
                $("#new_mm_request").hide();
                $("#new_npd_planner_request").show();
                $("#new_legal_request").hide();
                $("#new_ra_request").hide();
                $("#new_npd_po_request").hide();
                $("#new_npd_design_request").hide();
                $("#new_pe_request").hide();
                $("#new_qc_request").hide();
                $("#new_product_information").hide();
            }
            if(add_task_type == 'legal_request'){
                $("#task_type_name").text('LEGAL REQUEST');
                $("#new_mm_request").hide();
                $("#new_npd_planner_request").hide();
                $("#new_legal_request").show();
                $("#new_ra_request").hide();
                $("#new_npd_po_request").hide();
                $("#new_npd_design_request").hide();
                $("#new_pe_request").hide();
                $("#new_qc_request").hide();
                $("#new_product_information").hide();
            }
            if(add_task_type == 'ra_request'){
                $("#task_type_name").text('RA Request');
                $("#new_mm_request").hide();
                $("#new_npd_planner_request").hide();
                $("#new_legal_request").hide();
                $("#new_ra_request").show();
                $("#new_npd_po_request").hide();
                $("#new_npd_design_request").hide();
                $("#new_pe_request").hide();
                $("#new_qc_request").hide();
                $("#new_product_information").hide();
            }
            if(add_task_type == 'npd_po_request'){
                $("#task_type_name").text('NPD PO Request');
                $("#new_mm_request").hide();
                $("#new_npd_planner_request").hide();
                $("#new_legal_request").hide();
                $("#new_ra_request").hide();
                $("#new_npd_po_request").show();
                $("#new_npd_design_request").hide();
                $("#new_pe_request").hide();
                $("#new_qc_request").hide();
                $("#new_product_information").hide();
            }
            if(add_task_type == 'npd_design_request'){
                $("#task_type_name").text('NPD Design Request');
                $("#new_mm_request").hide();
                $("#new_npd_planner_request").hide();
                $("#new_legal_request").hide();
                $("#new_ra_request").hide();
                $("#new_npd_po_request").hide();
                $("#new_npd_design_request").show();
                $("#new_pe_request").hide();
                $("#new_qc_request").hide();
                $("#new_product_information").hide();
            }
            if(add_task_type == 'pe_request'){
                $("#task_type_name").text('DISPLAY & PE REQUEST');
                $("#new_mm_request").hide();
                $("#new_npd_planner_request").hide();
                $("#new_legal_request").hide();
                $("#new_ra_request").hide();
                $("#new_npd_po_request").hide();
                $("#new_npd_design_request").hide();
                $("#new_pe_request").show();
                $("#new_qc_request").hide();
                $("#new_product_information").hide();
            }
            if(add_task_type == 'qc_request'){
                $("#task_type_name").text('QA REQUEST');
                $("#new_mm_request").hide();
                $("#new_npd_planner_request").hide();
                $("#new_legal_request").hide();
                $("#new_ra_request").hide();
                $("#new_npd_po_request").hide();
                $("#new_npd_design_request").hide();
                $("#new_pe_request").hide();
                $("#new_qc_request").show();
                $("#new_product_information").hide();
            }
            if(add_task_type == 'product_information'){

                $("#task_type_name").text('PRODUCT INFORMATION');
                $("#new_mm_request").hide();
                $("#new_npd_planner_request").hide();
                $("#new_legal_request").hide();
                $("#new_ra_request").hide();
                $("#new_npd_po_request").hide();
                $("#new_npd_design_request").hide();
                $("#new_pe_request").hide();
                $("#new_qc_request").hide();
                $("#new_product_information").show();
                $("#task_select_setup").height('');
            }

        }

        function click_arrow(el, asset_id){
            // alert("hi");
            if($(el).hasClass('fa-angle-up')){
                $(el).toggleClass('with-border');
                $(el).removeClass('fa-angle-up');
                $(el).addClass('fa-angle-down');
                $('#asset-id-'+asset_id).slideUp();
            }else{
                $(el).removeClass('fa-angle-down');
                $(el).addClass('fa-angle-up');
                $('#asset-id-'+asset_id).slideDown();
            }
        }

        function delete_task(el) {
            if (confirm("Are you sure to Delete? Are you sure you want to delete? Once deleted, this action cannot be undone and the data will no longer be accessible.") == true) {
                let id = $(el).attr('data-task-id');
                let type = $(el).attr('data-task-type');
                $.ajax({
                    url: "<?php echo url('/admin/project/taskRemove'); ?>"+"/"+id+"/"+type,
                    type: "GET",
                    datatype: "json",
                    success: function(response) {
                        if(response != 'fail'){
                            alert('The request has been successfully deleted.');
                            $(el).parent().parent().parent().parent().parent().parent().fadeOut( "slow", function() {
                                $(el).parent().parent().parent().parent().parent().parent().remove();
                            });
                            window.location.reload(response);
                        }else{
                            alert('You do not have permission to remove this request');
                        }
                    },
                })
            }
        }

        function remove_skipped_task(el) {
            if (confirm("Are you sure you want to cancel this skipped request?") == true) {
                let id = $(el).attr('data-task-id');
                let type = $(el).attr('data-task-type');
                $.ajax({
                    url: "<?php echo url('/admin/project/taskRemove'); ?>"+"/"+id+"/"+type,
                    type: "GET",
                    datatype: "json",
                    success: function(response) {
                        if(response != 'fail'){
                            alert('The request has been successfully canceled.');
                            $(el).parent().parent().parent().parent().parent().parent().fadeOut( "slow", function() {
                                $(el).parent().parent().parent().parent().parent().parent().remove();
                            });
                            window.location.reload(response);
                        }else{
                            alert('You do not have permission to remove this request');
                        }
                    },
                })
            }
        }


        function another_upload(el) {
            upload_box = $('.p_attachment').prop('outerHTML');
            upload_name = $(el).prev().attr('name');
            upload_id = $(el).prev().attr('data-asset');
            $('.p_attachment').removeClass('last_upload');
            $(el).before(upload_box);
            $(el).prev().attr('name', upload_name);
        }

        function remove_file(el) {
            if (confirm("Are you sure to Delete File?") == true) {
                let id = $(el).attr('data-attachment-id');
                $.ajax({
                    url: "<?php echo url('/admin/project/fileRemove'); ?>"+"/"+id,
                    type: "GET",
                    datatype: "json",
                    success: function(response) {
                        if(response == 'success'){
                            $(el).parent().remove();
                        }else{
                            alert(response);
                        }
                    },
                })
            }
        }

        function open_download(link) {
            let click_link = document.createElement('a');
            click_link.href = link;
            image_arr = link.split('/');
            link = image_arr[image_arr.length-1];
            click_link.download = link;
            document.body.appendChild(click_link);
            click_link.click();
        }

        function copy_requested_toggle(el) {
            box = $(el).prev();
            if ($(el).prop('checked') == true) {
                if(box.is("div")){ // for editor
                    box.children('.note-editing-area').children('.note-editable').text('Requested');
                    box.prev().val('Requested');
                }
                if(box.is("input")){
                    box.attr('readonly', 'readonly');
                    box.attr('value', 'Requested');
                    box.val('Requested');
                }
                if(box.is("textarea")){
                    box.val('Requested');
                    box.attr('readonly', 'readonly');
                }
            } else {
                if(box.is("div")){ // for editor
                    box.children('.note-editing-area').children('.note-editable').text('');
                    box.prev().val('');
                }
                if(box.is("input")){ // for input, textarea
                    box.removeAttr('readonly');
                    box.attr('value', '');
                    box.val('');
                }
                if(box.is("textarea")){
                    box.removeAttr('readonly');
                    box.val('');
                }
            }
        }

        function send_archive_project(project_id){

            if (confirm("Are you sure to Send Archive?") == true) {

                $.ajax({
                    url: "<?php echo url('/admin/project/send_archive'); ?>"+"/"+project_id,
                    type: "GET",
                    datatype: "json",
                    success: function(response) {
                        if(response != 'fail'){
                            alert("Success. This Project moved to Archives Folder.")
                            window.location.reload(response);
                        }else{
                            alert('Error!');
                        }
                    },
                })
            }
        }

        function send_active_project(project_id){

            if (confirm("Are you sure to Send Active Back?") == true) {

                $.ajax({
                    url: "<?php echo url('/admin/project/send_active'); ?>"+"/"+project_id,
                    type: "GET",
                    datatype: "json",
                    success: function(response) {
                        if(response != 'fail'){
                            alert("Success. This Project moved to Active Folder.")
                            window.location.reload(response);
                        }else{
                            alert('Error!');
                        }
                    },
                })
            }
        }



        function change_to_copy_complete(el){
            if (confirm("Are you sure to Copy Completed?") == true) {
                let id = $(el).attr('data-asset-id');
                $.ajax({
                    url: "<?php echo url('/admin/asset/copyComplete'); ?>"+"/"+id,
                    type: "GET",
                    datatype: "json",
                    success: function(response) {
                        if(response != 'fail'){
                            alert('Success!');
                            window.location.reload(response);
                            $(el).remove();
                        }else{
                            alert('Error!');
                        }
                    },
                })
            }
        }

        function action_start(el){
            if (confirm("Your request status will change to 'In Progress'") == true) {
                let id = $(el).attr('data-task-id');
                $.ajax({
                    url: "<?php echo url('/admin/task/actionInProgress'); ?>"+"/"+id,
                    type: "GET",
                    datatype: "json",
                    success: function(response) {
                        if(response != 'fail'){
                            alert('You have successfully changed the status to "In Progress".');
                            window.location.reload(response);
                            $(el).remove();
                        }else{
                            alert('Error!');
                        }
                    },
                })
            }
        }

        function action_review(el){
            if (confirm("The work has been updated for review, and the status will now change to 'Action Review'.") == true) {
                let id = $(el).attr('data-task-id');
                $.ajax({
                    url: "<?php echo url('/admin/task/actionReview'); ?>"+"/"+id,
                    type: "GET",
                    datatype: "json",
                    success: function(response) {
                        if(response != 'fail'){
                            alert('You have successfully changed the status to "Action Review".');
                            window.location.reload(response);
                            $(el).remove();
                        }else{
                            alert('Error!');
                        }
                    },
                })
            }
        }

        function action_complete(el){
            if (confirm("Please confirm that all the information is final. Once you click 'Complete' no further changes can be made.") == true) {
                let id = $(el).attr('data-task-id');
                $.ajax({
                    url: "<?php echo url('/admin/task/actionComplete'); ?>"+"/"+id,
                    type: "GET",
                    datatype: "json",
                    success: function(response) {
                        if(response != 'fail'){
                            alert("You have successfully changed the status to 'Completed'.");
                            window.location.reload(response);
                            $(el).remove();
                        }else{
                            alert('Error!');
                        }
                    },
                })
            }
        }

        function action_skip(el){
            if (confirm("Are you sure you want to skip the request? It cannot be recovered once skipped.") == true) {
                let id = $(el).attr('data-project-id');
                let type = $(el).attr('data-task-type');
                $.ajax({
                    url: "<?php echo url('/admin/task/actionSkip'); ?>"+"/"+id+"/"+type,
                    type: "GET",
                    datatype: "json",
                    success: function(response) {
                        if(response != 'fail'){
                            alert('You have successfully skipped the task, and it has now been removed from the task list.');
                            window.location.reload(response);
                            $(el).remove();
                        }else{
                            alert('Error!');
                        }
                    },

                })
            }
        }

        function approve_project(project_id){
            if (confirm("Are you sure you want to approve the project to start?") == true) {
                $.ajax({
                    url: "<?php echo url('/admin/project/approve_project'); ?>"+"/"+project_id,
                    type: "GET",
                    datatype: "json",
                    success: function(response) {
                        if(response != 'fail'){
                            alert("Success! This project has been moved to the Active List.")
                            window.location.reload(response);
                        }else{
                            alert('Error!');
                        }
                    },
                })
            }
        }

        function resubmit_project(project_id){
            if (confirm("Ensure all changes are saved before resubmitting for approval.") == true) {
                $.ajax({
                    url: "<?php echo url('/admin/project/resubmit_project'); ?>"+"/"+project_id,
                    type: "GET",
                    datatype: "json",
                    success: function(response) {
                        if(response != 'fail'){
                            alert("Success! This project has been moved to the Approval List.")
                            window.location.reload(response);
                        }else{
                            alert('Error!');
                        }
                    },
                })
            }
        }

    </script>

    <script type="text/javascript">

        tinymce.init({
            selector: '.wysiwyg',
            license_key: 'gpl',
            placeholder: 'If you would like to notify a specific person, type @ and enter the persons name in the field that appears. ',
            menubar: false,
            statusbar: false,
            plugins: [],
            toolbar: 'undo redo | insert | styles | bold italic ',
            paste_as_text: true,
            init_instance_callback: function (editor) {
                editor.on('keypress', function (e) {
                    if (e.key == '@' && editor.id == 'create_note') {
                        $("#at_box").show();
                        $("#at_box input").attr('readonly', false);
                        $("#at_box input").focus();
                    }
                });
            }
        });

        arr = <?php echo json_encode($kiss_users); ?>;
        console.log(arr);

        total = [];
        $.each(arr, function(k,v) {
            total.push(k);
        });

        var email_list=[];

        $("#at_box input").autocomplete({
            source: total,
            minLength: 0,
            select: function(event, ui) {
                $.each(arr, function(k,v) {
                    if (k == ui.item.label) {
                        email = arr[k];
                        email_list.push(email);
                        name = '@' + arr[k].split('@')[0];
                        tinymce.get("create_note").execCommand('mceInsertContent', false, name);
                        $('#email_list').val(email_list);
                        $('#at_box input').val('');
                        $('#at_box').hide();
                    }
                })
                return false;
            },
            messages: {
                noResults: '',
                results: function() {}
            }
        });

    </script>

@endsection
