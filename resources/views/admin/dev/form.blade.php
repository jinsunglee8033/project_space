@extends('layouts.dashboard')

@section('content')

    <style>
        .create_note::before {
            white-space: pre;
        }
    </style>


    <section class="section">
        @include('admin.campaign.flash')
        <div class="section-header">
            <h1>Dev Task Management</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ url('admin/dev') }}">KDO Dev Ticket Request Form</a></div>
                <div class="breadcrumb-item">Create a Ticket</div>
            </div>
        </div>

        <div class="section-body">
            <h2 class="section-title">{{ empty($dev) ? 'Create a Task' : 'Update a Task' }}</h2>
            <div class="row">
                <div class="col-lg-6">
                    @if (empty($dev ?? '' ?? ''))
                        <form method="POST" action="{{ route('dev.store') }}" enctype="multipart/form-data">
                    @else
                        <form method="POST" action="{{ route('dev.update', $dev->id) }}" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="{{ $dev->id }}" />
                            @method('PUT')
                    @endif
                    @csrf
                            <div class="card">
                                <div class="card-header">
                                    <h4>{{ empty($dev) ? 'Create a Task' : 'Update a Task' }}</h4>
                                </div>

                                <div class="card-body">

                                    <div class="col">
                                        <div class="form-group">
                                            <label>Title <b style="color: #b91d19">*(required)</b></label>
                                            <input type="text" name="title" required
                                                   class="form-control @error('title') is-invalid @enderror @if (!$errors->has('title') && old('title')) is-valid @endif"
                                                   value="{{ old('title', !empty($dev) ? $dev->title : null) }}">
                                            @error('title')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label class="form-label">Asset Type: <b style="color: #b91d19">*(required)</b></label>
                                            <div class="selectgroup selectgroup-pills">
                                                <?php foreach($types as $key => $value): ?>
                                                    <label class="selectgroup-item">
                                                        <input type="radio" name="type" value="{{ $key }}"
                                                               class="selectgroup-input" {{ $key == old('type', $type) ? 'checked' : '' }} required>
                                                        <span class="selectgroup-button" data-toggle="tooltip"
                                                              data-original-title="{{ $value }}">{{ $key }}</span>
                                                    </label>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>

