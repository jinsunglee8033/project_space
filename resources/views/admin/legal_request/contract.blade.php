<?php $legal_request_type_id = $data[0]->legal_request_type_id; $request_type = $data[0]->request_type; $t_author = $data[0]->author_id; ?>

<form method="POST" action="{{ route('legal_request.edit_contract', $legal_request_type_id) }}" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="{{ $request_type }}_t_id" value="{{ $task_id }}" />
    <input type="hidden" name="{{ $request_type }}_request_type" value="{{ $request_type }}" />
    <input type="hidden" name="{{ $request_type }}_author_id" value="{{ Auth::user()->id }}" />

    <div class="formulation_prescreen">

        <?php if (!empty($data[0]) && $data[0]->status == 'action_requested') { ?>
        <?php if(auth()->user()->role == 'Admin' || auth()->user()->team == 'Legal') { ?>

            <div style="margin: -20px 0 0px 10px; padding: 0px 0 0px 0;">
                <input type="button"
                       name="action start"
                       value="Start"
                       onclick="action_start($(this))"
                       data-request-type-id="<?php echo $legal_request_type_id; ?>"
                       style="margin-top:10px; float:left; font-size: medium;"
                       class="btn btn-lg btn-success submit"/>
                <label style="font-size: medium; font-weight:100; color: #b91d19; padding: 22px 0 0 20px;"><b>* Click to indicate the start of work.</b></label>
            </div>

        <?php }?>
        <?php }?>

        <p style="color: #222; text-align: center; font-weight: bold;">CONTRACT</p>

        <div class="form-group">
            <label>Type: </label>
            <select class="form-control" name="request_category">
                <option value="">Select</option>
                <?php foreach($contract_categories as $val): ?>
                <option value="{{ $val }}" {{ $data[0]->request_category == $val ? 'selected' : '' }} >
                    {{ $val }}
                </option>
                <?php endforeach; ?>
            </select>
        </div>

            <?php if(isset($data[0]->if_other_request_category)){ ?>
            <div class="form-group">
                <label>If others, please specify:</label>
                <input type="text" name="if_other_request_category" class="form-control" value="{{ $data[0]->if_other_request_category }}">
            </div>
            <?php } ?>

        <div class="form-group">
            <label>Description:</label>
            {!! Form::textarea('request_description', !empty($data[0]->request_description) ? $data[0]->request_description : null, ['class' => 'form-control summernote']) !!}
{{--            <textarea class="form-control" style="height:100px;"--}}
{{--                      id="{{ $request_type }}_request_description"--}}
{{--                      name="{{ $request_type }}_description">{{ $data[0]->request_description }}</textarea>--}}
        </div>

        <div class="form-group">
            <label>Priority: </label>
            <select class="form-control" name="priority">
                <option value="">Select</option>
                <?php foreach($priorities as $val): ?>
                <option value="{{ $val }}" {{ $data[0]->priority == $val ? 'selected' : '' }} >
                    {{ $val }}
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <?php if($data[0]->due_date_urgent) { ?>
        <div class="form-group due_date_urgent">
            <label>Due Date (Urgent): <b style="color: #b91d19">(Select from the calendar)</b></label>
            <input type="text" name="due_date_urgent" readonly placeholder="Due Date Urgent"
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
            <input type="text" name="due_date" readonly autocomplete="off"
                   class="form-control"
                   value="{{ $data[0]->due_date }}">
        </div>

{{--        <div class="form-group">--}}
{{--            <label>Vendor Code:</label>--}}
{{--            <input type="text" name="vendor_code" class="form-control" id="vendor_auto" value="{{ $data[0]->vendor_code }}">--}}
{{--        </div>--}}

{{--        <div class="form-group">--}}
{{--            <label>Vendor Name:</label>--}}
{{--            <input type="text" name="vendor_name" class="form-control" id="vendor_name" value="{{ $data[0]->vendor_name }}" readonly>--}}
{{--        </div>--}}

{{--        <div class="form-group">--}}
{{--            <label>Vendor Location:</label>--}}
{{--            <input type="text" name="vendor_location" class="form-control" value="{{ $data[0]->vendor_location }}">--}}
{{--        </div>--}}

        <div class="form-group">
            <label>Target Region:</label>
            <div class="columns" style="column-count: 4;">
                <?php $checkbox_fields = explode(',', $data[0]->target_region); ?>
                <?php foreach($target_regions as $value): ?>
                <div class="col-md">
                    <div class="form-check" style="padding-left: 0px;">
                        <input  type="checkbox"
                                <?php if (in_array($value, $checkbox_fields)) echo "checked" ?>
                                name="target_region[]"
                                value="{{ $value }}" id="{{$legal_request_type_id}}_{{$value}}"
                        >
                        <label class="form-check-label " for="{{$legal_request_type_id}}_{{ $value }}">
                            {{ $value }}
                        </label>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="form-group">
            <label>If others, please specify:</label>
            <input type="text" name="if_selected_others" class="form-control" value="{{ $data[0]->if_selected_others }}">
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
            <div class="section-title mt-1">For Legal Team Only</div>
            <div class="form-group">
                <label>Assignee:</label>
                <select id="assignee" class="form-control" name="assignee">
                    <option value="">Select</option>
                    @foreach ($legal_assignee_list as $value)
                        <option value="{{ $value->id }}" {{ $value->id == $data[0]->assignee ? 'selected' : '' }}>
                            {{ $value->first_name }} {{ $value->last_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Legal Remarks:</label>
                {!! Form::textarea('legal_remarks', !empty($data[0]->legal_remarks) ? $data[0]->legal_remarks : null, ['class' => 'form-control summernote']) !!}
            </div>
        </div>

    </div>

    <div class="form-group">

        <?php if (!empty($data[0]) && $data[0]->status == 'in_progress') { ?>
            <?php if(auth()->user()->role == 'Admin' || auth()->user()->team == 'Legal') { ?>

            <input type="submit" name="submit" value="Save" style="margin-top:10px; font-size: large" class="btn btn-lg btn-primary submit"/>
            <label style="font-size: medium; font-weight:100; color: #b91d19; padding: 0 0 0 20px;">* Save all changes before clicking action buttons.</label>
            <br>
            <input type="button"
                   name="action review"
                   value="Review"
                   onclick="action_review($(this))"
                   data-request-type-id="<?php echo $legal_request_type_id; ?>"
                   style="margin-top:10px; font-size: medium;"
                   class="btn btn-lg btn-review submit"/>

            <?php }?>
        <?php }?>

            <?php if (!empty($data[0]) && $data[0]->status == 'action_review') { ?>
            <?php if(auth()->user()->role == 'Admin' || auth()->user()->id == $t_author) { ?>
            <input type="button"
                   value="Revision"
                   data-toggle="modal"
                   data-target="#revision_reason_{{$legal_request_type_id}}"
{{--                   onclick="action_resubmit($(this))"--}}
{{--                   data-request-type-id="<?php echo $legal_request_type_id; ?>"--}}
                   style="margin-top:10px; font-size: medium;"
                   class="btn btn-lg btn-revision submit"/>

            <input type="button"
                   name="action complete"
                   value="Complete"
                   onclick="action_complete($(this))"
                   data-request-type-id="<?php echo $legal_request_type_id; ?>"
                   style="margin-top:10px; font-size: medium;"
                   class="btn btn-lg btn-complete submit"/>

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

<script type="text/javascript">
    $(function() {
        $('input[id="<?php echo $legal_request_type_id;?>_due_date"]').daterangepicker({
            singleDatePicker: true,
            locale: {
                format: 'YYYY-MM-DD'
            },
        });
    });
</script>

<div class="modal fade"
     id="revision_reason_{{$legal_request_type_id}}"
     tabindex="-1"
     data-backdrop="false"
     role="dialog"
     aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('legal_request.revision_reason') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="request_type_id" value="{{$legal_request_type_id}}">
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
                                                id="revision_{{$legal_request_type_id}}_{{$val}}" value="{{$val}}"
                                        >
                                        <label class="form-check-label " for="revision_{{$legal_request_type_id}}_{{ $val }}">
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
