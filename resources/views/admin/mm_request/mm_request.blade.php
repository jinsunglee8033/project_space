<?php $task_id = $data[0]->task_id; $t_type = $data[0]->type; $t_author = $data[0]->author_id; ?>

<style>

    .asset--grid-row{
        --f: arial;
        --f-size: 15px;
        --light-steal-blue: #dee7ea;
        --prim: #ecdbe8;
        --rvs: #f3ebcd;
        --hosta-flower: #dcdde7;
        font-family: var(--f);
        font-size: var(--f-size);
        background: #fff;
        border-radius: 20px;
        padding: 20px 0px 40px 0;
        /*margin: -14px 0 30px 0;*/
        --copy-writer-bg: var(--light-steal-blue);
        --asset-creator-bg: var(--prim);
        --revision-bg: var(--rvs);
        --assignee-bg: var(--hosta-flower);
        display: grid;
        /*gap: 100px;*/
        grid-template-columns: 245px 1fr 38px;
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
        margin-right: 15px;
    }

    .project-members-list span{
        border-radius: 5px;
        padding: 3px 6px;
    }

    .asset-creator-bg{
        background-color: var(--asset-creator-bg);
    }

    .project-action-icons{
        display: flex;
        justify-content: flex-end;
    }

    .inner_box {
        margin: 0px 15px 0px 15px;
    }

</style>

<div class="inner_box">

    <div class="asset--grid-row">
        <div class="project-info">
            <ul class="project-info-list">
                <li><strong>MM Request #{{$task_id}}</strong> </li>
                <li><strong>Status: </strong>
                    <?php if($task_status == 'action_review'){ ?>
                    <b style="color: #6c757d;">REVISION</b>
                    <?php }else{ ?>
                    <b style="color: #6c757d;">{{ strtoupper(str_replace('_', ' ', $task_status))}}</b>
                    <?php } ?>
                </li>
                <?php if($data[0]->due_date_urgent){ ?>
                <li><strong>Due Date (Urgent): </strong> {{ date('m/d/y', strtotime($data[0]->due_date_urgent)) }}</li>
                <?php }else{ ?>
                <li><strong>Due Date: </strong> {{ date('m/d/y', strtotime($data[0]->due_date)) }}</li>
                <?php } ?>
            </ul>
        </div>

        <ul class="project-members-list">
            <li><strong>Creator : </strong> <span class="asset-creator-bg">{{ $data[2]}}</span></li>
            <?php if($data[0]->assignee){ ?>
            <li><strong>Assignee : </strong> <span class="asset-creator-bg" style="background-color: #dbe0ec;">{{ $data[0]->assignee_obj->first_name }} {{ $data[0]->assignee_obj->last_name }}</span></li>
            <?php } ?>
            <?php if($data[0]->due_date_revision){ ?>
            <li><strong>Due Date (Revision) : </strong>
                <span style="background-color: #eceadb;">{{ date('m/d/y', strtotime($data[0]->due_date_revision)) }}</span>
                <span style="background-color: #eceadb; color: #b91d19;">{{ $data[0]->revision_cnt }}</span>
            </li>
            <?php } ?>
        </ul>

