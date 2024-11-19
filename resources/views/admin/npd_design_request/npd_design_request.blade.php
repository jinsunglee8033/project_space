<?php $npd_design_request_type_id = $data[0]->npd_design_request_type_id; $request_type = $data[0]->request_type; $t_author = $data[0]->author_id; ?>

<form method="POST" action="{{ route('npd_design_request.edit_npd_design_request', $npd_design_request_type_id) }}" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="t_id" value="{{ $task_id }}" />
    <input type="hidden" name="request_type" value="{{ $request_type }}" />
    <input type="hidden" name="author_id" value="{{ Auth::user()->id }}" />

    <?php if (!empty($data[0]) && $data[0]->status == 'action_requested') { ?>
    <?php if(auth()->user()->role == 'Admin'
            || (auth()->user()->role == 'Team Lead' && auth()->user()->function== 'Design')
            || (auth()->user()->role == 'Team Lead' && auth()->user()->team== 'Brand Design')
            || (auth()->user()->role == 'Team Lead' && auth()->user()->team== 'Production Design')
            || (auth()->user()->role == 'Team Lead' && auth()->user()->team== 'Industrial Design')
            || (auth()->user()->role == 'Team Lead' && auth()->user()->team== 'Production Design Design')
    ) { ?>
    <div style="background-color: #e9e9e9; padding: 10px 10px 10px 10px; margin: 0 0 30px 0; border-radius: 25px;">
        <div class="form-group">
            <label style="color: #b91d19;">Assignee: (Only for Design Directors)</label>
            <select id="assignee" class="form-control" name="assignee">
                <option value="">Select</option>

                @foreach ($data[3] as $value)
                    <option value="{{ $value->id }}" {{ $value->id == $data[0]->assignee ? 'selected' : '' }}>
                        {{ $value->first_name }} {{ $value->last_name }}
                    </option>
                @endforeach
            </select>
        </div>
        <input type="submit" name="submit" value="Save" style="margin-top:10px; font-size: large" class="btn btn-lg btn-primary submit"/>
    </div>

    <?php } ?>
    <?php } ?>


    <div class="npd_design_request">

        <?php if (!empty($data[0]) && $data[0]->status == 'action_requested') { ?>
        <?php if( (auth()->user()->role == 'Admin' || auth()->user()->function == 'Design')
        || (auth()->user()->team == 'Brand Design' || auth()->user()->team == 'Production Design' || auth()->user()->team == 'Industrial Design') ) { ?>

            <div style="margin: -20px 0 0px 10px; padding: 0px 0 0px 0;">
                <input type="button"
                       name="action start"
                       value="Start"
                       onclick="action_start($(this))"
                       data-request-type-id="<?php echo $npd_design_request_type_id; ?>"
                       style="margin-top:10px; float:left; font-size: medium;"
                       class="btn btn-lg btn-success submit"/>
                <label style="font-size: medium; font-weight:100; color: #b91d19; padding: 22px 0 0 20px;"><b>* Click to indicate the start of work.</b></label>
            </div>

        <?php }?>
        <?php }?>


            <p style="color: #222; text-align: center; font-weight: bold;">{{ $data[0]->request_type }}</p>

            <div class="form-group">
                <label>Request Type:</label>
                <input type="text" name="request_type" class="form-control" readonly value="{{ $data[0]->request_type }}" required>
            </div>

            <div class="form-group">
                <label>Objective:</label>
                <textarea class="form-control" id="{{$task_id}}_objective" name="objective" style="height: 100px;" required>{{ $data[0]->objective }}</textarea>
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
                <label>Due Date (Urgent): <b style="color: #b91d19">(Select from the calendar)</b></label>
                <input type="text" name="due_date_urgent" id="{{$task_id}}_due_date_urgent" placeholder="Due Date Urgent"
                       class="form-control @error('due_date_urgent') is-invalid @enderror @if (!$errors->has('due_date_urgent') && old('due_date_urgent')) is-valid @endif"
                       value="{{ old('due_date_urgent', !empty($data[0]) ? $data[0]->due_date_urgent : null) }}">
            </div>

            <div class="form-group">
                <label>Urgent Reason:</label>
                <textarea class="form-control" id="urgent_reason" name="urgent_reason" style="height: 100px;">{{ $data[0]->urgent_reason }}</textarea>
            </div>

            <?php } ?>

            <div class="form-group">
                <label>Due Date:</label>
                <input type="text" name="due_date" class="form-control" value="{{ $data[0]->due_date }}" readonly>
            </div>

            <div class="form-group">
                <label>Scope:</label>
                <select id="{{ $task_id }}_scope" class="form-control"
                        name="scope" required>
                    <option value="">Select</option>
                    @foreach ($scope_list as $value)
                        <option value="{{ $value }}" {{ $value == $data[0]->scope ? 'selected' : '' }}>
                            {{ $value }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Artwork Type:</label>
                <select id="{{ $task_id }}_artwork_type" class="form-control"
                        name="artwork_type" required>
                    <option value="">Select</option>
                    @foreach ($artwork_type_list as $value)
                        <option value="{{ $value }}" {{ $value == $data[0]->artwork_type ? 'selected' : '' }}>
                            {{ $value }}
                        </option>
                    @endforeach
                </select>
            </div>

{{--            <div class="form-group">--}}
{{--                <label>Sales Channel:</label>--}}
{{--                <select id="{{ $task_id }}_sales_channel" class="form-control"--}}
{{--                        name="sales_channel" required>--}}
{{--                    <option value="">Select</option>--}}
{{--                    @foreach ($sales_channel_list as $value)--}}
{{--                        <option value="{{ $value }}" {{ $value == $data[0]->sales_channel ? 'selected' : '' }}>--}}
{{--                            {{ $value }}--}}
{{--                        </option>--}}
{{--                    @endforeach--}}
{{--                </select>--}}
{{--            </div>--}}

            <div class="form-group">
                <label>Sales Channel:</label>
                <div class="columns" style="column-count: 2;">
                    <?php if ($data[0]->sales_channel != null) { ?>
                    @foreach($sales_channel_list as $value)
                        <?php $checkbox_fields = explode(', ', $data[0]->sales_channel); ?>
                        <div class="col-md">
                            <div class="form-check" style="padding-left: 0px;">
                                <input  <?php if (in_array($value, $checkbox_fields)) echo "checked" ?>
                                        type="checkbox"
                                        name="sales_channel[]"
                                        value="{{ $value }}" id="{{$npd_design_request_type_id}}_{{$value}}"
                                >
                                <label class="form-check-label" for="{{$npd_design_request_type_id}}_{{ $value }}">
                                    {{ $value }}
                                </label>
                            </div>
                        </div>
                    @endforeach
                    <?php }else{ ?>
                    @foreach($sales_channel_list as $value)
                        <?php $checkbox_fields = explode(', ', $data[0]->sales_channel); ?>
                        <div class="col-md">
                            <div class="form-check" style="padding-left: 0px;">
                                <input  type="checkbox"
                                        name="sales_channel[]"
                                        value="{{ $value }}" id="{{$npd_design_request_type_id}}_{{$value}}"
                                >
                                <label class="form-check-label" for="{{$npd_design_request_type_id}}_{{ $value }}">
                                    {{ $value }}
                                </label>
                            </div>
                        </div>
                    @endforeach
                    <?php } ?>
                </div>
            </div>

            <div class="form-group">
                <label>If others, specify:</label>
                <textarea class="form-control" id="if_others_sales_channel" name="if_others_sales_channel" style="height: 50px;" >{{ $data[0]->if_others_sales_channel }}</textarea>
            </div>

            <div class="form-group">
                <label>Target Audience:</label>
                <textarea class="form-control" id="target_audience" name="target_audience" style="height: 100px;" >{{ $data[0]->target_audience }}</textarea>
            </div>

            <div class="form-group">
                <label>Head Copy:</label>
                <textarea class="form-control" id="head_copy" name="head_copy" style="height: 100px;" >{{ $data[0]->head_copy }}</textarea>
            </div>

            <div class="form-group">
                <label>References: </label>
                {!! Form::textarea('reference', !empty($data[0]->reference) ? $data[0]->reference : null, ['class' => 'form-control summernote']) !!}
            </div>

            <div class="form-group">
                <label>Material Number:</label>
                <input type="text" name="material_number" class="form-control" value="{{ $data[0]->material_number }}" required>
            </div>

            <div class="form-group">
                <label>Component Number:</label>
                <input type="text" name="component_number" class="form-control" value="{{ $data[0]->component_number }}" required>
            </div>

            <?php if (count($data[1]) > 0): ?>
            <label style="font-weight: 800; color: #34395e; font-size: 12px;">Attachments: </label>
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
            <?php endif; ?>

            <div class="form-group">
                <label>Attachment: <b style="color: #b91d19">(20MB Max)</b></label>
                <input type="file" data-asset="default" name="attachment[]" class="form-control p_attachment last_upload" multiple="multiple"/>
                <a href="javascript:void(0);" onclick="another_upload($(this))" class="another_upload">[ Upload Another ]</a>
            </div>

            <div style="background-color: #e9e9e9; padding: 10px 10px 10px 10px; margin: 0 0 10px 0;">
                <div class="section-title mt-1">For Design Team Only</div>
                <div class="form-group">
                    <label>Multiple Assignees:</label>
                    <div class="columns" style="column-count: 4;">
                        <?php $checkbox_fields = explode(', ', $data[0]->multiple_assignees); ?>
                        <?php foreach($data[3] as $value): ?>
                        <div class="col-md">
                            <div class="form-check" style="padding-left: 0px;">
                                <input  type="checkbox"
                                        <?php if (in_array($value->first_name . ' ' . $value->last_name, $checkbox_fields)) echo "checked" ?>
                                        name="multiple_assignees[]"
                                        value="{{ $value->first_name }} {{ $value->last_name }}"
                                        id="{{$npd_design_request_type_id}}_{{ $value->first_name }} {{ $value->last_name }}"
                                >
                                <label class="form-check-label " for="{{$npd_design_request_type_id}}_{{ $value->first_name }} {{ $value->last_name }}">
                                    {{ $value->first_name }} {{ $value->last_name }}
                                </label>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

            </div>

    </div>

    <div class="form-group">

        <?php if (!empty($data[0]) && $data[0]->status == 'in_progress') { ?>
            <?php if( (auth()->user()->role == 'Admin' || auth()->user()->function == 'Design')
            || (auth()->user()->team == 'Brand Design' || auth()->user()->team == 'Production Design' || auth()->user()->team == 'Industrial Design') ) { ?>

            <input type="submit" name="submit" value="Save" style="margin-top:10px; font-size: large" class="btn btn-lg btn-primary submit"/>
            <label style="font-size: medium; font-weight:100; color: #b91d19; padding: 0 0 0 20px;">* All changes must be saved before clicking any action buttons.</label>
            <br>
            <input type="button"
                   name="action review"
                   value="Revision"
                   data-toggle="modal"
                   data-target="#revision_reason_update_request_{{$npd_design_request_type_id}}"
{{--                   onclick="update_required($(this))"--}}
{{--                   data-request-type-id="<?php echo $npd_design_request_type_id; ?>"--}}
                   style="margin-top:10px; font-size: medium;"
                   class="btn btn-lg btn-revision submit"/>

            <input type="button"
                   name="action review"
                   value="Design Review"
                   onclick="action_review($(this))"
                   data-request-type-id="<?php echo $npd_design_request_type_id; ?>"
                   style="margin-top:10px; font-size: medium;"
                   class="btn btn-lg btn-review submit"/>
            <?php }?>
        <?php }?>


            <?php if (!empty($data[0]) && $data[0]->status == 'action_review') { ?>
            <?php if(auth()->user()->role == 'Admin' || auth()->user()->id == $t_author) { ?>
            <input type="submit" name="submit" value="Save" style="margin-top:10px; font-size: large" class="btn btn-lg btn-primary submit"/>
            <input type="button"
                   value="Decline"
                   data-toggle="modal"
                   data-target="#revision_reason_action_decline_{{$npd_design_request_type_id}}"
{{--                   onclick="action_decline($(this))"--}}
{{--                   data-request-type-id="<?php echo $npd_design_request_type_id; ?>"--}}
                   style="margin-top:10px; font-size: medium;"
                   class="btn btn-lg btn-revision submit"/>

            <input type="button"
                   name="action complete"
                   value="Complete"
                   onclick="action_complete($(this))"
                   data-request-type-id="<?php echo $npd_design_request_type_id; ?>"
                   style="margin-top:10px; font-size: medium;"
                   class="btn btn-lg btn-complete submit"/>
            <?php }?>
            <?php }?>

            <?php if (!empty($data[0]) && $data[0]->status == 'update_required') { ?>
            <?php if(auth()->user()->role == 'Admin' || auth()->user()->id == $t_author) { ?>
            <input type="submit" name="submit" value="Save" style="margin-top:10px; font-size: large" class="btn btn-lg btn-primary submit"/>
            <label style="font-size: medium; font-weight:100; color: #b91d19; padding: 0 0 0 20px;">* Save all changes before clicking action buttons. *</label>
            <br>
            <input type="button"
                   value="Resubmit"
                   onclick="action_resubmit($(this))"
                   data-request-type-id="<?php echo $npd_design_request_type_id; ?>"
                   style="margin-top:10px; font-size: medium;"
                   class="btn btn-lg btn-resubmit submit"/>
            <?php }?>
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

<div class="modal fade"
     id="revision_reason_update_request_{{$npd_design_request_type_id}}"
     tabindex="-1"
     data-backdrop="false"
     role="dialog"
     aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('npd_design_request.revision_reason_update_request') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="request_type_id" value="{{$npd_design_request_type_id}}">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Revision Reason (Update Request)</h4>
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
                                                id="revision_{{$npd_design_request_type_id}}_{{$val}}" value="{{$val}}"
                                        >
                                        <label class="form-check-label" for="revision_{{$npd_design_request_type_id}}_{{ $val }}">
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

<div class="modal fade"
     id="revision_reason_action_decline_{{$npd_design_request_type_id}}"
     tabindex="-1"
     data-backdrop="false"
     role="dialog"
     aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('npd_design_request.revision_reason_action_decline') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="request_type_id" value="{{$npd_design_request_type_id}}">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Decline Reason</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <h6><b>Select the appropriate reason and provide any additional details in the note before saving.</b></h6>
                        <div class="columns" style="column-count: 1;">
                            @foreach($decline_reason_list as $val)
                                <div class="col-md">
                                    <div class="form-check">
                                        <input  type="radio" name="decline_reason" required
                                                id="decline_{{$npd_design_request_type_id}}_{{$val}}" value="{{$val}}"
                                        >
                                        <label class="form-check-label" for="decline_{{$npd_design_request_type_id}}_{{$val}}">
                                            {{ $val }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <label>Note:</label>
                        <textarea class="form-control" id="decline_reason_note" name="decline_reason_note" style="height: 100px;"></textarea>

                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-dark" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-revision">Save Decline Reason</button>
                </div>
            </form>
        </div>
    </div>
</div>