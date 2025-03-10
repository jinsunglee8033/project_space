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
            <h1>RA Request</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ url('admin/project') }}">RA Request</a></div>
                <div class="breadcrumb-item active">RA Request</div>
            </div>
        </div>

        <div class="section-body">
            <h2 class="section-title">RA Request</h2>
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
                                <div class="clearfix" id="{{$task->qra_request_type_id}}" style="border-left: 10px solid {{ $left_border_color }}; border-radius: 20px;">

                                    <div class="asset--grid-row">

                                        <div class="project-info">
                                            <ul class="project-info-list">
                                                <li><strong>{{ strtoupper(str_replace('_', ' ', $task->request_type)) }} #{{ $task->qra_request_type_id }}</strong> </li>
                                                <?php if ($left_border_color == 'white') $left_border_color = '#6c757d'; ?>
                                                <li><strong>Status: </strong> <b style="color: {{ $left_border_color }};">{{ ucwords(str_replace('_', ' ', $task->status)) }}</b></li>
                                            </ul>
                                        </div>

                                        <ul class="project-members-list" style="padding-left: 70px;">

                                            <li><strong>Creator : </strong> <span class="asset-creator-bg">{{ $task->first_name }} {{ $task->last_name }}</span></li>
                                            <li><strong>Created :</strong>
                                                <span>{{ date('m-d-Y H:i', strtotime($task->created_at)) }}</span>
                                            </li>
                                        </ul>

                                        <div class="col-md-12">
                                            <div class="project-action-icons">
                                                <ul class="project-icons">
                                                    <li>
                                                        <a  href="javascript:void(0);"
                                                        class="close"
                                                        data-id=""
                                                        data-qra-request-type-id="{{ $task->qra_request_type_id }}"
                                                        data-qra-request-type="{{ $task->request_type }}"
                                                        onclick="delete_qra_request_type($(this));">
                                                        <i class="fa fa-times"></i>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>

                                    </div>

                                    <div style="text-align: center;">
                                        <i id="arrow-{{$task->qra_request_type_id}}" class="dropdown fa-lg fa fa-angle-down" onclick="click_arrow(this, {{$task->qra_request_type_id}})"></i>
                                    </div>

                                    <div id="asset-id-{{$task->qra_request_type_id}}" class="box-body form_creator" data-asset-id="{{ $task->qra_request_type_id }}" style="display: none">
                                        <section>
                                            <div class="inner_box">
                                                <?php $data = [$task, $task->files]; ?>
                                                @include('admin.qra_request.'.$task->request_type, $data)
                                            </div>
                                        </section>
                                    </div>

                                </div>
                            </div>

                        <?php endforeach; ?>

                    @endif

                    @if(!empty($project))

                        <div class="clearfix" id="asset_selector" style="display: block">
                        <div class="card box asset box-primary">
                            <div class="card-header">
                                <div class="row" style="display: flex; justify-content: center; padding: 5px 0 0 0;">
                                    <div id="add_task_btn" style="display: block">
                                        <a class="btn-lg btn-secondary add-row" style="color: #050000; background-color: #cdd3d8; border-radius: 1.25rem;" onclick="click_task_add_btn()">Add <b style="color: #b91d19;">RA REQUEST</b> Type</a>
                                    </div>
                                </div>

                                <div id="task_new" class="box-body form_creator" data-asset-id="" style="display: none">
                                    <section>
                                        <div class="inner_box">
                                            <div class="form-group">
                                                <label style="color: #000000; font-size: 1.2rem;">Request Type: </label>
                                                <span id="task_type_name" class="task_type_name"></span>
                                                <span class="float-right">
                                                    <a href="{{ url('admin/qra_request/'. $project->id .'/edit') }}">
                                                        <i class="fa fa-times" style="font-size: 1.5rem;"></i>
                                                    </a>
                                                </span>

                                                <select name="add_task_type" id="add_request_type" class="form-control" onchange="select_request_type()">
                                                    <option value="">Select</option>
                                                    <option value="formulation_prescreen">Formulation Prescreen</option>
                                                    <option value="artwork_contents">Artwork Contents</option>
                                                    <option value="artwork_review">Artwork Review</option>
                                                    <option value="registration_wercs">Registration (WERCS)</option>
                                                    <option value="registration_smarterx">Registration (SmarterX)</option>
                                                    <option value="registration_california">Registration (California)</option>
                                                    <option value="registration_cnf">Registration (CNF)</option>
                                                    <option value="registration_cpnp">Registration (CPNP)</option>
                                                    <option value="registration_scpn">Registration (SCPN)</option>
                                                    <option value="registration_iio">Registration (IIO)</option>
                                                    <option value="registration_mocra">Registration (MoCRA)</option>
                                                    <option value="document_support">Document Support</option>
                                                </select>

                                            </div>

                                            <div id="new_formulation_prescreen" style="display: none;">
                                                @include('admin.qra_request.new.formulation_prescreen')
                                            </div>
                                            <div id="new_artwork_contents" style="display: none;">
                                                @include('admin.qra_request.new.artwork_contents')
                                            </div>
                                            <div id="new_artwork_review" style="display: none;">
                                                @include('admin.qra_request.new.artwork_review')
                                            </div>
                                            <div id="new_registration_wercs" style="display: none;">
                                                @include('admin.qra_request.new.registration_wercs')
                                            </div>
                                            <div id="new_registration_smarterx" style="display: none;">
                                                @include('admin.qra_request.new.registration_smarterx')
                                            </div>
                                            <div id="new_registration_california" style="display: none;">
                                                @include('admin.qra_request.new.registration_california')
                                            </div>
                                            <div id="new_registration_cnf" style="display: none;">
                                                @include('admin.qra_request.new.registration_cnf')
                                            </div>
                                            <div id="new_registration_cpnp" style="display: none;">
                                                @include('admin.qra_request.new.registration_cpnp')
                                            </div>
                                            <div id="new_registration_scpn" style="display: none;">
                                                @include('admin.qra_request.new.registration_scpn')
                                            </div>
                                            <div id="new_registration_iio" style="display: none;">
                                                @include('admin.qra_request.new.registration_iio')
                                            </div>
                                            <div id="new_registration_mocra" style="display: none;">
                                                @include('admin.qra_request.new.registration_mocra')
                                            </div>
                                            <div id="new_document_support" style="display: none;">
                                                @include('admin.qra_request.new.document_support')
                                            </div>
                                        </div>
                                    </section>
                                </div>

                            </div>
                        </div>
                    </div>
                    @endif

                </div>

                @if(!empty($project))
                <div class="col-lg-5">
                    <div class="card">
                        <div class="card-header">
                            <h4>CORRESPONDENCE</h4>
{{--                            <div class=" text-right">--}}
{{--                                <button class="btn btn-primary" id="add_note_btn" onclick="click_add_note_btn()">Add Note</button>--}}
{{--                            </div>--}}
                        </div>

                        <div class="card-body">
                            <div class="col">
{{--                                <section id="add_note" class="notes" style="display: none;">--}}