{{--        <div class="col-md-12">--}}
{{--            <div class="project-action-icons">--}}
{{--            </div>--}}
{{--        </div>--}}
    </div>

    <?php if (!empty($task_status) && $task_status == 'action_requested') { ?>
    <?php if(auth()->user()->role == 'Admin' || auth()->user()->team == 'MDM') { ?>
    <div class="form-group">
    <input type="button"
           name="action start"
           value="Start"
           onclick="action_start($(this))"
           data-task-id="<?php echo $task_id; ?>"
           style="margin-top:-5px; font-size: medium;"
           class="btn btn-lg btn-success submit"/>
    <label style="font-size: medium; font-weight:100; color: #b91d19; padding: 22px 0 30px 20px;"><b>* Click to indicate the start of work.</b></label>
    </div>
    <?php } ?>
    <?php }?>

    <form method="POST" action="{{ route('mm_request.edit_mm_request', $task_id) }}" enctype="multipart/form-data">
        @csrf

        <?php if($data[0]->assignee) { ?>
        <div class="form-group">
            <label>Change Assignee:</label>
            <select id="{{ $task_id }}_assignee" class="form-control"
                    name="assignee">
                <option value="">Select</option>
                @foreach ($mm_request_assignee_list as $value)
                    <option value="{{ $value->id }}" {{ $value->id == $data[0]->assignee ? 'selected' : '' }}>
                        {{ $value->first_name }} {{ $value->last_name }}
                    </option>
                @endforeach
            </select>
        </div>
        <?php } ?>

        <div class="form-group">
            <label>Request Type: <a target="_blank" href="https://drive.google.com/drive/folders/1xkBEXtq8A5XAP20FpuoSLUsFxwKQY8Cj?usp=drive_link" >Download Link</a></label>
            <select id="{{ $task_id }}_request_type" class="form-control"
                    name="request_type" required>
                <option value="">Select</option>
                @foreach ($mm_request_types as $value)
                    <option value="{{ $value }}" {{ $value == $data[0]->request_type ? 'selected' : '' }}>
                        {{ $value }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Request Detail: </label>
            <textarea class="form-control" id="remark" name="remark" style="height: 100px;">{{ $data[0]->remark }}</textarea>
        </div>

        <div class="form-group">
            <label>Materials:</label>
            <textarea class="form-control" id="materials" name="materials" style="height: 200px;">{{ $data[0]->materials }}</textarea>
        </div>

        <div class="form-group">
            <label>Priority:</label>
            <select id="{{ $task_id }}_priority" class="form-control"
                    name="priority" required>
                <option value="">Select</option>
                @foreach ($priorities as $value)
                    <option value="{{ $value }}" {{ $value == $data[0]->priority ? 'selected' : '' }}>
                        {{ $value }}
                    </option>
                @endforeach
            </select>
        </div>

        <?php if($data[0]->due_date_urgent) { ?>
        <div class="form-group due_date_urgent">
            <label>Due Date (Urgent):</label>
            <input type="text" name="due_date_urgent" id="{{$task_id}}_due_date_urgent" placeholder="Due Date Urgent"
                   class="form-control @error('due_date_urgent') is-invalid @enderror @if (!$errors->has('due_date_urgent') && old('due_date_urgent')) is-valid @endif"
                   value="{{ old('due_date_urgent', !empty($data[0]) ? $data[0]->due_date_urgent : null) }}">
        </div>

{{--        <div class="form-group">--}}
{{--            <label>Urgent Reason:</label>--}}
{{--            <select id="{{ $task_id }}_urgent_reason" class="form-control"--}}
{{--                    name="priority" required>--}}
{{--                <option value="">Select</option>--}}
{{--                @foreach ($priorities as $value)--}}
{{--                    <option value="{{ $value }}" {{ $value == $data[0]->priority ? 'selected' : '' }}>--}}
{{--                        {{ $value }}--}}
{{--                    </option>--}}
{{--                @endforeach--}}
{{--            </select>--}}
{{--        </div>--}}

        <div class="form-group">
            <label>Urgent Detail:</label>
            <textarea class="form-control" id="urgent_reason" name="urgent_reason" style="height: 100px;">{{ $data[0]->urgent_reason }}</textarea>
        </div>

        <?php } ?>

        <div class="form-group">
            <label>Due Date:</label>
            <input type="text" name="due_date" class="form-control" value="{{ $data[0]->due_date }}" readonly>
        </div>

        <div class="form-group">
            <label>Set-up Plant(s):</label>
            <div class="columns" style="column-count: 3;">
                <?php if ($data[0]->set_up_plant != null) { ?>
                @foreach($mm_request_set_up_plants as $value)
                    <?php $checkbox_fields = explode(', ', $data[0]->set_up_plant); ?>
                    <div class="col-md">
                        <div class="form-check" style="padding-left: 0px;">
                            <input  <?php if (in_array($value->name, $checkbox_fields)) echo "checked" ?>
                                    type="checkbox"
                                    name="set_up_plant[]"
                                    value="{{ $value->name }}"
                            >
                            <label class="form-check-label " for="{{ $value->name }}">
                                {{ $value->name }}
                            </label>
                        </div>
                    </div>
                @endforeach
                <?php }else{ ?>
                @foreach($mm_request_set_up_plants as $value)
                    <?php $checkbox_fields = explode(', ', $data[0]->set_up_plant); ?>
                    <div class="col-md">
                        <div class="form-check" style="padding-left: 0px;">
                            <input  type="checkbox"
                                    name="set_up_plant[]"
                                    value="{{ $value->name }}"
                            >
                            <label class="form-check-label " for="{{ $value->name }}">
                                {{ $value->name }}
                            </label>
                        </div>
                    </div>
                @endforeach
                <?php } ?>
            </div>
        </div>

        <?php if (count($data[1]) > 0): ?>
        <div class="form-group">
            <label>Attachments: </label>
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
            <label><b style="color: #b91d19;">*</b> Attachments: <b style="color: #b91d19">(20MB Max)</b></label>
            <input type="file" data-asset="default" name="p_attachment[]" class="form-control p_attachment last_upload" multiple="multiple"/>
            <a href="javascript:void(0);" onclick="another_upload($(this))" class="another_upload">[ Upload Another ]</a>
        </div>

        <div class="form-group">
            <?php if (!empty($task_status) && $task_status == 'in_progress') { ?>
            <?php if(auth()->user()->role == 'Admin' || auth()->user()->team == 'MDM') { ?>

            <input type="submit" name="submit" value="Save" style="font-size: medium" class="btn btn-lg btn-primary submit"/>
                <label style="font-size: medium; font-weight:100; font-family: sans-serif; color: #b91d19; padding: 0 0 0 20px;">* All changes must be saved before clicking any action buttons. </label>
            <br>
            <input type="button"
                   value="Revision"
                   data-toggle="modal"
                   data-target="#revision_reason_{{$task_id}}"
{{--                   onclick="action_review($(this))"--}}
{{--                   data-task-id="<?php echo $task_id; ?>"--}}
                   style="margin-top:10px; font-size: medium;"
                   class="btn btn-lg btn-revision submit"/>

{{--           Complete button  --}}
            <input type="button"
                   value="Complete"
                   onclick="action_complete($(this))"
                   data-task-id="<?php echo $task_id; ?>"
                   style="margin-top:10px; font-size: medium;"
                   class="btn btn-lg btn-complete submit"/>

            <?php } ?>
            <?php }?>

            <?php if (!empty($task_status) && $task_status == 'action_review') { ?>
            <?php if(auth()->user()->role == 'Admin' || auth()->user()->id == $t_author ) { ?>
                <input type="submit" name="submit" value="Save" style="font-size: medium" class="btn btn-lg btn-primary submit"/>
                <label style="font-size: medium; font-weight:100; font-family: sans-serif; color: #b91d19; padding: 0 0 0 20px;">* All changes must be saved before clicking the 'Resubmit' button.</label>
                <br>
                <input type="button"
                   value="Resubmit"
{{--                       action_reqeust --}}
                   onclick="action_resubmit($(this))"
                   data-task-id="<?php echo $task_id; ?>"
                   style="margin-top:10px; font-size: medium;"
                   class="btn btn-lg btn-resubmit submit"/>
            <?php } ?>
            <?php }?>

        </div>
    </form>
</div>



<?php if (count($data[1])>0): ?>

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
                                        <span aria-hidden="true">
                                          ×
                                      </span>
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

<script type="text/javascript">
    $(function() {

        var today_urgent = "<?php echo $data[0]->due_date_urgent; ?>"
        $('input[name="<?php echo $task_id; ?>_due_date_urgent"]').daterangepicker({
            singleDatePicker: true,
            minDate: today_urgent,
            locale: {
                format: 'YYYY-MM-DD'
            },
        });

    });
</script>

<div class="modal fade"
     id="revision_reason_{{$task_id}}"
     tabindex="-1"
     data-backdrop="false"
     role="dialog"
     aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('mm_request.revision_reason') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="request_type_id" value="{{$task_id}}">
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
