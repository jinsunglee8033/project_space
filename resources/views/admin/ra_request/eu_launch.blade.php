<?php $ra_request_type_id = $data[0]->ra_request_type_id; $request_type = $data[0]->request_type;?>

<form method="POST" action="{{ route('ra_request.edit_eu_launch', $ra_request_type_id) }}" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="{{ $request_type }}_t_id" value="{{ $task_id }}" />
    <input type="hidden" name="{{ $request_type }}_request_type" value="{{ $request_type }}" />
    <input type="hidden" name="{{ $request_type }}_author_id" value="{{ Auth::user()->id }}" />

    <div class="artwork_review">

        <?php if (!empty($data[0]) && $data[0]->status == 'action_requested') { ?>
        <?php if(auth()->user()->role == 'Admin' || auth()->user()->team == 'Legal RA') { ?>

        <input type="button"
               name="action start"
               value="Start"
               onclick="action_start($(this))"
               data-request-type-id="<?php echo $ra_request_type_id; ?>"
               style="margin-top:-7px; float:left; font-size: medium;"
               class="btn btn-lg btn-success submit"/>
        <label style="font-size: medium; font-weight:100; color: #b91d19; padding: 8px 0 15px 20px;"><b>* Click to indicate the start of work.</b></label>

        <?php }?>
        <?php }?>

            <p style="color: #222; text-align: center; font-weight: bold;">EU LAUNCH</p>

            <div class="form-group">
                <a href="https://drive.google.com/drive/folders/1kCsyOnjxxQ0Z1wu7rbBRDyfe9gpilTMQ?usp=drive_link" target="_blank" class="badge badge-danger">View Submission Guideline</a>
            </div>

            <div class="form-group">
                <label>Requested Due Date: <b style="color: #b91d19">(The request will take 30 business days to complete after the CPNP submission by RA marked as “PENDING (3rd Party).")</b></label>
                <input type="text" name="due_date" class="form-control" readonly value="{{ $data[0]->due_date }}">
            </div>

        <?php if (count($data[1]) > 0): ?>
            <label style="font-weight: 800; color: #34395e; font-size: 12px;">Upload EU DOC: </label>
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
                <label><b style="color: #b91d19;">*</b> Upload EU DOC: <b style="color: #b91d19">(20MB Max)</b></label>
                <input type="file" data-asset="default" name="attachment[]" class="form-control p_attachment last_upload" multiple="multiple"/>
                <a href="javascript:void(0);" onclick="another_upload($(this))" class="another_upload">[ Upload Another ]</a>
            </div>

            <div style="background-color: #e9e9e9; padding: 10px 10px 10px 10px; margin: 0 0 10px 0;">
                <div class="section-title mt-1">For Legal RA Team Only</div>
                <div class="form-group">
                    <label>Assignee:</label>
                    <select id="assignee" class="form-control" name="assignee">
                        <option value="">Select</option>
                        @foreach ($ra_assignee_list as $value)
                            <option value="{{ $value->id }}" {{ $value->id == $data[0]->assignee ? 'selected' : '' }}>
                                {{ $value->first_name }} {{ $value->last_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>On-going Registrations:</label>
                    <div class="columns" style="column-count: 4;">
                        <?php $checkbox_fields = explode(',', $data[0]->on_going_registrations); ?>
                        <?php foreach($on_going_registrations_list as $value): ?>
                        <div class="col-md">
                            <div class="form-check" style="padding-left: 0px;">
                                <input  type="checkbox"
                                        <?php if (in_array($value, $checkbox_fields)) echo "checked" ?>
                                        name="on_going_registrations[]"
                                        value="{{ $value }}" id="{{$ra_request_type_id}}_{{$value}}"
                                >
                                <label class="form-check-label " for="{{$ra_request_type_id}}_{{ $value }}">
                                    {{ $value }}
                                </label>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label>CPNP Stage: </label>
                    <select class="form-control" name="cpnp_stage">
                        <option value="">Select</option>\
                        <option value="1. EU DOC" {{ $data[0]->cpnp_stage == '1. EU DOC' ? 'selected' : '' }} >1. EU DOC</option>
                        <option value="2. SA Request" {{ $data[0]->cpnp_stage == '2. SA Request' ? 'selected' : '' }} >2. SA Request</option>
                        <option value="3. Final EU artwork" {{ $data[0]->cpnp_stage == '3. Final EU artwork' ? 'selected' : '' }} >3. Final EU artwork</option>
                        <option value="4. CPNP initiative" {{ $data[0]->cpnp_stage == '4. CPNP initiative' ? 'selected' : '' }} >4. CPNP initiative</option>
                        <option value="5. CPNP completed" {{ $data[0]->cpnp_stage == '5. CPNP completed' ? 'selected' : '' }} >5. CPNP completed</option>
                        <option value="6. EU PCN submitted" {{ $data[0]->cpnp_stage == '6. EU PCN submitted' ? 'selected' : '' }} >6. EU PCN submitted</option>
                        <option value="7. EU PCN completed" {{ $data[0]->cpnp_stage == '7. EU PCN completed' ? 'selected' : '' }} >7. EU PCN completed</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Est. Registration Due Date:</label>
                    <input type="text" class="form-control" name="registration_due_date"
                           id="{{$ra_request_type_id}}_registration_due_date" autocomplete="off"
                           value="{{ old('registration_due_date', !empty($data[0]) ? $data[0]->registration_due_date : null) }}">
                </div>

                <div class="form-group">
                    <label>Registration #:</label>
                    <input type="text" name="registration_number" class="form-control" value="{{ $data[0]->registration_number }}">
                </div>

                <div class="form-group">
                    <label>Formula:</label>
                    <input type="text" name="formula" class="form-control" value="{{ $data[0]->formula }}">
                </div>

                <div class="form-group">
                    <label>RA Remarks: </label>
                    {!! Form::textarea('ra_remarks', !empty($data[0]->ra_remarks) ? $data[0]->ra_remarks : null, ['class' => 'form-control summernote']) !!}
                </div>

            </div>

    </div>

    <div class="form-group">

        <?php if (!empty($data[0]) && $data[0]->status == 'in_progress') { ?>
        <?php if(auth()->user()->role == 'Admin' || auth()->user()->team == 'Legal RA') { ?>
        <input type="submit" name="submit" value="Save" style="margin-top:10px; font-size: large" class="btn btn-lg btn-primary submit"/>
        <label style="font-size: medium; font-weight:100; font-family: sans-serif; color: #b91d19; padding: 0 0 0 20px;"><b>* All changes must be saved before clicking any action buttons.</b></label>
        <br>
        <input type="button"
               name="RA revision"
               value="Revision"
               data-toggle="modal"
               data-target="#revision_reason_{{$ra_request_type_id}}"
{{--               onclick="ra_revision($(this))"--}}
{{--               data-request-type-id="<?php echo $ra_request_type_id; ?>"--}}
               style="margin-top:10px; font-size: medium;"
               class="btn btn-lg btn-revision submit"/>

        <input type="button"
               name="Review complete"
               value="Complete"
               onclick="review_complete($(this))"
               data-request-type-id="<?php echo $ra_request_type_id; ?>"
               style="margin-top:10px; font-size: medium;"
               class="btn btn-lg btn-complete submit"/>
        <?php }?>
        <?php }?>

        <?php if (!empty($data[0]) && $data[0]->status == 'action_review') { ?>
        <?php if(auth()->user()->role == 'Admin' || auth()->user()->id == $data[0]->author_id)  { ?>
        <input type="submit" name="submit" value="Save" style="margin-top:10px; font-size: large" class="btn btn-lg btn-primary submit"/>
        <label style="font-size: medium; font-family: sans-serif; color: #b91d19; padding: 0 0 0 20px;"><b>* Save all changes before clicking action buttons. *</b></label>
        <br>
        <input type="button"
               name="RA resubmit"
               value="Resubmit"
               onclick="ra_resubmit($(this))"
               data-request-type-id="<?php echo $ra_request_type_id; ?>"
               style="margin-top:10px; font-size: medium;"
               class="btn btn-lg btn-resubmit submit"/>
        <?php }?>
        <?php }?>

    </div>

</form>

<script type="text/javascript">

    $(function() {
        var today = new Date();
        if(today.getHours() >= 16){
            var count = 1; // past 4pm
        }else{
            var count = 0; // before 4pm
        }
        for (let i = 1; i <= count; i++) {
            today.setDate(today.getDate() + 1);
            if (today.getDay() === 6) {
                today.setDate(today.getDate() + 2);
            }
            else if (today.getDay() === 0) {
                today.setDate(today.getDate() + 1);
            }
        }
        $('input[id="<?php echo $ra_request_type_id;?>_registration_due_date"]').daterangepicker({
            singleDatePicker: true,
            autoUpdateInput: false,
            minDate: today,
            locale: {
                format: 'YYYY-MM-DD'
            },
        }).on("apply.daterangepicker", function (e, picker) {
            picker.element.val(picker.startDate.format(picker.locale.format));
        });
    });

    $(document).ready(function(){
        $("#{{ $ra_request_type_id }}_vendor_auto").autocomplete({
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
                    $('#{{ $ra_request_type_id }}_vendor_name').val(data.name);
                }
            }
        });
    });

</script>

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
     id="revision_reason_{{$ra_request_type_id}}"
     tabindex="-1"
     data-backdrop="false"
     role="dialog"
     aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('ra_request.revision_reason') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="request_type_id" value="{{$ra_request_type_id}}">
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
                                                id="revision_{{$ra_request_type_id}}_{{$val}}" value="{{$val}}"
                                        >
                                        <label class="form-check-label " for="revision_{{$ra_request_type_id}}_{{ $val }}">
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