{{--                                    <div class="write note">--}}
{{--                                        <form method="POST" action="{{ route('asset.asset_add_note') }}" enctype="multipart/form-data">--}}
{{--                                            @csrf--}}
{{--                                            <select name="exist_assets" id="exist_assets" style="font-size: large; width: 50%; background-color: #c4c4c4" onchange="select_note_asset()">--}}
{{--                                                <option value="">Select Asset</option>--}}
{{--                                                <?php foreach ($assets as $asset): ?>--}}
{{--                                                <option value="{{ucwords(str_replace('_', ' ', $asset->a_type))}} #{{ $asset->a_id }}">{{ucwords(str_replace('_', ' ', $asset->a_type))}} #{{ $asset->a_id }}</option>--}}
{{--                                                <?php endforeach; ?>--}}
{{--                                            </select>--}}
{{--                                            <input type="hidden" name="c_id" value="{{ $project->id }}">--}}
{{--                                            <input type="hidden" id="email_list" name="email_list" value="">--}}
{{--                                            <input type="hidden" name="c_title" value="{{ $project->name }}">--}}

{{--                                            <textarea id="create_note" name="create_note" class="wysiwyg"></textarea>--}}
{{--                                            <div id="at_box" style="display: none">--}}
{{--                                                <input class="form-control" placeholder="Name" type="text"/>--}}
{{--                                            </div>--}}
{{--                                            <div class=" text-right">--}}
{{--                                                <button type="button" class="btn btn-primary" onclick="click_cancel_note_btn()">Cancel</button>--}}
{{--                                                <button type="submit" class="btn btn-primary">Send Note</button>--}}
{{--                                            </div>--}}
{{--                                        </form>--}}
{{--                                    </div>--}}
{{--                                </section>--}}

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

{{--    @if(!empty($assets))--}}
{{--        <?php foreach ($assets as $asset): ?>--}}
{{--            <div class="modal fade" id="myModal-{{$asset->a_id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">--}}
{{--                <div class="modal-dialog modal-xl" role="document">--}}

{{--                    <div class="modal-content">--}}

{{--                        <form method="POST" action="{{ route('asset.asset_notification_user') }}" enctype="multipart/form-data">--}}
{{--                            @csrf--}}

{{--                            <input type="hidden" name="a_id" value="{{ $asset->a_id }}">--}}
{{--                            <input type="hidden" name="c_id" value="{{ $project->id }}">--}}

{{--                            <div class="modal-header">--}}
{{--                                <h4 class="modal-title" id="myModalLabel">Notification List - {{ ucwords(str_replace('_', ' ', $asset->a_type)) }} #{{ $asset->a_id }} </h4>--}}
{{--                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>--}}
{{--                            </div>--}}
{{--                            <div class="modal-body">--}}
{{--                                <div class="form-group">--}}
{{--                                    <label style="color: #b91d19; font-size: medium">Do not need to choose Project & Asset Creators since they will receive all notifications of the project</label>--}}
{{--                                    <div class="columns" style="column-count: 4;">--}}
{{--                                        <?php if (isset($users)): ?>--}}
{{--                                            <?php if (isset($asset->asset_notification_user[0]->user_id_list)) { ?>--}}

{{--                                                @foreach($users as $user)--}}
{{--                                                <?php $checkbox_fields = explode(', ', $asset->asset_notification_user[0]->user_id_list); ?>--}}
{{--                                                        <div class="col-md">--}}
{{--                                                            <div class="form-check">--}}
{{--                                                                <input  <?php if (in_array($user->id, $checkbox_fields)) echo "checked" ?>--}}
{{--                                                                        type="checkbox"--}}
{{--                                                                        name="user_id_list[]"--}}
{{--                                                                        value="{{ $user->id }}"--}}
{{--                                                                >--}}
{{--                                                                <label class="form-check-label " for="{{ $user->id }}">--}}
{{--                                                                    {{ $user->first_name }} {{ $user->last_name }}--}}
{{--                                                                </label>--}}
{{--                                                            </div>--}}
{{--                                                        </div>--}}
{{--                                                @endforeach--}}
{{--                                            <?php }else{ ?>--}}
{{--                                                @foreach($users as $user)--}}
{{--                                                    <div class="col-md">--}}
{{--                                                        <div class="form-check">--}}
{{--                                                            <input--}}
{{--                                                                type="checkbox"--}}
{{--                                                                name="user_id_list[]"--}}
{{--                                                                value="{{ $user->id }}"--}}
{{--                                                            >--}}
{{--                                                            <label class="form-check-label " for="{{ $user->id }}">--}}
{{--                                                                {{ $user->first_name }} {{ $user->last_name }}--}}
{{--                                                            </label>--}}
{{--                                                        </div>--}}
{{--                                                    </div>--}}
{{--                                                @endforeach--}}
{{--                                            <?php } ?>--}}
{{--                                        <?php endif; ?>--}}
{{--                                    </div>--}}

{{--                                </div>--}}

{{--                            </div>--}}
{{--                            <div class="modal-footer">--}}
{{--                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>--}}
{{--                                <button type="submit" class="btn btn-primary">Save changes</button>--}}
{{--                            </div>--}}

{{--                        </form>--}}

{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}


{{--            <div class="modal fade" id="asset-owner-{{$asset->a_id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">--}}
{{--                <div class="modal-dialog modal-xl" role="document">--}}

{{--                    <div class="modal-content">--}}

{{--                        <form method="POST" action="{{ route('asset.asset_owner_change') }}" enctype="multipart/form-data">--}}
{{--                            @csrf--}}
{{--                            <input type="hidden" name="a_id" value="{{ $asset->a_id }}">--}}
{{--                            <input type="hidden" name="c_id" value="{{ $project->id }}">--}}
{{--                            <div class="modal-header">--}}
{{--                                <h4 class="modal-title" id="myModalLabel">Change Asset Creator - {{ ucwords(str_replace('_', ' ', $asset->a_type)) }} #{{ $asset->a_id }} </h4>--}}
{{--                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>--}}
{{--                            </div>--}}
{{--                            <div class="modal-body">--}}
{{--                                <div class="form-group">--}}
{{--                                    <div class="columns" style="column-count: 4;">--}}
{{--                                        <?php if (isset($users)): ?>--}}
{{--                                            @foreach($users as $user)--}}
{{--                                                <div class="col-md">--}}
{{--                                                    <div class="form-check">--}}
{{--                                                        <input  <?php if ($user->id == $asset->asset_creator_id) echo "checked" ?>--}}
{{--                                                                type="radio"--}}
{{--                                                                name="author_id"--}}
{{--                                                                value="{{ $user->id }},{{ $user->first_name }}"--}}
{{--                                                        >--}}
{{--                                                        <label class="form-check-label " for="{{ $user->id }}">--}}
{{--                                                            {{ $user->first_name }} {{ $user->last_name }}--}}
{{--                                                        </label>--}}
{{--                                                    </div>--}}
{{--                                                </div>--}}
{{--                                            @endforeach--}}
{{--                                        <?php endif; ?>--}}
{{--                                    </div>--}}

{{--                                </div>--}}

{{--                            </div>--}}
{{--                            <div class="modal-footer">--}}
{{--                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>--}}
{{--                                <button type="submit" class="btn btn-primary">Save changes</button>--}}
{{--                            </div>--}}

{{--                        </form>--}}

{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}

{{--        <?php endforeach; ?>--}}
{{--    @endif--}}

{{--    <?php if (!empty($attach_files)): ?>--}}
{{--        <?php foreach ($attach_files as $attachment): ?>--}}
{{--            <div class="modal fade"--}}
{{--                 id="exampleModal_<?php echo $attachment['attachment_id']; ?>"--}}
{{--                 tabindex="-1"--}}
{{--                 role="dialog"--}}
{{--                 aria-labelledby="exampleModalLabel"--}}
{{--                 aria-hidden="true">--}}
{{--                <div class="modal-dialog"--}}
{{--                     role="document">--}}
{{--                    <div class="modal-content">--}}
{{--                        <div class="modal-header">--}}
{{--                            <button type="button"--}}
{{--                                    class="close"--}}
{{--                                    data-dismiss="modal"--}}
{{--                                    aria-label="Close">--}}
{{--                                    <span aria-hidden="true">--}}
{{--                                      ×--}}
{{--                                  </span>--}}
{{--                            </button>--}}
{{--                        </div>--}}
{{--                        <!--Modal body with image-->--}}
{{--                        <?php $name = explode('/', $attachment['attachment']); ?>--}}
{{--                        <?php $name = $name[count($name)-1]; ?>--}}
{{--                        <div class="modal-title text-lg-center">{{ $name }}</div>--}}
{{--                        <div class="modal-body">--}}
{{--                            <img class="img-fluid" src="<?php echo '/storage' . $attachment['attachment']; ?>">--}}
{{--                        </div>--}}
{{--                        <div class="modal-footer">--}}
{{--                            <button type="button"--}}
{{--                                    class="btn btn-primary"--}}
{{--                                    data-dismiss="modal"--}}
{{--                                    onclick="open_download('<?php echo '/storage' . $attachment['attachment']; ?>')"--}}
{{--                            >--}}
{{--                                Download--}}
{{--                            </button>--}}
{{--                            <button type="button"--}}
{{--                                    class="btn btn-danger"--}}
{{--                                    data-dismiss="modal">--}}
{{--                                Close--}}
{{--                            </button>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        <?php endforeach; ?>--}}
{{--    <?php endif; ?>--}}

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

            if(request_type == 'formulation_prescreen'){
                $("#new_formulation_prescreen").fadeIn(600);
                $("#new_artwork_contents").fadeOut();
                $("#new_artwork_review").fadeOut();
                $("#new_registration_wercs").fadeOut();
                $("#new_registration_smarterx").fadeOut();
                $("#new_registration_california").fadeOut();
                $("#new_registration_cnf").fadeOut();
                $("#new_registration_cpnp").fadeOut();
                $("#new_registration_scpn").fadeOut();
                $("#new_registration_iio").fadeOut();
                $("#new_registration_mocra").fadeOut();
                $("#new_document_support").fadeOut();
            }else if(request_type == 'artwork_contents'){
                $("#new_formulation_prescreen").fadeOut();
                $("#new_artwork_contents").fadeIn(600);
                $("#new_artwork_review").fadeOut();
                $("#new_registration_wercs").fadeOut();
                $("#new_registration_smarterx").fadeOut();
                $("#new_registration_california").fadeOut();
                $("#new_registration_cnf").fadeOut();
                $("#new_registration_cpnp").fadeOut();
                $("#new_registration_scpn").fadeOut();
                $("#new_registration_iio").fadeOut();
                $("#new_registration_mocra").fadeOut();
                $("#new_document_support").fadeOut();
            }else if(request_type == 'artwork_review'){
                $("#new_formulation_prescreen").fadeOut();
                $("#new_artwork_contents").fadeOut();
                $("#new_artwork_review").fadeIn(600);
                $("#new_registration_wercs").fadeOut();
                $("#new_registration_smarterx").fadeOut();
                $("#new_registration_california").fadeOut();
                $("#new_registration_cnf").fadeOut();
                $("#new_registration_cpnp").fadeOut();
                $("#new_registration_scpn").fadeOut();
                $("#new_registration_iio").fadeOut();
                $("#new_registration_mocra").fadeOut();
                $("#new_document_support").fadeOut();
            }else if(request_type == 'registration_wercs'){
                $("#new_formulation_prescreen").fadeOut();
                $("#new_artwork_contents").fadeOut();
                $("#new_artwork_review").fadeOut();
                $("#new_registration_wercs").fadeIn(600);
                $("#new_registration_smarterx").fadeOut();
                $("#new_registration_california").fadeOut();
                $("#new_registration_cnf").fadeOut();
                $("#new_registration_cpnp").fadeOut();
                $("#new_registration_scpn").fadeOut();
                $("#new_registration_iio").fadeOut();
                $("#new_registration_mocra").fadeOut();
                $("#new_document_support").fadeOut();
            }else if(request_type == 'registration_smarterx'){
                $("#new_formulation_prescreen").fadeOut();
                $("#new_artwork_contents").fadeOut();
                $("#new_artwork_review").fadeOut();
                $("#new_registration_wercs").fadeOut();
                $("#new_registration_smarterx").fadeIn(600);
                $("#new_registration_california").fadeOut();
                $("#new_registration_cnf").fadeOut();
                $("#new_registration_cpnp").fadeOut();
                $("#new_registration_scpn").fadeOut();
                $("#new_registration_iio").fadeOut();
                $("#new_registration_mocra").fadeOut();
                $("#new_document_support").fadeOut();
            }else if(request_type == 'registration_california'){
                $("#new_formulation_prescreen").fadeOut();
                $("#new_artwork_contents").fadeOut();
                $("#new_artwork_review").fadeOut();
                $("#new_registration_wercs").fadeOut();
                $("#new_registration_smarterx").fadeOut();
                $("#new_registration_california").fadeIn(600);
                $("#new_registration_cnf").fadeOut();
                $("#new_registration_cpnp").fadeOut();
                $("#new_registration_scpn").fadeOut();
                $("#new_registration_iio").fadeOut();
                $("#new_registration_mocra").fadeOut();
                $("#new_document_support").fadeOut();
            }else if(request_type == 'registration_cnf'){
                $("#new_formulation_prescreen").fadeOut();
                $("#new_artwork_contents").fadeOut();
                $("#new_artwork_review").fadeOut();
                $("#new_registration_wercs").fadeOut();
                $("#new_registration_smarterx").fadeOut();
                $("#new_registration_california").fadeOut();
                $("#new_registration_cnf").fadeIn(600);
                $("#new_registration_cpnp").fadeOut();
                $("#new_registration_scpn").fadeOut();
                $("#new_registration_iio").fadeOut();
                $("#new_registration_mocra").fadeOut();
                $("#new_document_support").fadeOut();
            }else if(request_type == 'registration_cpnp'){
                $("#new_formulation_prescreen").fadeOut();
                $("#new_artwork_contents").fadeOut();
                $("#new_artwork_review").fadeOut();
                $("#new_registration_wercs").fadeOut();
                $("#new_registration_smarterx").fadeOut();
                $("#new_registration_california").fadeOut();
                $("#new_registration_cnf").fadeOut();
                $("#new_registration_cpnp").fadeIn(600);
                $("#new_registration_scpn").fadeOut();
                $("#new_registration_iio").fadeOut();
                $("#new_registration_mocra").fadeOut();
                $("#new_document_support").fadeOut();
            }else if(request_type == 'registration_scpn'){
                $("#new_formulation_prescreen").fadeOut();
                $("#new_artwork_contents").fadeOut();
                $("#new_artwork_review").fadeOut();
                $("#new_registration_wercs").fadeOut();
                $("#new_registration_smarterx").fadeOut();
                $("#new_registration_california").fadeOut();
                $("#new_registration_cnf").fadeOut();
                $("#new_registration_cpnp").fadeOut();
                $("#new_registration_scpn").fadeIn(600);
                $("#new_registration_iio").fadeOut();
                $("#new_registration_mocra").fadeOut();
                $("#new_document_support").fadeOut();
            }else if(request_type == 'registration_iio'){
                $("#new_formulation_prescreen").fadeOut();
                $("#new_artwork_contents").fadeOut();
                $("#new_artwork_review").fadeOut();
                $("#new_registration_wercs").fadeOut();
                $("#new_registration_smarterx").fadeOut();
                $("#new_registration_california").fadeOut();
                $("#new_registration_cnf").fadeOut();
                $("#new_registration_cpnp").fadeOut();
                $("#new_registration_scpn").fadeOut();
                $("#new_registration_iio").fadeIn(600);
                $("#new_registration_mocra").fadeOut();
                $("#new_document_support").fadeOut();
            }else if(request_type == 'registration_mocra'){
                $("#new_formulation_prescreen").fadeOut();
                $("#new_artwork_contents").fadeOut();
                $("#new_artwork_review").fadeOut();
                $("#new_registration_wercs").fadeOut();
                $("#new_registration_smarterx").fadeOut();
                $("#new_registration_california").fadeOut();
                $("#new_registration_cnf").fadeOut();
                $("#new_registration_cpnp").fadeOut();
                $("#new_registration_scpn").fadeOut();
                $("#new_registration_iio").fadeOut();
                $("#new_registration_mocra").fadeIn(600);
                $("#new_document_support").fadeOut();
            }else if(request_type == 'document_support'){
                $("#new_formulation_prescreen").fadeOut();
                $("#new_artwork_contents").fadeOut();
                $("#new_artwork_review").fadeOut();
                $("#new_registration_wercs").fadeOut();
                $("#new_registration_smarterx").fadeOut();
                $("#new_registration_california").fadeOut();
                $("#new_registration_cnf").fadeOut();
                $("#new_registration_cpnp").fadeOut();
                $("#new_registration_scpn").fadeOut();
                $("#new_registration_iio").fadeOut();
                $("#new_registration_mocra").fadeOut();
                $("#new_document_support").fadeIn(600);
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

        function delete_qra_request_type(el) {
            if (confirm("Are you sure to Delete?") == true) {
                let id = $(el).attr('data-qra-request-type-id');
                let type = $(el).attr('data-qra-request-type');
                $.ajax({
                    url: "<?php echo url('/admin/qra_request/requestTypeRemove'); ?>"+"/"+id+"/"+type,
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
                    url: "<?php echo url('/admin/qra_request/fileRemove'); ?>"+"/"+id,
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
                let id = $(el).attr('data-request-type-id');
                $.ajax({
                    url: "<?php echo url('/admin/qra_request/actionInProgress'); ?>"+"/"+id,
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
                    url: "<?php echo url('/admin/qra_request/actionReview'); ?>"+"/"+id,
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
                    url: "<?php echo url('/admin/qra_request/actionComplete'); ?>"+"/"+id,
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


        function task_done(el){

            if (confirm("Are you sure to Done?") == true) {
                let id = $(el).attr('data-task-id');
                $.ajax({
                    url: "<?php echo url('/admin/task/done'); ?>"+"/"+id,
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