{{--                                        <div class="form-group">--}}
{{--                                            <label>Type</label>--}}
{{--                                            <select class="form-control" name="type">--}}
{{--                                                <option>Select</option>--}}
{{--                                                @foreach ($types as $value)--}}
{{--                                                    <option value="{{ $value }}" {{ $value == $type ? 'selected' : '' }}>--}}
{{--                                                        {{ $value }}--}}
{{--                                                    </option>--}}
{{--                                                @endforeach--}}
{{--                                            </select>--}}
{{--                                        </div>--}}

                                        <div class="form-group">
                                            <label>Priority <b style="color: #b91d19">*(required)</b></label>
                                            <select class="form-control @error('priority') is-invalid @enderror @if (!$errors->has('priority') && old('priority')) is-valid @endif" name="priority" required>
                                                <option value="">Select</option>
                                                @if(empty(old('priority')))
                                                @foreach ($priorities as $key => $value)
                                                    <option value="{{ $key }}" {{ $key == $priority ? 'selected' : '' }} >
                                                        {{ $value }}
                                                    </option>
                                                @endforeach
                                                @else
                                                @foreach ($priorities as $key => $value)
                                                    <option value="{{ $key }}" {{ old('priority') == $key ? 'selected' : '' }} >
                                                        {{ $value }}
                                                    </option>
                                                @endforeach
                                                @endif
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label>Domain <b style="color: #b91d19">*(required - can choose more than one)</b></label>

                                            <div class="columns" style="column-count: 3;">
                                                <?php if ($domain != null) { ?>
                                                    @foreach($domains as $value)
                                                    <?php $checkbox_fields = explode(',', $domain); ?>
                                                        <div class="col-md">
                                                            <div class="form-check" style="padding-left: 0px;">
                                                                <input  <?php if (in_array($value, $checkbox_fields)) echo "checked" ?>
                                                                        type="checkbox"
                                                                        name="domain[]"
                                                                        value="{{ $value }}"
                                                                >
                                                                <label class="form-check-label " for="{{ $value }}">
                                                                {{ $value }}
                                                                </label>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                <?php }else{ ?>
                                                    @foreach($domains as $value)
                                                        <?php $checkbox_fields = explode(', ', $domain); ?>
                                                        <div class="col-md">
                                                            <div class="form-check" style="padding-left: 0px;">
                                                                <input
                                                                        type="checkbox"
                                                                        name="domain[]"
                                                                        value="{{ $value }}"
                                                                >
                                                                <label class="form-check-label " for="{{ $value }}">
                                                                    {{ $value }}
                                                                </label>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                <?php } ?>
                                            </div>

                                        </div>

                                        <div class="form-group">
                                            <label>Description: <b style="color: #b91d19">*(required)</b></label>
                                            {!! Form::textarea('description', !empty($dev) ? $dev->description : null, ['class' => 'form-control summernote']) !!}
                                            @error('description')
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
                                            <label>Upload Visual References: <b style="color: #b91d19">(20MB Max)<br>Please add detailed screenshots as much as possible to aid in the understanding of the issue by the developers.</b></label>
                                            <input type="file" id="c_attachment[]" name="c_attachment[]"
                                                   data-asset="default" multiple="multiple"
                                                   class="form-control c_attachment last_upload @error('c_attachment') is-invalid @enderror @if (!$errors->has('c_attachment') && old('c_attachment')) is-valid @endif"
                                                   value="{{ old('c_attachment', !empty($dev) ? $dev->id : null) }}">
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

                                    <?php if( ($dev_status == 'dev_to_do') && (auth()->user()->role=='admin' || auth()->user()->role=='developer' || auth()->user()->role=='developer manager') ) { ?>
                                    <button class="btn btn-success"
                                            to-do-id="<?php echo $dev->id; ?>"
                                            onclick="dev_work_start($(this))">
                                        Dev Start
                                    </button>
                                    <?php } ?>

                                    <?php if($dev_status == 'dev_in_progress' && (auth()->user()->role=='admin' || auth()->user()->role=='developer' || auth()->user()->role=='developer manager') ) { ?>
                                    <button class="btn btn-info"
                                            in-progress-id="<?php echo $dev->id; ?>"
                                            onclick="dev_work_finish($(this))">
                                        Dev Review
                                    </button>
                                    <?php } ?>

                                    <?php if($dev_status == 'dev_review' && (auth()->user()->role=='admin' || auth()->user()->id == $dev->request_by ) ) { ?>
                                    <button class="btn btn-info"
                                            review-id="<?php echo $dev->id; ?>"
                                            onclick="dev_work_done($(this))">
                                        Ticket Close
                                    </button>
                                    <?php } ?>

                                    <?php if($dev_status == null || $dev_status == 'dev_requested') { ?>
                                    <button class="btn btn-primary">
                                        {{ empty($dev) ? 'Create' : 'Update' }}
                                    </button>
                                    <?php } ?>

                                </div>



                            </div>

                        </form>

                    @if((!empty($dev)) && (auth()->user()->role == 'developer manager'))

                        <div class="card">
                            <form method="POST" action="{{ route('dev.assign') }}" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="d_id" value="{{ $dev->id }}">
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>Assign Developer</label>
                                        <select class="form-control" name="developer">
                                            <option value="">Select</option>
                                            @foreach ($developers as $developer)
                                                <option value="{{ $developer->id }}" {{ $developer->id == $dev->assign_to ? 'selected' : '' }}>
                                                    {{ $developer->first_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <input type="hidden" name="dev_id" value="{{ $dev->dev_id }}">

                                <div class="card-footer text-right">
                                    <button class="btn btn-danger">Assign</button>
                                </div>
                            </form>
                        </div>

                    @endif
                </div>


                @if(!empty($dev))
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
                                <section id="add_note" class="notes" style="display: none;">
                                    <div class="write note">
                                        <form method="POST" action="{{ route('dev.dev_add_note') }}" enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" name="d_id" value="{{ $dev->id }}">
                                            <input type="hidden" name="d_title" value="{{ $dev->title }}">
                                            <input type="hidden" id="email_list" name="email_list" value="">

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

    <script>
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
                    url: "<?php echo url('/admin/dev/fileRemove'); ?>"+"/"+id,
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


    </script>

    <script>
        function click_add_note_btn(){
            $("#add_note_btn").hide();
            $("#add_note").slideDown();

        }

        function click_cancel_note_btn(){
            $("#add_note_btn").show();
            $("#add_note").slideUp();
        }

        function dev_work_start(el){

            if (confirm("Are you sure to Dev In Progress?") == true) {
                let id = $(el).attr('to-do-id');
                $.ajax({
                    url: "<?php echo url('/admin/dev/dev_in_progress'); ?>"+"/"+id,
                    type: "GET",
                    datatype: "json",
                    success: function(response) {
                        if(response != 'fail'){
                            alert('Success!');
                            // window.location.reload(response);
                            $(el).remove();
                        }else{
                            alert('Error!');
                        }
                    },
                })
            }
        }

        function dev_work_finish(el){

            if (confirm("Are you sure to Dev Review?") == true) {
                let id = $(el).attr('in-progress-id');
                $.ajax({
                    url: "<?php echo url('/admin/dev/dev_review'); ?>"+"/"+id,
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

        function dev_work_done(el){

            if (confirm("Are you sure to Dev Finish?") == true) {
                let id = $(el).attr('review-id');
                $.ajax({
                    url: "<?php echo url('/admin/dev/dev_done'); ?>"+"/"+id,
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
