@extends('layouts.dashboard')

@section('content')

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
            <h1>MM Request</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ url('admin/project') }}">MM Request</a></div>
                <div class="breadcrumb-item active">MM Request</div>
            </div>
        </div>

        <div class="section-body">
            <h2 class="section-title">MM Request</h2>
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
                                            <span style="color:#000000; font-size: medium;background-color: #ecdbe8;border-radius: 6px;">&nbsp{{ $author_name }}&nbsp</span>
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
                                    {!! Form::textarea('description', $project->description, ['class' => 'form-control summernote summernote_project']) !!}
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
                                            <label>International Sales Plan: </label>
                                            <select class="form-control" name="international_sales_plan" disabled>
                                                <option value="">Select</option>
                                                @foreach ($sales_plan_list as $value)
                                                    <option value="{{ $value }}" {{ $value == $project->international_sales_plan ? 'selected' : '' }}>
                                                        {{ $value }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Sale Available Date: </label>
                                            <input type="text" name="sale_available_date" id="sale_available_date"
                                                   disabled class="form-control" value="{{ $project->sale_available_date }}">
                                        </div>
                                    </div>
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

                                <?php if(!empty($launch_date_history_text)) { ?>
                                <div class="media-description text-muted" style="padding: 15px;">
                                    {!! $launch_date_history_text !!}
                                </div>
                                <?php } ?>

                            </div>
                        </div>
                    </div>

                    @if(!empty($request_type_list))

                        <?php foreach ($request_type_list as $task): ?>

                        <div class="card assets_existing">
                            <?php

                            if($task->status == 'action_requested'){
                                $left_border_color = '#28A745';
                            }else if($task->status == 'in_progress'){
                                $left_border_color = '#fbd102';
                            }else if($task->status == 'action_review'){
                                $left_border_color = '#F03C3C';
                            }else if($task->status == 'action_completed'){
                                $left_border_color = '#7e7e7e';
                            }else {
                                $left_border_color = 'white';
                            }

                            ?>
                            <div class="clearfix" id="{{$task->mm_request_type_id}}" style="border-left: 10px solid {{ $left_border_color }}; border-radius: 20px;">

                                <div class="asset--grid-row">

                                    <div class="project-info">
                                        <ul class="project-info-list">
                                            <li><strong>{{ strtoupper(str_replace('_', ' ', $task->sub_task_type)) }} #{{ $task->mm_request_type_id }}</strong> </li>
                                            <?php if ($left_border_color == 'white') $left_border_color = '#6c757d'; ?>
                                            <li><strong>Status: </strong> <b style="color: {{ $left_border_color }};">{{ ucwords(str_replace('_', ' ', $task->status)) }}</b></li>
                                            <?php if($task->due_date_urgent){ ?>
                                            <li><strong>Due Date (Urgent): {{ date('m/d/y', strtotime($task->due_date_urgent)) }}</strong></li>
                                            <?php }else{ ?>
                                            <li><strong>Due Date: {{ date('m/d/y', strtotime($task->due_date)) }}</strong></li>
                                            <?php } ?>
                                        </ul>
                                    </div>

                                    <ul class="project-members-list" style="padding-left: 70px;">

                                        <li><strong>Creator : </strong> <span class="asset-creator-bg">{{ $task->author_name }}</span></li>
                                        <?php if($task->assignee){ ?>
                                        <li><strong>Assignee : </strong> <span class="asset-creator-bg" style="background-color: #dbe0ec;">{{ $task->assignee_name }}</span></li>
                                        <?php } ?>
                                        <?php if($task->due_date_revision){ ?>
                                        <li><strong>Due Date (Revision) : </strong>
                                            <span style="background-color: #eceadb;">{{ date('m/d/y', strtotime($task->due_date_revision)) }}</span>
                                            <span style="background-color: #eceadb; color: #b91d19;">{{ $task->revision_cnt }}</span>
                                        </li>
                                        <?php } ?>
                                    </ul>

                                    <?php if($task->status != 'action_completed'){ ?>
{{--                                    <div class="col-md-12">--}}
{{--                                        <div class="project-action-icons">--}}
{{--                                            <ul class="project-icons">--}}
{{--                                                <li>--}}
{{--                                                    <a  href="javascript:void(0);"--}}
{{--                                                        class="close"--}}
{{--                                                        data-id=""--}}
{{--                                                        data-request-type-id="{{ $task->mm_request_type_id }}"--}}
{{--                                                        data-request-type="{{ $task->sub_task_type }}"--}}
{{--                                                        onclick="delete_mm_request_type($(this));">--}}
{{--                                                        <i class="fa fa-times"></i>--}}
{{--                                                    </a>--}}
{{--                                                </li>--}}
{{--                                            </ul>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
                                    <?php } ?>

                                </div>

                                <div style="text-align: center;">
                                    <i id="arrow-{{$task->mm_request_type_id}}" class="dropdown fa-lg fa fa-angle-down" onclick="click_arrow(this, {{$task->mm_request_type_id}})"></i>
                                </div>

                                <div id="asset-id-{{$task->mm_request_type_id}}" class="box-body form_creator" data-asset-id="{{ $task->mm_request_type_id }}" style="display: none">
                                    <section>
                                        <div class="inner_box">
                                            <?php $data = [$task, $task->files]; ?>
                                            @include('admin.mm_request.'.$task->sub_task_type, $data)
                                        </div>
                                    </section>
                                </div>

                            </div>
                        </div>

                        <?php endforeach; ?>

                    @endif

                    @if(!empty($project))
                        <?php if(Auth::user()->function == 'Product' || Auth::user()->function == 'Admin') {?>

                        <div class="clearfix" id="asset_selector" style="display: block">
                            <div class="card box asset box-primary">
                                <div class="card-header" id="mm_bg" style="height: 500px;">
                                    <div class="row" style="display: flex; justify-content: center; padding: 5px 0 0 0;">
                                        <div id="add_task_btn" style="display: block">
                                            <a class="btn-lg btn-secondary add-row" style="color: #050000; background-color: #cdd3d8; border-radius: 1.25rem;" onclick="click_task_add_btn()">Add <b style="color: #b91d19;">MM REQUEST</b> Type</a>
                                        </div>
                                    </div>

                                    <div id="task_new" class="box-body form_creator" data-asset-id="" style="display: none">
                                        <section>
                                            <div class="inner_box">
                                                <div class="form-group">
                                                    <label style="color: #000000; font-size: 1.2rem;">Request Type: </label>
                                                    <span id="task_type_name" class="task_type_name"></span>
                                                    <span class="float-right">
                                                    <a href="{{ url('admin/mm_request/'. $project->id .'/edit') }}">
                                                        <i class="fa fa-times" style="font-size: 1.5rem;"></i>
                                                    </a>
                                                </span>

                                                    <select name="add_task_type" id="add_request_type" class="form-control" onchange="select_request_type()">
                                                        <option value="">Select</option>
                                                        <option value="new">New</option>
                                                        <option value="update">Update</option>
                                                        <option value="dimensions">Dimensions & Logistics</option>
                                                        <option value="price">Price</option>
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label>
                                                        <a href="https://drive.google.com/drive/folders/1xkBEXtq8A5XAP20FpuoSLUsFxwKQY8Cj?usp=drive_link" target="_blank" class="badge badge-danger">Download Link</a>
                                                    </label>
                                                </div>


                                                <div id="new_new" style="display: none;">
                                                    @include('admin.mm_request.new.new')
                                                </div>
                                                <div id="new_update" style="display: none;">
                                                    @include('admin.mm_request.new.update')
                                                </div>
                                                <div id="new_dimensions" style="display: none;">
                                                    @include('admin.mm_request.new.dimensions')
                                                </div>
                                                <div id="new_price" style="display: none;">
                                                    @include('admin.mm_request.new.price')
                                                </div>
                                            </div>
                                        </section>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <?php } ?>
                    @endif

                </div>

                @if(!empty($project))
                    <div class="col-lg-5">
                        <div class="card">
                            <div class="card-header">
                                <h4>CORRESPONDENCE</h4>
                                <div class="text-right">
                                    <button class="btn btn-primary" id="add_note_btn" onclick="click_add_note_btn()">Add Note</button>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="col">

                                    <section id="add_note" class="notes" style="display: none;">
                                        <div class="write note">
                                            <form method="POST" action="{{ route('mm_request.add_note') }}" enctype="multipart/form-data">
                                                @csrf
                                                <input type="hidden" name="p_id" value="{{ $project->id }}">
                                                <input type="hidden" name="t_id" value="{{ $task_id }}">
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

                @endif

            </div>
        </div>

    </section>

    <script type="text/javascript">

        $(function() {
            $('.summernote_project').summernote('disable');
        });

        const queryString = window.location.href;
        if(queryString.includes('#')) {
            var asset_id = queryString.split('#').pop();
            $('#asset-id-'+asset_id).show();
            $('#arrow-'+asset_id).removeClass('fa-angle-down');
            $('#arrow-'+asset_id).addClass('fa-angle-up');
        }



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

        function select_request_type(){
            request_type = $('#add_request_type option:selected').val();

            if(request_type == 'new'){
                $("#new_new").fadeIn(600);
                $("#new_update").fadeOut();
                $("#new_dimensions").fadeOut();
                $("#new_price").fadeOut();
            }else if(request_type == 'update'){
                $("#new_new").fadeOut();
                $("#new_update").fadeIn(600);
                $("#new_dimensions").fadeOut();
                $("#new_price").fadeOut();
            }else if(request_type == 'dimensions'){
                $("#new_new").fadeOut();
                $("#new_update").fadeOut();
                $("#new_dimensions").fadeIn(600);
                $("#new_price").fadeOut();
            }else if(request_type == 'price'){
                $("#new_new").fadeOut();
                $("#new_update").fadeOut();
                $("#new_dimensions").fadeOut();
                $("#new_price").fadeIn(600);
            }
            $("#mm_bg").height('');
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

        function delete_mm_request_type(el) {
            if (confirm("Are you sure you want to delete? Once deleted, this action cannot be undone and the data will no longer be accessible.") == true) {
                let id = $(el).attr('data-request-type-id');
                let type = $(el).attr('data-request-type');
                $.ajax({
                    url: "<?php echo url('/admin/mm_request/requestTypeRemove'); ?>"+"/"+id+"/"+type,
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
                    url: "<?php echo url('/admin/mm_request/fileRemove'); ?>"+"/"+id,
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

        function action_resubmit(el){

            if (confirm("Ensure all changes are saved before resubmitting.") == true) {
                let id = $(el).attr('data-request-type-id');
                $.ajax({
                    url: "<?php echo url('/admin/mm_request/actionReSubmit'); ?>"+"/"+id,
                    type: "GET",
                    datatype: "json",
                    success: function(response) {
                        if(response != 'fail'){
                            alert('You have successfully Resubmitted the request.');
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
                let id = $(el).attr('data-request-type-id');
                $.ajax({
                    url: "<?php echo url('/admin/mm_request/actionInProgress'); ?>"+"/"+id,
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
                let id = $(el).attr('data-request-type-id');
                $.ajax({
                    url: "<?php echo url('/admin/mm_request/actionReview'); ?>"+"/"+id,
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
                let id = $(el).attr('data-request-type-id');
                $.ajax({
                    url: "<?php echo url('/admin/mm_request/actionComplete'); ?>"+"/"+id,
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
