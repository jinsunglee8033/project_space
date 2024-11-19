@extends('layouts.dashboard')

@section('content')

    <?php
    if (!empty($campaign)){
        if($campaign->status == 'archived'){
            $status = 'archived';
            $second = 'Project Archives';
            $third = 'Show Project';
        }else if($campaign->status == 'deleted'){
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
        $second = 'Project Manage';
        $third = 'Create Project';
    }

    ?>

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
        @include('admin.campaign.flash')
        <div class="section-header">
            <h1>Create Project</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ url('admin/campaign') }}">{{ $second }}</a></div>
                <div class="breadcrumb-item active">{{ $third }}</div>
            </div>
        </div>

        <div class="section-body">
            <h2 class="section-title">{{ $third }}</h2>
            <div class="row">
                <div class="col-lg-6">
                    @if (empty($campaign))
                        <form method="POST" action="{{ route('campaign.store') }}" enctype="multipart/form-data">
                    @else
                        <form method="POST" action="{{ route('campaign.update', $campaign->id) }}" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="{{ $campaign->id }}" />
                            @method('PUT')
                    @endif
                    @csrf
                            <div class="card">
                                <div class="card-header">
                                    <h4>{{ $third }}</h4>
                                    @if ( ($third == 'Update Project') && (auth()->user()->role == 'admin') )
                                        <div class="text-right">
                                            <button class="btn btn-primary" id="send_archive" onclick="send_archive_project({{ $campaign->id }})">Send Archive</button>
                                        </div>
                                    @elseif ( ($third == 'Deleted Project') && (auth()->user()->role == 'admin') )
                                        <div class="text-right">
                                            <button class="btn btn-primary" id="send_active" onclick="send_active_project({{ $campaign->id }})">Send Active Back</button>
                                        </div>
                                    @endif
                                </div>
                                <div class="card-body">
{{--                                    @include('admin.campaign.flash')--}}
                                    <div class="col">
                                        <div class="form-group">
                                            @if($author_name != null)
                                                <p style="float: right">Project Creator &nbsp
                                                    <span style="color:#000000; font-size: medium;background-color: #ecdbe8;border-radius: 6px;">
                                                    &nbsp{{ $author_name }}&nbsp
                                                    </span>
                                                </p>
                                            @endif
                                            <label>Brands</label>
                                            <select class="form-control @error('campaign_brand') is-invalid @enderror @if (!$errors->has('campaign_brand') && old('campaign_brand')) is-valid @endif"
                                                    name="campaign_brand" id="campaign_brand" onchange="check_retailer()">
                                                <option value="">Select</option>
                                                @if(empty(old('campaign_brand')))
                                                    @foreach ($brands as $key => $value)
                                                        <option value="{{ $key }}" {{ $key == $campaign_brand ? 'selected' : '' }}>
                                                            {{ $value }}
                                                        </option>
                                                    @endforeach
                                                @else
                                                    @foreach ($brands as $key => $value)
                                                        <option value="{{ $key }}" {{ $key == old('campaign_brand') ? 'selected' : '' }}>
                                                            {{ $value }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            @error('campaign_brand')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>


                                        @if (!empty($campaign->retailer))
                                        <div class="form-group retailer_box">
                                            <label>Retailer</label>
                                            <select class="form-control @error('retailer') is-invalid @enderror @if (!$errors->has('retailer') && old('retailer')) is-valid @endif"
                                                    name="retailer" id="retailer">
                                                <option value="">Select</option>
                                                @foreach ($retailers as $key => $value)
                                                    <option value="{{ $value }}" {{ $value == $retailer ? 'selected' : '' }}>
                                                        {{ $value }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('retailer')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                        @else
                                            <div class="form-group retailer_box" style="display: none">
                                                <label>Retailer</label>
                                                <select class="form-control @error('retailer') is-invalid @enderror @if (!$errors->has('retailer') && old('retailer')) is-valid @endif"
                                                        name="retailer" id="retailer">
                                                    <option value="">Select</option>
                                                    @foreach ($retailers as $key => $value)
                                                        <option value="{{ $value }}" {{ $value == $retailer ? 'selected' : '' }}>
                                                            {{ $value }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('retailer')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        @endif



                                        <div class="form-group">
                                            <label>Department (Promotion)</label>
                                            <select class="form-control @error('promotion') is-invalid @enderror @if (!$errors->has('promotion') && old('promotion')) is-valid @endif"
                                                    name="promotion">
                                                <option value="">Select</option>
                                                @if(empty(old('promotion')))
                                                    @foreach ($promotions as $value)
                                                        <option value="{{ $value }}" {{ $value == $promotion ? 'selected' : '' }}>
                                                            {{ $value }}
                                                        </option>
                                                    @endforeach
                                                @else
                                                    @foreach ($promotions as $value)
                                                        <option value="{{ $value }}" {{ $value == old('promotion') ? 'selected' : '' }}>
                                                            {{ $value }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            @error('$promotion')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label>Project Name: </label>
                                            <input type="text" name="name"
                                                   class="form-control @error('name') is-invalid @enderror @if (!$errors->has('name') && old('name')) is-valid @endif"
                                                   value="{{ old('name', !empty($campaign) ? $campaign->name : null) }}">
                                            @error('name')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Run From: </label>
                                                    <input type="text" name="date_from" id="date_from" placeholder="Start date" autocomplete="off"
                                                           class="form-control datepicker @error('date_from') is-invalid @enderror @if (!$errors->has('date_from') && old('date_from')) is-valid @endif"
                                                           value="{{ old('date_from', !empty($campaign) ? $campaign->date_from : null) }}">
                                                    @error('date_from')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>
{{--                                            <div class="col-md-6">--}}
{{--                                                <div class="form-group">--}}
{{--                                                    <label>Run To: </label>--}}
{{--                                                    <input type="text" name="date_to" id="date_to" placeholder="End date"--}}
{{--                                                           class="form-control datepicker @error('date_to') is-invalid @enderror @if (!$errors->has('date_to') && old('date_to')) is-valid @endif"--}}
{{--                                                           value="{{ old('date_to', !empty($campaign) ? $campaign->date_to : null) }}">--}}
{{--                                                    @error('date_to')--}}
{{--                                                    <div class="invalid-feedback">--}}
{{--                                                        {{ $message }}--}}
{{--                                                    </div>--}}
{{--                                                    @enderror--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
                                        </div>
                                        <div class="form-group">
                                            <label>Primary Message: </label>
                                            <input type="text" name="primary_message"
                                                   class="form-control @error('primary_message') is-invalid @enderror @if (!$errors->has('primary_message') && old('primary_message')) is-valid @endif"
                                                   value="{{ old('primary_message', !empty($campaign) ? $campaign->primary_message : null) }}">
                                            @error('primary_message')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label>Products Featured: </label>
                                            <input type="text" name="products_featured"
                                                   class="form-control @error('products_featured') is-invalid @enderror @if (!$errors->has('products_featured') && old('products_featured')) is-valid @endif"
                                                   value="{{ old('products_featured', !empty($campaign) ? $campaign->products_featured : null) }}">
                                            @error('products_featured')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label>Secondary Message: </label>
                                            <input type="text" name="secondary_message"
                                                   class="form-control @error('secondary_message') is-invalid @enderror @if (!$errors->has('secondary_message') && old('secondary_message')) is-valid @endif"
                                                   value="{{ old('secondary_message', !empty($campaign) ? $campaign->secondary_message : null) }}">
                                            @error('secondary_message')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>

                                        <div class="form-group checkboxes">
                                            <label>Asset Type: </label><br/>
                                            <?php if (isset($asset_list)): ?>
                                                <?php foreach($asset_list as $asset_type_item): ?>
                                                <?php $checkbox_fields = explode(', ', $asset_type); ?>
                                                <input  <?php if (in_array($asset_type_item->asset_name, $checkbox_fields)) echo "checked" ?>
                                                        type="checkbox"
                                                        name="asset_type[]"
                                                        value="<?php echo $asset_type_item->asset_name ?>"
                                                />
                                                <span> <?php echo $asset_type_item->asset_name; ?></span><br/>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>

{{--                                        <div class="form-group">--}}
{{--                                            <label class="form-label">Asset Type: </label>--}}
{{--                                            <div class="selectgroup selectgroup-pills">--}}
{{--                                                <?php if (isset($asset_list)): ?>--}}
{{--                                                    <?php foreach($asset_list as $asset_type_item): ?>--}}
{{--                                                    <?php $checkbox_fields = explode(', ', $asset_type); ?>--}}
{{--                                                        <label class="selectgroup-item">--}}
{{--                                                            <input type="checkbox" name="asset_type[]" value="<?php echo $asset_type_item->asset_name ?>" class="selectgroup-input" <?php if (in_array($asset_type_item->asset_name, $checkbox_fields)) echo "checked" ?>>--}}
{{--                                                            <span class="selectgroup-button"><?php echo $asset_type_item->asset_name; ?></span>--}}
{{--                                                        </label>--}}
{{--                                                    <?php endforeach; ?>--}}
{{--                                                <?php endif; ?>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}

                                        <div class="form-group">
                                            <label>Note / Description: </label>
                                            {!! Form::textarea('campaign_notes', !empty($campaign) ? $campaign->campaign_notes : null, ['class' => 'form-control summernote']) !!}
                                            @error('campaign_notes')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>

                                        <?php if (!empty($attach_files)): ?>
                                            <label>Attachments: </label>
                                            <br/>
                                            <?php foreach ($attach_files as $attachment): ?>
                                                <?php
                                                        $file_ext = $attachment['file_ext'];
                                                        if(strpos($file_ext, ".") !== false){
                                                            $file_ext = substr($file_ext, 1);
                                                        }
                                                        $not_image = ['pdf','doc','docx','pptx','ppt','mp4','xls','xlsx','csv','zip'];
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
                                        <?php endif; ?>

                                        <div class="form-group">
                                            <label>Upload Visual References: <b style="color: #b91d19">(20MB Max)</b></label>
                                            <input type="file" id="c_attachment[]" name="c_attachment[]"
                                                   data-asset="default" multiple="multiple"
                                                   class="form-control c_attachment last_upload @error('c_attachment') is-invalid @enderror @if (!$errors->has('c_attachment') && old('c_attachment')) is-valid @endif"
                                                   value="{{ old('c_attachment', !empty($campaign) ? $campaign->secondary_message : null) }}">
                                            <a href="javascript:void(0);" onclick="another_upload($(this))" class="another_upload">[ Upload Another ]</a>
                                            @error('c_attachment')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>

                                    </div>
                                </div>
                                <div class="card-footer text-right">
                                    <?php if ($status == 'active') { ?>
                                        <button class="btn btn-primary">{{ empty($campaign) ? __('Create') : __('Save Changes') }}</button>
                                    <?php } ?>
                                </div>
                            </div>
                        </form>

                    @if(!empty($assets))

                        <?php foreach ($assets as $asset): ?>

                            <div class="card assets_existing">
                                <?php

                                if($asset->status == 'to_do'){
                                    $left_border_color = '#a50018';
                                }else if($asset->status == 'in_progress'){
                                    $left_border_color = '#238c21';
                                }else if($asset->status == 'done'){
                                    $left_border_color = '#13439a';
                                }else if($asset->status == 'final_approval'){
                                    $left_border_color = '#7e7e7e';
                                }else if($asset->status == 'copy_review'){
                                    $left_border_color = '#ff6e19';
                                }else {
                                    $left_border_color = 'white';
                                }

                                ?>
                                <div class="clearfix" id="{{$asset->a_id}}" style="border-left: 10px solid {{ $left_border_color }}; border-radius: 20px;">

                                    <div class="asset--grid-row">

                                        <div class="project-info">
                                            <ul class="project-info-list">
                                                <li><strong>{{ ucwords(str_replace('_', ' ', $asset->a_type)) }} #{{ $asset->a_id }}</strong> </li>
                                                <?php $temp_status = $asset->status ?>
                                                <?php if ($temp_status == 'done') $temp_status = 'creative_review'; ?>
                                                <?php if ($left_border_color == 'white') $left_border_color = '#6c757d'; ?>
                                                <li><strong>Status: </strong> <b style="color: {{ $left_border_color }};">{{ ucwords(str_replace('_', ' ', $temp_status)) }}</b></li>
                                                <li><strong>Due: </strong> {{ date('m/d/Y', strtotime($asset->due)) }}</li>
                                            </ul>
                                        </div>

                                        <ul class="project-members-list">

                                            <li><strong>Creator : </strong> <span class="asset-creator-bg">{{ $asset->asset_creator }}</span></li>
                                            <li><strong>Copy :</strong>
                                                <?php if(!empty($asset->copy_writer)) { ?>
                                                <span class="copy-writer-bg">{{ $asset->copy_writer }}</span>
                                                <?php } ?>
                                            </li>
                                            <li><strong>Assignee : </strong>
                                                <?php if(!empty($asset->assignee)) { ?>
                                                <span class="assignee-bg">
                                                    {{ $asset->assignee }}
                                                </span>
                                                <?php } ?>
                                            </li>
                                        </ul>

                                        <div class="col-md-12">
                                            <div class="project-action-icons">
                                                <ul class="project-icons">
                                                    <?php if(auth()->user()->role == 'admin') { ?>
                                                        <li>
                                                            <i class="fa fa-spin fa-cog"
                                                            data-toggle="modal"
                                                            data-target="#asset-owner-{{$asset->a_id}}"></i>
                                                        </li>
                                                    <?php } ?>
                                                        <li>
                                                            <i class="fa fa-address-card"
                                                               data-toggle="modal"
                                                               data-target="#myModal-{{$asset->a_id}}"></i>
                                                        </li>
{{--                                                        <li>--}}
{{--                                                            <i id="arrow-{{$asset->a_id}}" class="dropdown fa fa-angle-down" onclick="click_arrow(this, {{$asset->a_id}})"></i>--}}
{{--                                                        </li>--}}
                                                        <li>
                                                            <a  href="javascript:void(0);"
                                                            class="close"
                                                            data-id=""
                                                            data-asset-id="{{ $asset->a_id }}"
                                                            data-asset-type="{{ $asset->a_type }}"
                                                            onclick="delete_asset($(this));">
                                                            <i class="fa fa-times"></i>
                                                            </a>
                                                        </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <div style="text-align: center;">
                                        <i id="arrow-{{$asset->a_id}}" class="dropdown fa-lg fa fa-angle-down" onclick="click_arrow(this, {{$asset->a_id}})"></i>
                                    </div>

                                    <div id="asset-id-{{$asset->a_id}}" class="box-body form_creator" data-asset-id="{{ $asset->a_id }}" style="display: none">
                                        <section>
                                            <div class="inner_box">
                                                <?php $data = [$asset->detail, $asset->files, $asset->status, $asset->decline_creative, $asset->decline_kec, $asset->decline_copy, $asset->assignee, $asset->team_to, $asset->copy_writer, $asset]; ?>
                                                @include('admin.campaign.asset.'.$asset->a_type, $data)
                                            </div>
                                        </section>
                                    </div>

                                </div>
                            </div>

                        <?php endforeach; ?>

                    @endif

                    @if(!empty($campaign))

                        <div class="clearfix" id="asset_selector" style="display: block">
                        <div class="card box asset box-primary">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-md-6" id="add_asset_btn" style="display: block">
                                        <a class="btn btn-light add-row" onclick="click_asset_add_btn()">Add asset needed</a>
                                    </div>
                                </div>

                                <div id="asset_new" class="box-body form_creator" data-asset-id="" style="display: none">
                                    <section>
                                        <div class="inner_box">
                                            <div class="form-group">
                                                <label style="color: #000000; font-size: 1.2rem;">Asset Type: </label>
                                                <span id="asset_type_name" class="asset_type_name"></span>
                                                <span class="float-right">
                                                    <a href="{{ url('admin/campaign/'. $campaign->id .'/edit') }}">
                                                        <i class="fa fa-times" style="font-size: 1.5rem;"></i>
                                                    </a>
                                                </span>

                                                <select name="add_asset_type" id="add_asset_type" class="form-control" onchange="change_asset_type()">
                                                    <option value="">Select</option>
                                                    <option value="email_blast">Email Blast</option>
                                                    <option value="social_ad">Social Ad</option>
                                                    <option value="website_banners">Website Banners</option>
{{--                                                    <option value="website_changes">Website Changes</option>--}}
                                                    <option value="landing_page">Landing Page</option>
                                                    <option value="misc">Misc</option>
                                                    <option value="sms_request">SMS Request</option>
                                                    <option value="topcategories_copy">Top Categories Copy</option>
                                                    <option value="programmatic_banners">Programmatic Banners</option>
                                                    <option value="image_request">Image Request</option>
                                                    <option value="roll_over">Roll Over</option>
                                                    <option value="store_front">Store Front</option>
                                                    <option value="a_content">A+ Content</option>
                                                    <option value="youtube_copy">YouTube Copy</option>
                                                    <option value="info_graphic">Info Graphic</option>
                                                </select>

                                            </div>

                                            <div id="new_email_blast" style="display: none;">
                                                @include('admin.campaign.asset.new.email_blast')
                                            </div>
                                            <div id="new_landing_page" style="display: none;">
                                                @include('admin.campaign.asset.new.landing_page')
                                            </div>
                                            <div id="new_misc" style="display: none;">
                                                @include('admin.campaign.asset.new.misc')
                                            </div>
                                            <div id="new_sms_request" style="display: none;">
                                                @include('admin.campaign.asset.new.sms_request')
                                            </div>
                                            <div id="new_social_ad" style="display: none;">
                                                @include('admin.campaign.asset.new.social_ad')
                                            </div>
                                            <div id="new_website_banners" style="display: none;">
                                                @include('admin.campaign.asset.new.website_banners')
                                            </div>
{{--                                            <div id="new_website_changes" style="display: none;">--}}
{{--                                                @include('admin.campaign.asset.new.website_changes')--}}
{{--                                            </div>--}}
                                            <div id="new_topcategories_copy" style="display: none;">
                                                @include('admin.campaign.asset.new.topcategories_copy')
                                            </div>
                                            <div id="new_programmatic_banners" style="display: none;">
                                                @include('admin.campaign.asset.new.programmatic_banners')
                                            </div>
                                            <div id="new_image_request" style="display: none;">
                                                @include('admin.campaign.asset.new.image_request')
                                            </div>
                                            <div id="new_roll_over" style="display: none;">
                                                @include('admin.campaign.asset.new.roll_over')
                                            </div>
                                            <div id="new_store_front" style="display: none;">
                                                @include('admin.campaign.asset.new.store_front')
                                            </div>
                                            <div id="new_a_content" style="display: none;">
                                                @include('admin.campaign.asset.new.a_content')
                                            </div>
                                            <div id="new_youtube_copy" style="display: none;">
                                                @include('admin.campaign.asset.new.youtube_copy')
                                            </div>
                                            <div id="new_info_graphic" style="display: none;">
                                                @include('admin.campaign.asset.new.info_graphic')
                                            </div>
                                        </div>
                                    </section>
                                </div>

                            </div>
                        </div>
                    </div>
                    @endif

                </div>

                @if(!empty($campaign))
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h4>CORRESPONDENCE</h4>
                            <div class=" text-right">
                                <button class="btn btn-primary" id="add_note_btn" onclick="click_add_note_btn()">Add Note</button>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="col">

{{--                                <div class="form-group" id="add_note" style="display: none">--}}
{{--                                    <div class="note">--}}
{{--                                        <textarea class="form-control"--}}
{{--                                                  id="new_note"--}}
{{--                                                  name="new_note"--}}
{{--                                                  style="height: 100px;"></textarea>--}}
{{--                                        <div class=" text-right">--}}
{{--                                            <button class="btn btn-primary" onclick="click_cancel_note_btn()">Cancel</button>--}}
{{--                                            <button type="submit" class="btn btn-primary">Send Note</button>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                </div>--}}


                                    <section id="add_note" class="notes" style="display: none;">

                                        <div class="write note">
                                            <form method="POST" action="{{ route('asset.asset_add_note') }}" enctype="multipart/form-data">
                                                @csrf
                                                <select name="exist_assets" id="exist_assets" style="font-size: large; width: 50%; background-color: #c4c4c4" onchange="select_note_asset()">
                                                    <option value="">Select Asset</option>
                                                    <?php foreach ($assets as $asset): ?>
                                                    <option value="{{ucwords(str_replace('_', ' ', $asset->a_type))}} #{{ $asset->a_id }}">{{ucwords(str_replace('_', ' ', $asset->a_type))}} #{{ $asset->a_id }}</option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <input type="hidden" name="c_id" value="{{ $campaign->id }}">
                                                <input type="hidden" id="email_list" name="email_list" value="">
                                                <input type="hidden" name="c_title" value="{{ $campaign->name }}">

                                                <textarea id="create_note" name="create_note" class="wysiwyg"></textarea>
                                                <div id="at_box" style="display: none">
                                                    <input class="form-control" placeholder="Name" type="text"/>
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
                                                            <div class="media-right"><div class="text-time">{{ date('m/d/y g:i A', strtotime($correspondence->date_created)) }}</div></div>
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

    @if(!empty($assets))
        <?php foreach ($assets as $asset): ?>
            <div class="modal fade" id="myModal-{{$asset->a_id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog modal-xl" role="document">

                    <div class="modal-content">

                        <form method="POST" action="{{ route('asset.asset_notification_user') }}" enctype="multipart/form-data">
                            @csrf

                            <input type="hidden" name="a_id" value="{{ $asset->a_id }}">
                            <input type="hidden" name="c_id" value="{{ $campaign->id }}">

                            <div class="modal-header">
                                <h4 class="modal-title" id="myModalLabel">Notification List - {{ ucwords(str_replace('_', ' ', $asset->a_type)) }} #{{ $asset->a_id }} </h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label style="color: #b91d19; font-size: medium">Do not need to choose Project & Asset Creators since they will receive all notifications of the project</label>
                                    <div class="columns" style="column-count: 4;">
                                        <?php if (isset($users)): ?>
                                            <?php if (isset($asset->asset_notification_user[0]->user_id_list)) { ?>

                                                @foreach($users as $user)
                                                <?php $checkbox_fields = explode(', ', $asset->asset_notification_user[0]->user_id_list); ?>
                                                        <div class="col-md">
                                                            <div class="form-check">
                                                                <input  <?php if (in_array($user->id, $checkbox_fields)) echo "checked" ?>
                                                                        type="checkbox"
                                                                        name="user_id_list[]"
                                                                        value="{{ $user->id }}"
                                                                >
                                                                <label class="form-check-label " for="{{ $user->id }}">
                                                                    {{ $user->first_name }} {{ $user->last_name }}
                                                                </label>
                                                            </div>
                                                        </div>
                                                @endforeach
                                            <?php }else{ ?>
                                                @foreach($users as $user)
                                                    <div class="col-md">
                                                        <div class="form-check">
                                                            <input
                                                                type="checkbox"
                                                                name="user_id_list[]"
                                                                value="{{ $user->id }}"
                                                            >
                                                            <label class="form-check-label " for="{{ $user->id }}">
                                                                {{ $user->first_name }} {{ $user->last_name }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            <?php } ?>
                                        <?php endif; ?>
                                    </div>

                                </div>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Save changes</button>
                            </div>

                        </form>

                    </div>
                </div>
            </div>


            <div class="modal fade" id="asset-owner-{{$asset->a_id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog modal-xl" role="document">

                    <div class="modal-content">

                        <form method="POST" action="{{ route('asset.asset_owner_change') }}" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="a_id" value="{{ $asset->a_id }}">
                            <input type="hidden" name="c_id" value="{{ $campaign->id }}">
                            <div class="modal-header">
                                <h4 class="modal-title" id="myModalLabel">Change Asset Creator - {{ ucwords(str_replace('_', ' ', $asset->a_type)) }} #{{ $asset->a_id }} </h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <div class="columns" style="column-count: 4;">
                                        <?php if (isset($users)): ?>
                                            @foreach($users as $user)
                                                <div class="col-md">
                                                    <div class="form-check">
                                                        <input  <?php if ($user->id == $asset->asset_creator_id) echo "checked" ?>
                                                                type="radio"
                                                                name="author_id"
                                                                value="{{ $user->id }},{{ $user->first_name }}"
                                                        >
                                                        <label class="form-check-label " for="{{ $user->id }}">
                                                            {{ $user->first_name }} {{ $user->last_name }}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        <?php endif; ?>
                                    </div>

                                </div>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Save changes</button>
                            </div>

                        </form>

                    </div>
                </div>
            </div>

        <?php endforeach; ?>
    @endif

    <?php if (!empty($attach_files)): ?>
        <?php foreach ($attach_files as $attachment): ?>
            <div class="modal fade"
                 id="exampleModal_<?php echo $attachment['attachment_id']; ?>"
                 tabindex="-1"
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
                                    <span aria-hidden="true">
                                      ×
                                  </span>
                            </button>
                        </div>
                        <!--Modal body with image-->
                        <?php $name = explode('/', $attachment['attachment']); ?>
                        <?php $name = $name[count($name)-1]; ?>
                        <div class="modal-title text-lg-center">{{ $name }}</div>
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
            if($('#campaign_brand').val() == 10){
                $(".retailer_box").show();
            }else{
                $(".retailer_box").hide();
                $("#retailer").val("");
            }
        }

        function click_asset_add_btn(){

            $("#add_asset_btn").hide();
            $("#asset_new").show();

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


        function change_asset_type(){

            add_asset_type = $('#add_asset_type option:selected').val();

            if(add_asset_type == 'email_blast'){
                $("#asset_type_name").text('Email Blast');
                $("#new_email_blast").show();
                $("#new_landing_page").hide();
                $("#new_misc").hide();
                $("#new_sms_request").hide();
                $("#new_social_ad").hide();
                $("#new_website_banners").hide();
                // $("#new_website_changes").hide();
                $("#new_topcategories_copy").hide();
                $("#new_programmatic_banners").hide();
                $("#new_image_request").hide();
                $("#new_roll_over").hide();
                $("#new_store_front").hide();
                $("#new_a_content").hide();
                $("#new_youtube_copy").hide();
                $("#new_info_graphic").hide();
            }
            if(add_asset_type == 'landing_page'){
                $("#asset_type_name").text('Landing Page');
                $("#new_email_blast").hide();
                $("#new_landing_page").show();
                $("#new_misc").hide();
                $("#new_sms_request").hide();
                $("#new_social_ad").hide();
                $("#new_website_banners").hide();
                // $("#new_website_changes").hide();
                $("#new_topcategories_copy").hide();
                $("#new_programmatic_banners").hide();
                $("#new_image_request").hide();
                $("#new_roll_over").hide();
                $("#new_store_front").hide();
                $("#new_a_content").hide();
                $("#new_youtube_copy").hide();
                $("#new_info_graphic").hide();
            }
            if(add_asset_type == 'misc'){
                $("#asset_type_name").text('Misc');
                $("#new_email_blast").hide();
                $("#new_landing_page").hide();
                $("#new_misc").show();
                $("#new_sms_request").hide();
                $("#new_social_ad").hide();
                $("#new_website_banners").hide();
                // $("#new_website_changes").hide();
                $("#new_topcategories_copy").hide();
                $("#new_programmatic_banners").hide();
                $("#new_image_request").hide();
                $("#new_roll_over").hide();
                $("#new_store_front").hide();
                $("#new_a_content").hide();
                $("#new_youtube_copy").hide();
                $("#new_info_graphic").hide();
            }
            if(add_asset_type == 'sms_request'){
                $("#asset_type_name").text('SMS Request');
                $("#new_email_blast").hide();
                $("#new_landing_page").hide();
                $("#new_misc").hide();
                $("#new_sms_request").show();
                $("#new_social_ad").hide();
                $("#new_website_banners").hide();
                // $("#new_website_changes").hide();
                $("#new_topcategories_copy").hide();
                $("#new_programmatic_banners").hide();
                $("#new_image_request").hide();
                $("#new_roll_over").hide();
                $("#new_store_front").hide();
                $("#new_a_content").hide();
                $("#new_youtube_copy").hide();
                $("#new_info_graphic").hide();
            }
            if(add_asset_type == 'social_ad'){
                $("#asset_type_name").text('Social Ad');
                $("#new_email_blast").hide();
                $("#new_landing_page").hide();
                $("#new_misc").hide();
                $("#new_sms_request").hide();
                $("#new_social_ad").show();
                $("#new_website_banners").hide();
                // $("#new_website_changes").hide();
                $("#new_topcategories_copy").hide();
                $("#new_programmatic_banners").hide();
                $("#new_image_request").hide();
                $("#new_roll_over").hide();
                $("#new_store_front").hide();
                $("#new_a_content").hide();
                $("#new_youtube_copy").hide();
                $("#new_info_graphic").hide();
            }
            if(add_asset_type == 'website_banners'){
                $("#asset_type_name").text('Website Banners');
                $("#new_email_blast").hide();
                $("#new_landing_page").hide();
                $("#new_misc").hide();
                $("#new_sms_request").hide();
                $("#new_social_ad").hide();
                $("#new_website_banners").show();
                // $("#new_website_changes").hide();
                $("#new_topcategories_copy").hide();
                $("#new_programmatic_banners").hide();
                $("#new_image_request").hide();
                $("#new_roll_over").hide();
                $("#new_store_front").hide();
                $("#new_a_content").hide();
                $("#new_youtube_copy").hide();
                $("#new_info_graphic").hide();
            }
            // if(add_asset_type == 'website_changes'){
            //     $("#asset_type_name").text('Website Changes');
            //     $("#new_email_blast").hide();
            //     $("#new_landing_page").hide();
            //     $("#new_misc").hide();
            //     $("#new_social_ad").hide();
            //     $("#new_website_banners").hide();
            //     $("#new_website_changes").show();
            //     $("#new_topcategories_copy").hide();
            //     $("#new_programmatic_banners").hide();
            //     $("#new_image_request").hide();
            //     $("#new_roll_over").hide();
            //     $("#new_store_front").hide();
            //     $("#new_a_content").hide();
            // }
            if(add_asset_type == 'topcategories_copy'){
                $("#asset_type_name").text('TopCategories Copy');
                $("#new_email_blast").hide();
                $("#new_landing_page").hide();
                $("#new_misc").hide();
                $("#new_sms_request").hide();
                $("#new_social_ad").hide();
                $("#new_website_banners").hide();
                // $("#new_website_changes").hide();
                $("#new_topcategories_copy").show();
                $("#new_programmatic_banners").hide();
                $("#new_image_request").hide();
                $("#new_roll_over").hide();
                $("#new_store_front").hide();
                $("#new_a_content").hide();
                $("#new_youtube_copy").hide();
                $("#new_info_graphic").hide();
            }
            if(add_asset_type == 'programmatic_banners'){
                $("#asset_type_name").text('Programmatic Banners');
                $("#new_email_blast").hide();
                $("#new_landing_page").hide();
                $("#new_misc").hide();
                $("#new_sms_request").hide();
                $("#new_social_ad").hide();
                $("#new_website_banners").hide();
                // $("#new_website_changes").hide();
                $("#new_topcategories_copy").hide();
                $("#new_programmatic_banners").show();
                $("#new_image_request").hide();
                $("#new_roll_over").hide();
                $("#new_store_front").hide();
                $("#new_a_content").hide();
                $("#new_youtube_copy").hide();
                $("#new_info_graphic").hide();
            }
            if(add_asset_type == 'image_request'){
                $("#asset_type_name").text('Image Request');
                $("#new_email_blast").hide();
                $("#new_landing_page").hide();
                $("#new_misc").hide();
                $("#new_sms_request").hide();
                $("#new_social_ad").hide();
                $("#new_website_banners").hide();
                // $("#new_website_changes").hide();
                $("#new_topcategories_copy").hide();
                $("#new_programmatic_banners").hide();
                $("#new_image_request").show();
                $("#new_roll_over").hide();
                $("#new_store_front").hide();
                $("#new_a_content").hide();
                $("#new_youtube_copy").hide();
                $("#new_info_graphic").hide();
            }
            if(add_asset_type == 'roll_over'){
                $("#asset_type_name").text('Roll Over');
                $("#new_email_blast").hide();
                $("#new_landing_page").hide();
                $("#new_misc").hide();
                $("#new_sms_request").hide();
                $("#new_social_ad").hide();
                $("#new_website_banners").hide();
                // $("#new_website_changes").hide();
                $("#new_topcategories_copy").hide();
                $("#new_programmatic_banners").hide();
                $("#new_image_request").hide();
                $("#new_roll_over").show();
                $("#new_store_front").hide();
                $("#new_a_content").hide();
                $("#new_youtube_copy").hide();
                $("#new_info_graphic").hide();
            }
            if(add_asset_type == 'store_front'){
                $("#asset_type_name").text('Store Front');
                $("#new_email_blast").hide();
                $("#new_landing_page").hide();
                $("#new_misc").hide();
                $("#new_sms_request").hide();
                $("#new_social_ad").hide();
                $("#new_website_banners").hide();
                // $("#new_website_changes").hide();
                $("#new_topcategories_copy").hide();
                $("#new_programmatic_banners").hide();
                $("#new_image_request").hide();
                $("#new_roll_over").hide();
                $("#new_store_front").show();
                $("#new_a_content").hide();
                $("#new_youtube_copy").hide();
                $("#new_info_graphic").hide();
            }
            if(add_asset_type == 'a_content'){
                $("#asset_type_name").text('A+ Content');
                $("#new_email_blast").hide();
                $("#new_landing_page").hide();
                $("#new_misc").hide();
                $("#new_sms_request").hide();
                $("#new_social_ad").hide();
                $("#new_website_banners").hide();
                // $("#new_website_changes").hide();
                $("#new_topcategories_copy").hide();
                $("#new_programmatic_banners").hide();
                $("#new_image_request").hide();
                $("#new_roll_over").hide();
                $("#new_store_front").hide();
                $("#new_a_content").show();
                $("#new_youtube_copy").hide();
                $("#new_info_graphic").hide();
            }
            if(add_asset_type == 'youtube_copy'){
                $("#asset_type_name").text('YouTube Copy');
                $("#new_email_blast").hide();
                $("#new_landing_page").hide();
                $("#new_misc").hide();
                $("#new_sms_request").hide();
                $("#new_social_ad").hide();
                $("#new_website_banners").hide();
                // $("#new_website_changes").hide();
                $("#new_topcategories_copy").hide();
                $("#new_programmatic_banners").hide();
                $("#new_image_request").hide();
                $("#new_roll_over").hide();
                $("#new_store_front").hide();
                $("#new_a_content").hide();
                $("#new_youtube_copy").show();
                $("#new_info_graphic").hide();
            }
            if(add_asset_type == 'info_graphic'){
                $("#asset_type_name").text('Info Graphic');
                $("#new_email_blast").hide();
                $("#new_landing_page").hide();
                $("#new_misc").hide();
                $("#new_sms_request").hide();
                $("#new_social_ad").hide();
                $("#new_website_banners").hide();
                // $("#new_website_changes").hide();
                $("#new_topcategories_copy").hide();
                $("#new_programmatic_banners").hide();
                $("#new_image_request").hide();
                $("#new_roll_over").hide();
                $("#new_store_front").hide();
                $("#new_a_content").hide();
                $("#new_youtube_copy").hide();
                $("#new_info_graphic").show();
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

        function delete_asset(el) {
            if (confirm("Are you sure to Delete?") == true) {
                let id = $(el).attr('data-asset-id');
                let type = $(el).attr('data-asset-type');
                $.ajax({
                    url: "<?php echo url('/admin/campaign/assetRemove'); ?>"+"/"+id+"/"+type,
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
            upload_box = $('.c_attachment').prop('outerHTML');
            upload_name = $(el).prev().attr('name');
            upload_id = $(el).prev().attr('data-asset');
            $('.c_attachment').removeClass('last_upload');
            $(el).before(upload_box);
            $(el).prev().attr('name', upload_name);
        }

        function remove_file(el) {
            if (confirm("Are you sure to Delete File?") == true) {
                let id = $(el).attr('data-attachment-id');
                $.ajax({
                    url: "<?php echo url('/admin/campaign/fileRemove'); ?>"+"/"+id,
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
                    url: "<?php echo url('/admin/campaign/send_archive'); ?>"+"/"+project_id,
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
                    url: "<?php echo url('/admin/campaign/send_active'); ?>"+"/"+project_id,
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

        function change_to_copy_review(el){

            if (confirm("Are you sure to Copy Review?") == true) {
                let id = $(el).attr('data-asset-id');
                $.ajax({
                    url: "<?php echo url('/admin/asset/copyReview'); ?>"+"/"+id,
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

        function copy_work_start(el){

            if (confirm("Are you sure to Copy In Progress?") == true) {
                let id = $(el).attr('data-asset-id');
                $.ajax({
                    url: "<?php echo url('/admin/asset/copyInProgress'); ?>"+"/"+id,
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

        function work_start(el){

            if (confirm("Your request status will change to 'In Progress'") == true) {
                let id = $(el).attr('data-asset-id');
                $.ajax({
                    url: "<?php echo url('/admin/asset/inProgress'); ?>"+"/"+id,
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

        function work_done(el){

            if (confirm("Are you sure to Done?") == true) {
                let id = $(el).attr('data-asset-id');
                $.ajax({
                    url: "<?php echo url('/admin/asset/done'); ?>"+"/"+id,
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

        function final_approval(el){

            if (confirm("Are you sure to Final Approval?") == true) {
                let id = $(el).attr('data-asset-id');
                $.ajax({
                    url: "<?php echo url('/admin/asset/finalApproval'); ?>"+"/"+id,
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

    </script>

    <script type="text/javascript">

        // If on mobile and click the wysiwyg box make sure its no hidden
        // var $div = $("body");
        // var observer = new MutationObserver(function(mutations) {
        //     mutations.forEach(function(mutation) {
        //         if (mutation.attributeName === "data-ephox-mobile-fullscreen-style") {
        //             var attributeValue = $(mutation.target).attr(mutation.attributeName);
        //             if (attributeValue == undefined) {
        //                 $div.removeClass('tinymc');
        //             } else {
        //                 $div.addClass('tinymc');
        //             }
        //         }
        //     });
        // });
        //
        // observer.observe($div[0], {
        //     attributes: true
        // });

        tinymce.init({
            selector: '.wysiwyg',
            placeholder: 'If you would like to notify a specific person, type @ and enter the persons name in the field that appears. ',
            menubar: true,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'help', 'wordcount'
            ],
            toolbar: 'undo redo | insert | styles | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image code',
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
