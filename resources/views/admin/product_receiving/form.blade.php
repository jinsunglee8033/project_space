@extends('layouts.dashboard')

@section('content')


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
            padding: 20px 0px;
            margin: -14px 0 30px 0;
            --copy-writer-bg: var(--light-steal-blue);
            --asset-creator-bg: var(--prim);
            --assignee-bg: var(--hosta-flower);
            display: grid;
            gap: 100px;
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
            <h1>Product Receiving</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ url('admin/project') }}">Product Receiving</a></div>
                <div class="breadcrumb-item active">Product Receiving</div>
            </div>
        </div>

        <div class="section-body">
            <h2 class="section-title">Product Receiving</h2>
            <div class="row">
                <div class="col-lg-7">
                    <div class="card">
                        <div class="card-header">
                            <h4>Project #{{$project->id}}</h4>
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
                                    <label>Team</label>
                                    <select name="team" disabled class="form-control" >
                                        <option value="{{ $team }}" selected>
                                            {{ $team }}
                                        </option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Brand</label>
                                    <select id="secondSelect" name="brand" disabled class="form-control" >
                                        <option value="{{ $brand }}" selected>
                                            {{ $brand }}
                                        </option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Project Name: </label>
                                    <input type="text" name="name"
                                           class="form-control"
                                           value="{{$project->name}}" disabled>
                                </div>

                                <div class="form-group">
                                    <label>Description: </label>
                                    {!! Form::textarea('description', $project->description, ['class' => 'form-control summernote']) !!}
                                </div>

                                <div class="form-group">
                                    <label>Project Type</label>
                                    <select class="form-control" name="project_type" disabled>
                                        <option value="">Select</option>
                                        @foreach ($project_types as $value)
                                            <option value="{{ $value }}" {{ $value == $project_type ? 'selected' : '' }}>
                                                {{ $value }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Launch Date: </label>
                                            <input type="text" name="launch_date" id="launch_date"
                                                   disabled class="form-control" value="{{ $project->launch_date }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Launch Date (LA): </label>
                                            <input type="text" name="target_date" id="target_date"
                                                   disabled class="form-control" value="{{ $project->target_date }}">
                                        </div>
                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>

                    @if(!empty($product_receiving_list))

                        <?php foreach ($product_receiving_list as $task): ?>

                        <?php
                            if($task->status == 'action_requested'){
                                $status_color = '#28A745';
                            }else if($task->status == 'in_progress'){
                                $status_color = '#fbd102';
                            }else if($task->status == 'action_review'){
                                $status_color = '#F03C3C';
                            }else if($task->status == 'action_completed'){
                                $status_color = '#7e7e7e';
                            }else {
                                $status_color = 'white';
                            }
                        ?>

                            <?php $data = [$task->detail, $task->files]; ?>
                            <?php $task_id = $data[0][0]->task_id; $t_type = $data[0][0]->type; $t_author = $data[0][0]->author_id; ?>

                            <div class="clearfix card" id="{{ $task->detail[0]->task_id }}" style="border-left: 10px solid {{$status_color}}; border-radius: 20px;">
                                <div class="box-body form_creator">
                                    <section>
                                        <div class="inner_box">

                                            <div class="asset--grid-row">

                                                <div class="project-info">
                                                    <ul class="project-info-list">
                                                        <li><strong>Product Receiving #{{$task_id}}</strong> </li>
                                                        <li><strong>Status: </strong> <b style="color: #6c757d;">{{ strtoupper(str_replace('_', ' ', $task->status))}}</b></li>
                                                        <li><strong>Created: </strong> {{ date('m-d-Y', strtotime($task->created_at)) }}</li>
                                                    </ul>
                                                </div>

                                                <ul class="project-members-list">

                                                    <li><strong>Creator : </strong> <span class="asset-creator-bg">{{ $task->first_name }} {{ $task->last_name }}</span></li>
{{--                                                    <li><strong>Copy :</strong></li>--}}
{{--                                                    <li><strong>Assignee : </strong></li>--}}
                                                </ul>

                                                <div class="col-md-12">
                                                    <div class="project-action-icons">
{{--                                                        <ul class="project-icons">--}}
{{--                                                            <li>--}}
{{--                                                                <i class="fa fa-spin fa-cog" data-toggle="modal" data-target="#asset-owner-8712"></i>--}}
{{--                                                            </li>--}}
{{--                                                            <li>--}}
{{--                                                                <i class="fa fa-address-card" data-toggle="modal" data-target="#myModal-8712"></i>--}}
{{--                                                            </li>--}}



{{--                                                            <li>--}}
{{--                                                                <a href="javascript:void(0);" class="close" data-id="" data-asset-id="8712" data-asset-type="website_banners" onclick="delete_asset($(this));">--}}
{{--                                                                    <i class="fa fa-times"></i>--}}
{{--                                                                </a>--}}
{{--                                                            </li>--}}
{{--                                                        </ul>--}}
                                                    </div>
                                                </div>
                                            </div>

                                            <?php if (!empty($task_status) && $task_status == 'action_requested') { ?>
                                            <?php if(auth()->user()->role == 'Admin' || auth()->user()->team == 'QM QC') { ?>

                                            <div style="margin: 0 0 10px 10px; padding: 0px 0 30px 0;">
                                                <input type="button"
                                                       name="action start"
                                                       value="Start"
                                                       onclick="action_start($(this))"
                                                       data-task-id="<?php echo $task_id; ?>"
                                                       style="margin-top:-10px; float:left; font-size: medium;"
                                                       class="btn btn-lg btn-info submit"/>
                                                <label style="font-size: medium; font-weight:100; color: #b91d19; padding: 0 0 0 20px;"><b>* Click to indicate the start of work.</b></label>
                                            </div>

                                            <?php } ?>
                                            <?php }?>

                                            <form method="POST" action="{{ route('product_receiving.edit_product_receiving', $task_id) }}" enctype="multipart/form-data">
                                                @csrf

                                                <div class="form-group">
                                                    <label>PO# :</label>
                                                    <input type="text" name="po" class="form-control" value="<?php echo $data[0][0]->po; ?>">
                                                </div>

                                                <div class="form-group">
                                                    <label>Materials# :</label>
                                                    <input type="text" name="materials" class="form-control" value="<?php echo $data[0][0]->materials; ?>" >
                                                </div>

                                                <div class="form-group">
                                                    <label>Posting Date :</label>
                                                    <input  type="text" name="posting_date" id="{{$task_id}}_posting_date" placeholder="Posting Date"
                                                            class="form-control @error('posting_date') is-invalid @enderror @if (!$errors->has('posting_date') && old('posting_date')) is-valid @endif"
                                                            value="{{ old('posting_date', !empty($data[0][0]) ? $data[0][0]->posting_date : null) }}">
                                                </div>

                                                <div class="form-group">
                                                    <label>QIR Status:</label>
                                                    <select class="form-control" name="qir_status">
                                                        <option value="">Select</option>
                                                        @foreach ($qir_statuses as $val)
                                                            <option value="{{ $val }}" {{ $val == $data[0][0]->qir_status ? 'selected' : '' }}>
                                                                {{ $val }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label>Division:</label>
                                                    <select class="form-control" name="division">
                                                        <option value="">Select</option>
                                                        @foreach($divisions as $val)
                                                            <option value="{{ $val }}" {{ $val == $data[0][0]->division ? 'selected' : '' }}>
                                                                {{ $val }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label>QIR Action:</label>
                                                    <select class="form-control" name="qir_action">
                                                        <option value="">Select</option>
                                                        @foreach($qir_actions as $val)
                                                            <option value="{{ $val }}" {{ $val == $data[0][0]->qir_action ? 'selected' : '' }}>
                                                                {{ $val }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label>Vendor Code:</label>
                                                    <input type="text" name="vendor_code" class="form-control" id="vendor_auto" value="<?php echo $data[0][0]->vendor_code; ?>" >
                                                </div>

                                                <div class="form-group">
                                                    <label>Vendor Name:</label>
                                                    <input type="text" name="vendor_name" class="form-control" id="vendor_name" readonly value="<?php echo $data[0][0]->vendor_name; ?>">
                                                </div>

                                                <div class="form-group">
                                                    <label>Cost Center:</label>
                                                    <select class="form-control" name="cost_center">
                                                        <option value="">Select</option>
                                                        @foreach($cost_center_list as $val)
                                                            <option value="{{ $val }}" {{ $val == $data[0][0]->cost_center ? 'selected' : '' }}>
                                                                {{ $val }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label>Location:</label>
                                                    <select class="form-control" name="location">
                                                        <option value="">Select</option>
                                                        @foreach($locations as $val)
                                                            <option value="{{ $val }}" {{ $val == $data[0][0]->location ? 'selected' : '' }}>
                                                                {{ $val }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label>Primary Contact:</label>
                                                    <input type="text" name="primary_contact" class="form-control" value="<?php echo $data[0][0]->primary_contact; ?>" >
                                                </div>

                                                <div class="form-group">
                                                    <label>Related Team Contact:</label>
                                                    <input type="text" name="related_team_contact" class="form-control" value="<?php echo $data[0][0]->related_team_contact; ?>" >
                                                </div>

                                                <div class="form-group">
                                                    <label>Year:</label>
                                                    <input type="text" name="year" class="form-control" value="<?php echo $data[0][0]->year; ?>" >
                                                </div>

                                                <div class="form-group">
                                                    <label>Received QTY:</label>
                                                    <input type="text" name="received_qty" class="form-control" value="<?php echo $data[0][0]->received_qty; ?>" >
                                                </div>

                                                <div class="form-group">
                                                    <label>Inspection QTY:</label>
                                                    <input type="text" name="inspection_qty" id="inspection_qty" onkeyup="blocked_rate_cal();" class="form-control" value="<?php echo $data[0][0]->received_qty; ?>" >
                                                </div>

                                                <div class="form-group">
                                                    <label>Defect QTY:</label>
                                                    <input type="text" name="defect_qty" id="defect_qty" onkeyup="blocked_rate_cal();" class="form-control" value="<?php echo $data[0][0]->defect_qty; ?>" >
                                                </div>

                                                <div class="form-group">
                                                    <label>Blocked Rate: (Defect QTY / Inspection QTY %)</label>
                                                    <input type="text" readonly name="blocked_rate" id="blocked_rate" class="form-control" value="<?php echo $data[0][0]->blocked_rate; ?>">
                                                </div>

                                                <div class="form-group">
                                                    <label>Blocked QTY:</label>
                                                    <input type="text" name="blocked_qty" class="form-control" value="<?php echo $data[0][0]->blocked_qty; ?>" >
                                                </div>

                                                <div class="form-group">
                                                    <label>Item Net Cost ($):</label>
                                                    <input type="text" name="item_net_cost" id="item_net_cost" onkeyup="defect_cost_cal();" class="form-control" value="<?php echo $data[0][0]->item_net_cost; ?>" >
                                                </div>

                                                <div class="form-group">
                                                    <label>Defect Cost ($): (Defect QTY * Item Next Cost)</label>
                                                    <input type="text" readonly name="defect_cost" id="defect_cost" class="form-control" value="<?php echo $data[0][0]->defect_cost; ?>" >
                                                </div>

                                                <div class="form-group">
                                                    <label>Full Cost ($): </label>
                                                    <input type="text" name="full_cost" id="full_cost" onkeyup="total_claim_cal();" class="form-control" value="<?php echo $data[0][0]->full_cost; ?>" >
                                                </div>

                                                <div class="form-group">
                                                    <label>Rework Cost ($): </label>
                                                    <input type="text" name="rework_cost" id="rework_cost" onkeyup="total_claim_cal();" class="form-control" value="<?php echo $data[0][0]->rework_cost; ?>" >
                                                </div>

                                                <div class="form-group">
                                                    <label>Special Inspection Cost ($): </label>
                                                    <input type="text" name="special_inspection_cost" id="special_inspection_cost" onkeyup="total_claim_cal();" class="form-control" value="<?php echo $data[0][0]->special_inspection_cost; ?>" >
                                                </div>

                                                    <div class="form-group">
                                                        <label>Total Claim ($): ( Defect Cost + Full Cost + Rework Cost + Special Inspection Cost ) </label>
                                                        <input type="text" readonly name="total_claim" id="total_claim" class="form-control" value="<?php echo $data[0][0]->total_claim; ?>" >
                                                    </div>

                                                    <div class="form-group">
                                                        <label>Batch #:</label>
                                                        <input type="text" name="batch" class="form-control" value="<?php echo $data[0][0]->batch; ?>" >
                                                    </div>

                                                    <div class="form-group">
                                                        <label>Defect Area:</label>
                                                        <div class="columns" style="column-count: 3;">
                                                            <?php $checkbox_fields = explode(', ', $data[0][0]->defect_area); ?>
                                                            <?php foreach($defect_areas as $value): ?>
                                                            <div class="col-md">
                                                                <div class="form-check" style="padding-left: 0px;">
                                                                    <input  type="checkbox"
                                                                            <?php if (in_array($value, $checkbox_fields)) echo "checked" ?>
                                                                            name="defect_area[]"
                                                                            value="{{ $value }}"
                                                                    >
                                                                    <label class="form-check-label " for="{{ $value }}">
                                                                        {{ $value }}
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label>Defect Type:</label>
                                                        <div class="columns" style="column-count: 3;">
                                                            <?php $checkbox_fields = explode(', ', $data[0][0]->defect_type); ?>
                                                            <?php foreach($defect_types as $value): ?>
                                                            <div class="col-md">
                                                                <div class="form-check" style="padding-left: 0px;">
                                                                    <input  type="checkbox"
                                                                            <?php if (in_array($value, $checkbox_fields)) echo "checked" ?>
                                                                            name="defect_type[]"
                                                                            value="{{ $value }}"
                                                                    >
                                                                    <label class="form-check-label " for="{{ $value }}">
                                                                        {{ $value }}
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label>Defect Details:</label>
                                                        {!! Form::textarea('defect_details', !empty($data[0][0]) ? $data[0][0]->defect_details : null, ['class' => 'form-control summernote']) !!}
                                                    </div>

                                                    <div class="form-group">
                                                        <label>RSR ID: </label>
                                                        <input type="text" name="rsr_id" class="form-control" value="<?php echo $data[0][0]->rsr_id; ?>" >
                                                    </div>

                                                    <div class="form-group">
                                                        <label>Processing Date :</label>
                                                        <input  type="text" name="processing_date" id="{{$task_id}}_processing_date" placeholder="Processing Date"
                                                                class="form-control @error('processing_date') is-invalid @enderror @if (!$errors->has('processing_date') && old('processing_date')) is-valid @endif"
                                                                value="{{ old('processing_date', !empty($data[0][0]) ? $data[0][0]->processing_date : null) }}">
                                                    </div>

                                                    <div class="form-group">
                                                        <label>Aging Days: </label>
                                                        <input type="text" name="aging_days" class="form-control" value="<?php echo $data[0][0]->aging_days; ?>" >
                                                    </div>

                                                    <div class="form-group">
                                                        <label>CAPA : </label>
                                                        <input type="checkbox" name="capa" class="form-control" <?php if ($data[0][0]->capa == 'on') echo "checked" ?> style="width: 18%; margin: -28px 0px 0px 10px;">
                                                    </div>

                                                    <div class="form-group">
                                                        <label>Actual CM Total: </label>
                                                        <input type="text" name="actual_cm_total" class="form-control" value="<?php echo $data[0][0]->actual_cm_total; ?>" >
                                                    </div>

                                                    <div class="form-group">
                                                        <label>Claim Status:</label>
                                                        <select class="form-control" name="claim_status">
                                                            <option value="">Select</option>
                                                            @foreach($claim_statuses as $val)
                                                                <option value="{{ $val }}" {{ $val == $data[0][0]->claim_status ? 'selected' : '' }}>
                                                                    {{ $val }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="form-group">
                                                        <label>Override Authorized By:</label>
                                                        <select class="form-control" name="override_authorized_by">
                                                            <option value="">Select</option>
                                                            @foreach($override_authorized_by_list as $val)
                                                                <option value="{{ $val }}" {{ $val == $data[0][0]->override_authorized_by ? 'selected' : '' }}>
                                                                    {{ $val }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="form-group">
                                                        <label>Waived Amount ($): </label>
                                                        <input type="text" name="waived_amount" class="form-control" value="<?php echo $data[0][0]->waived_amount; ?>" >
                                                    </div>

                                                    <div class="form-group">
                                                        <label>Settlement Total ($): </label>
                                                        <input type="text" name="settlement_total" class="form-control" value="<?php echo $data[0][0]->settlement_total; ?>" >
                                                    </div>

                                                    <div class="form-group">
                                                        <label>Settlement Type:</label>
                                                        <select class="form-control" name="settlement_type">
                                                            <option value="">Select</option>
                                                            @foreach($settlement_type_list as $val)
                                                                <option value="{{ $val }}" {{ $val == $data[0][0]->settlement_type ? 'selected' : '' }}>
                                                                    {{ $val }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                <?php if (count($data[1]) > 0): ?>
                                                <div class="form-group">
                                                    <label>Defect Photo / Report:</label>
                                                    <br/>
                                                    <?php foreach ($data[1] as $attachment): ?>
                                                    <?php
                                                    $file_ext = $attachment['file_ext'];
                                                    if(strpos($file_ext, ".") !== false){
                                                        $file_ext = substr($file_ext, 1);
                                                    }
                                                    $not_image = ['pdf','doc','docx','pptx','ppt','mp4','xls','xlsx','csv'];
                                                    $file_icon = '/storage/'.$file_ext.'.png';
                                                    $attachment_link = '/storage' . $attachment['attachment'];
                                                    $open_link = 'open_download';
                                                    ?>
                                                    <div class="attachment_wrapper">
                                                        <?php $name = explode('/', $attachment['attachment']); ?>
                                                        <?php $name = $name[count($name)-1]; ?>
                                                        <?php $date = date('m/d/Y g:ia', strtotime($attachment['date_created'])); ?>
                                                        <div class="attachement">{{ $name }}</div>
                                                        <a onclick="remove_file($(this))"
                                                           class="delete attachement close"
                                                           title="Delete"
                                                           data-file-name="<?php echo $name; ?>"
                                                           data-attachment-id="<?php echo $attachment['attachment_id']; ?>">
                                                            <i class="fa fa-times"></i>
                                                        </a>
                                                        <img title="<?php echo $name . ' (' . date('m/d/Y g:ia', strtotime($date)) . ')'; ?>"
                                                             data-file-date="<?php echo $date; ?>"
                                                             <?php
                                                             if (!in_array($file_ext, $not_image)) {
                                                             $file_icon = $attachment_link;
                                                             $open_link = 'open_image';
                                                             ?>
                                                             data-toggle="modal"
                                                             data-target="#exampleModal_<?php echo $attachment['attachment_id']; ?>"
                                                             <?php
                                                             }
                                                             ?>
                                                             onclick="<?php echo $open_link; ?>('<?php echo $attachment_link; ?>')"
                                                             src="<?php echo $file_icon; ?>"
                                                             class="thumbnail"/>
                                                    </div>
                                                    <?php endforeach; ?>
                                                </div>
                                                <?php endif; ?>


                                                    <div class="form-group">
                                                        <label>Defect Photo / Report: <b style="color: #b91d19">(20MB Max)</b></label>
                                                        <input type="file" data-asset="default" name="p_attachment[]" class="form-control p_attachment last_upload" multiple="multiple"/>
                                                        <a href="javascript:void(0);" onclick="another_upload($(this))" class="another_upload">[ Upload Another ]</a>
                                                    </div>


                                                <div class="form-group">

                                                    <?php if (!empty($task_status) && $task_status == 'in_progress') { ?>
                                                    <?php if(auth()->user()->role == 'Admin' || auth()->user()->team == 'QM QC') { ?>

                                                        <input type="submit" name="submit" value="Save" style="font-size: medium" class="btn btn-lg btn-primary submit"/>
                                                        <label style="font-size: medium; font-weight:100; color: #b91d19; padding: 0 0 0 20px;">* Save all changes before clicking action buttons. *</label>
                                                        <br>
                                                        <input type="button"
                                                           value="Review"
                                                           onclick="action_review($(this))"
                                                           data-task-id="<?php echo $task_id; ?>"
                                                           style="margin-top:10px; font-size: medium;"
                                                           class="btn btn-lg btn-review submit"/>

                                                    <?php } ?>
                                                    <?php }?>

                                                    <?php if (!empty($task_status) && $task_status == 'action_review') { ?>
                                                    <?php if(auth()->user()->role == 'Admin' || auth()->user()->id == $t_author) { ?>
                                                    <input type="button"
                                                           value="Complete"
                                                           onclick="action_complete($(this))"
                                                           data-task-id="<?php echo $task_id; ?>"
                                                           style="margin-top:10px; font-size: medium;"
                                                           class="btn btn-lg btn-complete submit"/>
                                                    <?php } ?>
                                                    <?php }?>

                                                </div>

                                            </form>

                                            <?php if (!empty($data[1])): ?>
                                            <?php foreach ($data[1] as $attachment): ?>
                                            <div class="modal fade"
                                                 id="exampleModal_<?php echo $attachment['attachment_id']; ?>"
                                                 tabindex="-1"
                                                 data-backdrop="false"
                                                 role="dialog"
                                                 aria-labelledby="exampleModalLabel"
                                                 aria-hidden="true">
                                                <div class="modal-dialog"
                                                     role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button"
                                                                    class="close"
                                                                    data-dismiss="modal"
                                                                    aria-label="Close">
                                                                <span aria-hidden="true">×</span>
                                                            </button>
                                                        </div>
                                                        <!--Modal body with image-->
                                                        <?php $name = explode('/', $attachment['attachment']); ?>
                                                        <?php $name = $name[count($name)-1]; ?>
                                                        <div class="modal-title text-lg-center" style="font-size: 18px; color: #1a1a1a; float: right;">{{ $name }} </div>
                                                        <div class="modal-title text-sm-center">{{ $attachment['date_created'] }} </div>
                                                        <div class="modal-body">
                                                            <img class="img-fluid" src="<?php echo '/storage' . $attachment['attachment']; ?>">
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button"
                                                                    class="btn btn-primary"
                                                                    data-dismiss="modal"
                                                                    onclick="open_download('<?php echo '/storage' . $attachment['attachment']; ?>')"
                                                            >
                                                                Download
                                                            </button>
                                                            <button type="button"
                                                                    class="btn btn-danger"
                                                                    data-dismiss="modal">
                                                                Close
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php endforeach; ?>
                                            <?php endif; ?>

                                        </div>
                                    </section>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    @endif

                </div>

                @if(!empty($correspondences))
                <div class="col-lg-5">
                    <div class="card">
                        <div class="card-header">
                            <h4>CORRESPONDENCE</h4>
                        </div>

                        <div class="card-body">
                            <div class="col">

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

                @endif

            </div>
        </div>

    </section>

    <script type="text/javascript">
        $(function() {
            var today = new Date();
            $('input[name="posting_date"]').daterangepicker({
                singleDatePicker: true,
                minDate: today,
                locale: {
                    format: 'YYYY-MM-DD'
                },
            });
            $('input[name="processing_date"]').daterangepicker({
                singleDatePicker: true,
                minDate: today,
                locale: {
                    format: 'YYYY-MM-DD'
                },
            });
        });
    </script>

    <script>
        $(document).ready(function(){
            $("#vendor_auto").autocomplete({
                source: function (request, cb){
                    $.ajax({
                        url: "<?php echo url('/admin/project/autocomplete_vendor'); ?>"+"?code="+request.term,
                        method: 'GET',
                        dataType: 'json',
                        success: function(res){
                            var result;
                            result = [
                                {
                                    label : 'There is no matching record found for '+request.term,
                                    value : ''
                                }
                            ];
                            // console.log(res);
                            if(res.length) {
                                result = $.map(res, function(obj){
                                    return {
                                        label: obj.code,
                                        value: obj.code,
                                        data : obj
                                    };
                                });
                            }
                            cb(result);
                        }
                    });
                },
                select:function(e,selectedData) {
                    console.log(selectedData);
                    if(selectedData && selectedData.item && selectedData.item.data){
                        var data = selectedData.item.data;
                        $('#vendor_name').val(data.name);
                    }
                }
            });
        });

        function blocked_rate_cal(){
            var inspection_qty = document.getElementById('inspection_qty').value;
            if(inspection_qty == 0){
                alert("You can not input 0 in Inspection QTY");
                return;
            }
            var defect_qty = document.getElementById('defect_qty').value;
            var blocked_rate = defect_qty / inspection_qty * 100;
            var blocked_rate = blocked_rate + '%';
            document.getElementById('blocked_rate').value = blocked_rate;
        }

        function defect_cost_cal(){
            var defect_qty = document.getElementById('defect_qty').value;
            var item_net_cost = document.getElementById('item_net_cost').value;
            var defect_cost = defect_qty * item_net_cost;
            // var defect_cost_dollar = '$' + defect_cost;
            document.getElementById('defect_cost').value = defect_cost;
        }

        function total_claim_cal(){
            var defect_cost = document.getElementById('defect_cost').value;
            var full_cost = document.getElementById('full_cost').value;
            var rework_cost = document.getElementById('rework_cost').value;
            var special_inspection_cost = document.getElementById('special_inspection_cost').value;
            var total_claim = Number(defect_cost) + Number(full_cost) + Number(rework_cost) + Number(special_inspection_cost);
            document.getElementById('total_claim').value = total_claim;
        }

    </script>

    <script type="text/javascript">

        $(function() {
            $('.summernote').summernote('disable');
        });

        const queryString = window.location.href;
        if(queryString.includes('#')) {
            var asset_id = queryString.split('#').pop();
            $('#asset-id-'+asset_id).show();
            $('#arrow-'+asset_id).removeClass('fa-angle-down');
            $('#arrow-'+asset_id).addClass('fa-angle-up');
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

        function delete_legal_request_type(el) {
            if (confirm("Are you sure to Delete?") == true) {
                let id = $(el).attr('data-legal-request-type-id');
                let type = $(el).attr('data-legal-request-type');
                $.ajax({
                    url: "<?php echo url('/admin/product_receiving/requestTypeRemove'); ?>"+"/"+id+"/"+type,
                    type: "GET",
                    datatype: "json",
                    success: function(response) {
                        if(response != 'fail'){
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
                    url: "<?php echo url('/admin/product_receiving/fileRemove'); ?>"+"/"+id,
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

        function action_start(el){

            if (confirm("Your request status will change to 'In Progress'") == true) {
                let id = $(el).attr('data-task-id');
                $.ajax({
                    url: "<?php echo url('/admin/product_receiving/actionInProgress'); ?>"+"/"+id,
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

            if (confirm("The work has been updated for review, and the status will now change to 'Action Review'. If data is entered but not saved, please save first.") == true) {
                let id = $(el).attr('data-task-id');
                $.ajax({
                    url: "<?php echo url('/admin/product_receiving/actionReview'); ?>"+"/"+id,
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

            if (confirm("The request is now complete, and no further changes can be made.") == true) {
                let id = $(el).attr('data-task-id');
                $.ajax({
                    url: "<?php echo url('/admin/product_receiving/actionComplete'); ?>"+"/"+id,
                    type: "GET",
                    datatype: "json",
                    success: function(response) {
                        if(response != 'fail'){
                            alert('You have successfully changed the status to "Completed".');
                            window.location.reload(response);
                            $(el).remove();
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
