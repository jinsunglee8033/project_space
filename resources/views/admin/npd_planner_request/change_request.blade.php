<?php $npd_planner_request_type_id = $data[0]->npd_planner_request_type_id; $request_type = $data[0]->request_type; $t_author = $data[0]->author_id; ?>

<form method="POST" action="{{ route('npd_planner_request.edit_change_request', $npd_planner_request_type_id) }}" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="{{ $request_type }}_t_id" value="{{ $task_id }}" />
    <input type="hidden" name="{{ $request_type }}_request_type" value="{{ $request_type }}" />
    <input type="hidden" name="{{ $request_type }}_author_id" value="{{ Auth::user()->id }}" />

    <div class="price">

        <?php if (!empty($data[0]) && $data[0]->status == 'action_requested') { ?>
        <?php if(auth()->user()->role == 'Admin'
        || auth()->user()->team == 'Red Trade Marketing (A&A)'
        || auth()->user()->team == 'B2B Marketing') { ?>

            <div style="margin: -20px 0 0px 10px; padding: 0px 0 0px 0;">
                <input type="button"
                       name="action start"
                       value="Start"
                       onclick="action_start($(this))"
                       data-request-type-id="<?php echo $npd_planner_request_type_id; ?>"
                       style="margin-top:10px; float:left; font-size: medium;"
                       class="btn btn-lg btn-success submit"/>
                <label style="font-size: medium; font-weight:100; color: #b91d19; padding: 22px 0 0 20px;"><b>* Click to indicate the start of work.</b></label>
            </div>

        <?php }?>
        <?php }?>

        <p style="color: #222; text-align: center; font-weight: bold;">Change Request</p>

            <?php if($data[0]->assignee) { ?>
            <div class="form-group">
                <label>Change Assignee: {{ $data[0]->request_group }}</label>
                <select id="{{ $task_id }}_assignee" class="form-control"
                        name="assignee">
                    <option value="">Select</option>
                    <?php
                        if($data[0]->request_group == 'Red Trade Marketing (A&A)') {
                            $assignee_list = $red_marketing_assignee_list;
                        }else if($data[0]->request_group == 'B2B Marketing'){
                            $assignee_list = $ivy_marketing_assignee_list;
                        }else{
                            $assignee_list = $kiss_marketing_assignee_list;
                        }
                    ?>
                    @foreach ($assignee_list as $value)
                        <option value="{{ $value->id }}" {{ $value->id == $data[0]->assignee ? 'selected' : '' }}>
                            {{ $value->first_name }} {{ $value->last_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <?php } ?>

            <div class="form-group">
                <label>Due  Date: <b style="color: #b91d19">(Select from the calendar)</b></label>
                <input type="text" name="due_date" id="{{ $npd_planner_request_type_id }}_due_date" autocomplete="off"
                       class="form-control" required
                       value="{{ $data[0]->due_date }}">
            </div>

            <div class="form-group">
                <label>Target Door Number:</label>
                <input type="text" name="target_door_number" class="form-control" value="{{ $data[0]->target_door_number }}">
            </div>

{{--            <div class="row">--}}
{{--                <div class="col-md-6">--}}
{{--                    <div class="form-group">--}}
{{--                        <label>NY Launch Date (LA): <b style="color: #b91d19">(Select from the calendar)</b></label>--}}
{{--                        <input type="text" name="ny_target_receiving_date" id="{{ $npd_planner_request_type_id }}_ny_target_receiving_date" autocomplete="off"--}}
{{--                               class="form-control"--}}
{{--                               value="{{ $data[0]->ny_target_receiving_date }}">--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="col-md-6">--}}
{{--                    <div class="form-group">--}}
{{--                        <label>LA Launch Date (LA): <b style="color: #b91d19">(Select from the calendar)</b></label>--}}
{{--                        <input type="text" name="la_target_receiving_date" id="{{ $npd_planner_request_type_id }}_la_target_receiving_date" autocomplete="off"--}}
{{--                               class="form-control"--}}
{{--                               value="{{ $data[0]->la_target_receiving_date }}">--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>NY Planned Launch  Date: <b style="color: #b91d19">(Select from the calendar)</b></label>
                        <input type="text" name="ny_planned_launch_date" id="{{ $npd_planner_request_type_id }}_ny_planned_launch_date" autocomplete="off"
                               class="form-control"
                               value="{{ $data[0]->ny_planned_launch_date }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>LA Planned Launch Date: <b style="color: #b91d19">(Select from the calendar)</b></label>
                        <input type="text" name="la_planned_launch_date" id="{{ $npd_planner_request_type_id }}_la_planned_launch_date" autocomplete="off"
                               class="form-control"
                               value="{{ $data[0]->la_planned_launch_date }}">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Update Type: </label>
                <select class="form-control" name="update_type" required>
                    <option value="">Select</option>
                    <?php foreach($update_type_list as $val): ?>
                    <option value="{{ $val }}" {{ $data[0]->update_type == $val ? 'selected' : '' }} >
                        {{ $val }}
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Revised Target Door Number:</label>
                <input type="text" name="revised_target_door_number" class="form-control" value="{{ $data[0]->revised_target_door_number }}">
            </div>

{{--            <div class="row">--}}
{{--                <div class="col-md-6">--}}
{{--                    <div class="form-group">--}}
{{--                        <label>Revised NY Receiving Date: <b style="color: #b91d19">(Select from the calendar)</b></label>--}}
{{--                        <input type="text" name="revised_ny_receiving_date" id="{{ $npd_planner_request_type_id }}_revised_ny_receiving_date" autocomplete="off"--}}
{{--                               class="form-control"--}}
{{--                               value="{{ $data[0]->revised_ny_receiving_date }}">--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="col-md-6">--}}
{{--                    <div class="form-group">--}}
{{--                        <label>Revised LA Receiving Date: <b style="color: #b91d19">(Select from the calendar)</b></label>--}}
{{--                        <input type="text" name="revised_la_receiving_date" id="{{ $npd_planner_request_type_id }}_revised_la_receiving_date" autocomplete="off"--}}
{{--                               class="form-control"--}}
{{--                               value="{{ $data[0]->revised_la_receiving_date }}">--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Revised NY Launch Date: <b style="color: #b91d19">(Select from the calendar)</b></label>
                        <input type="text" name="revised_ny_launch_date" id="{{ $npd_planner_request_type_id }}_revised_ny_launch_date" autocomplete="off"
                               class="form-control"
                               value="{{ $data[0]->revised_ny_launch_date }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Revised LA Launch Date: <b style="color: #b91d19">(Select from the calendar)</b></label>
                        <input type="text" name="revised_la_launch_date" id="{{ $npd_planner_request_type_id }}_revised_la_launch_date" autocomplete="off"
                               class="form-control"
                               value="{{ $data[0]->revised_la_launch_date }}">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Change Request Reason: </label>
                <select class="form-control" name="change_request_reason" required>
                    <option value="">Select</option>
                    <?php foreach($change_request_reason_list as $val): ?>
                    <option value="{{ $val }}" {{ $data[0]->change_request_reason == $val ? 'selected' : '' }} >
                        {{ $val }}
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Change Request Detail: </label>
                {!! Form::textarea('change_request_detail', !empty($data[0]->change_request_detail) ? $data[0]->change_request_detail : null, ['class' => 'form-control summernote']) !!}
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

    </div>

    <div class="form-group">

        <?php if (!empty($data[0]) && $data[0]->status == 'in_progress') { ?>
        <?php if(auth()->user()->role == 'Admin'
        || auth()->user()->team == 'Red Trade Marketing (A&A)'
        || auth()->user()->team == 'B2B Marketing') { ?>

        <input type="submit" name="submit" value="Save" style="margin-top:10px; font-size: large" class="btn btn-lg btn-primary submit"/>
        <label style="font-size: medium; font-weight:100; color: #b91d19; padding: 0 0 0 20px;">* Save all changes before clicking action buttons.</label>
        <br>
        <input type="button"
               value="Revision"
               data-toggle="modal"
               data-target="#revision_reason_update_request_{{$npd_planner_request_type_id}}"
               style="margin-top:10px; font-size: medium;"
               class="btn btn-lg btn-revision submit"/>

            <input type="button"
                   name="action review"
                   value="Review"
                   onclick="action_review($(this))"
                   data-request-type-id="<?php echo $npd_planner_request_type_id; ?>"
                   style="margin-top:10px; font-size: medium;"
                   class="btn btn-lg btn-review submit"/>

        {{--           Complete button  --}}


        <?php }?>
        <?php }?>

            <?php if (!empty($data[0]) && $data[0]->status == 'action_review') { ?>
            <?php if(auth()->user()->role == 'Admin' || auth()->user()->id == $t_author) { ?>

            <input type="button"
                   value="Decline"
                   data-toggle="modal"
                   data-target="#revision_reason_action_decline_{{$npd_planner_request_type_id}}"
                   style="margin-top:10px; font-size: medium;"
                   class="btn btn-lg btn-revision submit"/>

            <input type="button"
                   name="action complete"
                   value="Complete"
                   onclick="action_complete($(this))"
                   data-request-type-id="<?php echo $npd_planner_request_type_id; ?>"
                   style="margin-top:10px; font-size: medium;"
                   class="btn btn-lg btn-complete submit"/>

            <?php }?>
            <?php }?>

            <?php if (!empty($data[0]) && $data[0]->status == 'update_required') { ?>
            <?php if(auth()->user()->role == 'Admin' || auth()->user()->id == $t_author) { ?>
            <input type="submit" name="submit" value="Save" style="margin-top:10px; font-size: large" class="btn btn-lg btn-primary submit"/>
            <label style="font-size: medium; font-weight:100; color: #b91d19; padding: 0 0 0 20px;">* Save before Resubmit *</label>
            <br>
            <input type="button"
                   value="Resubmit"
                   onclick="action_resubmit($(this))"
                   data-request-type-id="<?php echo $npd_planner_request_type_id; ?>"
                   style="margin-top:10px; font-size: medium;"
                   class="btn btn-lg btn-resubmit submit"/>
            <?php }?>
            <?php }?>

            <?php if (!empty($data[0]) && $data[0]->status == 'action_completed' && $data[0]->uploaded_date == null ) { ?>
            <?php if ( (auth()->user()->function == 'Admin') || ($data[0]->request_group == 'B2B Marketing' && auth()->user()->team == 'SOM' )
            || ($data[0]->request_group == 'Red Trade Marketing (A&A)' && auth()->user()->team == 'Red Trade Marketing (A&A)' ) ) { ?>
            <div class="form-group">
                <input type="button"
                       name="uploaded"
                       value="Uploaded"
                       onclick="action_upload($(this))"
                       data-request-type-id="<?php echo $npd_planner_request_type_id; ?>"
                       style="margin-top:-5px; font-size: medium;"
                       class="btn btn-lg btn-dark submit"/>
                <label style="font-size: medium; color: #b91d19; padding: 22px 0 30px 20px;"><b>* Click here to confirm that the upload to the system is complete. *</b></label>
            </div>
            <?php } ?>
            <?php } ?>

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

<script type="text/javascript">
    $(function() {
        $('input[id="<?php echo $npd_planner_request_type_id;?>_due_date"]').daterangepicker({
            singleDatePicker: true,
            locale: {
                format: 'YYYY-MM-DD'
            },
        });
        $('input[id="<?php echo $npd_planner_request_type_id;?>_ny_target_receiving_date"]').daterangepicker({
            singleDatePicker: true,
            locale: {
                format: 'YYYY-MM-DD'
            },
        });
        $('input[id="<?php echo $npd_planner_request_type_id;?>_la_target_receiving_date"]').daterangepicker({
            singleDatePicker: true,
            locale: {
                format: 'YYYY-MM-DD'
            },
        });
        $('input[id="<?php echo $npd_planner_request_type_id;?>_ny_planned_launch_date"]').daterangepicker({
            singleDatePicker: true,
            locale: {
                format: 'YYYY-MM-DD'
            },
        });
        $('input[id="<?php echo $npd_planner_request_type_id;?>_la_planned_launch_date"]').daterangepicker({
            singleDatePicker: true,
            locale: {
                format: 'YYYY-MM-DD'
            },
        });
        $('input[id="<?php echo $npd_planner_request_type_id;?>_revised_ny_receiving_date"]').daterangepicker({
            singleDatePicker: true,
            locale: {
                format: 'YYYY-MM-DD'
            },
        });
        $('input[id="<?php echo $npd_planner_request_type_id;?>_revised_la_receiving_date"]').daterangepicker({
            singleDatePicker: true,
            locale: {
                format: 'YYYY-MM-DD'
            },
        });
        $('input[id="<?php echo $npd_planner_request_type_id;?>_revised_ny_launch_date"]').daterangepicker({
            singleDatePicker: true,
            locale: {
                format: 'YYYY-MM-DD'
            },
        });
        $('input[id="<?php echo $npd_planner_request_type_id;?>_revised_la_launch_date"]').daterangepicker({
            singleDatePicker: true,
            locale: {
                format: 'YYYY-MM-DD'
            },
        });
    });
</script>

<div class="modal fade"
     id="revision_reason_update_request_{{$npd_planner_request_type_id}}"
     tabindex="-1"
     data-backdrop="false"
     role="dialog"
     aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('npd_planner_request.revision_reason_update_request') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="request_type_id" value="{{$npd_planner_request_type_id}}">
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
                                                id="revision_{{$npd_planner_request_type_id}}_{{$val}}" value="{{$val}}"
                                        >
                                        <label class="form-check-label" for="revision_{{$npd_planner_request_type_id}}_{{ $val }}">
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
     id="revision_reason_action_decline_{{$npd_planner_request_type_id}}"
     tabindex="-1"
     data-backdrop="false"
     role="dialog"
     aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('npd_planner_request.revision_reason_action_decline') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="request_type_id" value="{{$npd_planner_request_type_id}}">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Decline Reason</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <h6><b>Select the appropriate reason and provide any additional details in the note before saving.</b></h6>
                        <div class="columns" style="column-count: 1;">
                            @foreach($revision_reason_list as $val)
                                <div class="col-md">
                                    <div class="form-check">
                                        <input  type="radio" name="decline_reason" required
                                                id="decline_{{$npd_planner_request_type_id}}_{{$val}}" value="{{$val}}"
                                        >
                                        <label class="form-check-label" for="decline_{{$npd_planner_request_type_id}}_{{$val}}">
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
                    <button type="submit" class="btn btn-revision">Save Revision Reason</button>
                </div>
            </form>
        </div>
    </div>
</div>