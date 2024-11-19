<?php $task_id = $data[0][0]->task_id; $t_type = $data[0][0]->type; $t_author = $data[0][0]->author_id; ?>

<?php if (!empty($task_status) && $task_status == 'action_requested') { ?>
<?php if(auth()->user()->role == 'Admin' || auth()->user()->team == 'QM QA') { ?>
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

<form method="POST" action="{{ route('qc_request.edit_qc_request', $task_id) }}" enctype="multipart/form-data">
    @csrf

    <div class="form-group">
    <label>Request Type:</label>
    <select id="{{ $task_id }}_work_type" class="form-control"
            name="work_type" required>
        <option value="">Select</option>
        @foreach ($work_type_list as $value)
            <option value="{{ $value }}" {{ $value == $data[0][0]->work_type ? 'selected' : '' }}>
                {{ $value }}
            </option>
        @endforeach
    </select>
    </div>

    <div class="form-group">
        <label>Ship Date (ETD): <b style="color: #b91d19">(Select from the calendar)</b></label>
        <input type="text" name="ship_date" id="{{$task_id}}_ship_date" placeholder="Ship Date" required
               class="form-control @error('ship_date') is-invalid @enderror @if (!$errors->has('ship_date') && old('ship_date')) is-valid @endif"
               value="{{ old('ship_date', !empty($data[0][0]) ? $data[0][0]->ship_date : null) }}">
    </div>

    <div class="form-group">
        <label>QC Date (Requested): <b style="color: #b91d19">(Select from the calendar)</b></label>
        <input type="text" name="qc_date" id="{{$task_id}}_qc_date" placeholder="QC Date" required
               class="form-control @error('qc_date') is-invalid @enderror @if (!$errors->has('qc_date') && old('qc_date')) is-valid @endif"
               value="{{ old('qc_date', !empty($data[0][0]) ? $data[0][0]->qc_date : null) }}">
    </div>

    <div class="form-group">
        <label>PO# (If PO is not released, fill in 'TBD'):</label>
        <input type="text" name="po" class="form-control" value="<?php echo $data[0][0]->po; ?>" required>
    </div>

    <div class="form-group">
        <label>PO (USD):</label>
        <input type="text" name="po_usd" class="form-control" value="<?php echo $data[0][0]->po_usd; ?>" required>
    </div>

    <div class="form-group">
        <label>Materials #(Item code, SKU):</label>
        <input type="text" name="materials" class="form-control" value="<?php echo $data[0][0]->materials; ?>" required>
    </div>

    <div class="form-group">
        <label>Item Type: <b style="color: #b91d19;">(Auto-Complete)</b></label>
        <input type="text" name="item_type" class="form-control" id="product_category_auto" value="<?php echo $data[0][0]->item_type; ?>" required>
    </div>

    <div class="form-group">
        <label>Vendor Code: <b style="color: #b91d19;">(Auto-Complete)</b></label>
        <input type="text" name="vendor_code" class="form-control" id="vendor_auto" value="<?php echo $data[0][0]->vendor_code; ?>" required>
    </div>

    <div class="form-group">
        <label>Vendor Name:</label>
        <input type="text" name="vendor_name" class="form-control" id="vendor_name" readonly value="<?php echo $data[0][0]->vendor_name; ?>">
    </div>

    <div class="form-group">
        <label>Country:</label>
        <input type="text" name="country" class="form-control" value="<?php echo $data[0][0]->country; ?>" required>
    </div>

    <div class="form-group">
        <label>Vendor Primary Contact (Name):</label>
        <input type="text" name="vendor_primary_contact_name" class="form-control" value="<?php echo $data[0][0]->vendor_primary_contact_name; ?>" required>
    </div>

    <div class="form-group">
        <label>Vendor Primary Contact (E-mail):</label>
        <input type="text" name="vendor_primary_contact_email" class="form-control" value="<?php echo $data[0][0]->vendor_primary_contact_email; ?>" required>
    </div>

    <div class="form-group">
        <label>Vendor Primary Contact (Phone):</label>
        <input type="text" name="vendor_primary_contact_phone" class="form-control" value="<?php echo $data[0][0]->vendor_primary_contact_phone; ?>" required>
    </div>

    <div class="form-group">
        <label>Facility Address:</label>
        <input type="text" name="facility_address" class="form-control" value="<?php echo $data[0][0]->facility_address; ?>" required>
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
        <label>Attachments: <b style="color: #b91d19">(Click for icon to download the template)</b>
            <br>
            <img title="COT_Electric Device_R2024"
                 data-file-date="COT_Electric Device_R2024"
                 src="/storage/doc.png"
                 onclick="open_download('/storage/COT_Electric Device_R2024.docx')"
                 class="thumbnail"/>
            COT_Electric Device_R2024
            <img title="COT_General_R2024"
                 data-file-date="COT_Electric Device_R2024"
                 src="/storage/xls.png"
                 onclick="open_download('/storage/COT_General_R2024.xlsx')"
                 class="thumbnail"/>
            COT_General_R2024
        </label>
        <input type="file" data-asset="default" name="p_attachment[]" class="form-control p_attachment last_upload" multiple="multiple"/>
        <a href="javascript:void(0);" onclick="another_upload($(this))" class="another_upload">[ Upload Another ]</a>
    </div>

    <hr style="border-width: 1px 0px 0px 0px;border-style:solid;border-color: #e0e0e0;
            height:1px;margin-top: 15px;margin-bottom: 10px;width:100%">

    <div style="background-color: #e9e9e9; padding: 10px 10px 10px 10px; margin: 0 0 10px 0;">
        <div class="section-title mt-1">For QM-QA Team Only</div>
        <div class="form-group">
            <label>Performed By:</label>
            <select id="{{ $task_id }}_performed_by" class="form-control"
                    name="performed_by">
                <option value="">Select</option>
                @foreach ($performed_bys as $value)
                    <option value="{{ $value }}" {{ $value == $data[0][0]->performed_by ? 'selected' : '' }}>
                        {{ $value }}
                    </option>
                @endforeach
            </select>
        </div>


        <div class="form-group">
            <label>Critical:</label>
            <input type="text" name="critical" class="form-control" value="<?php echo $data[0][0]->critical; ?>">
        </div>

        <div class="form-group">
            <label>Result:</label>
            <select id="{{ $task_id }}_result" class="form-control"
                    name="result">
                <option value="">Select</option>
                @foreach ($result_list as $value)
                    <option value="{{ $value }}" {{ $value == $data[0][0]->result ? 'selected' : '' }}>
                        {{ $value }}
                    </option>
                @endforeach
            </select>
        </div>

{{--        <div class="form-group">--}}
{{--            <label>Decision:</label>--}}
{{--            <select id="{{ $task_id }}_decision" class="form-control"--}}
{{--                    name="decision">--}}
{{--                <option value="">Select</option>--}}
{{--                @foreach ($decision_list as $value)--}}
{{--                    <option value="{{ $value }}" {{ $value == $data[0][0]->decision ? 'selected' : '' }}>--}}
{{--                        {{ $value }}--}}
{{--                    </option>--}}
{{--                @endforeach--}}
{{--            </select>--}}
{{--        </div>--}}

        <div class="form-group">
            <label>QC Completed Date: <b style="color: #b91d19">(Select from the calendar)</b></label>
            <input type="text" name="qc_completed_date" id="{{$task_id}}_qc_completed_date" placeholder="QC Completed Date"
                   class="form-control @error('qc_completed_date') is-invalid @enderror @if (!$errors->has('qc_completed_date') && old('qc_completed_date')) is-valid @endif"
                   value="{{ old('qc_completed_date', !empty($data[0][0]) ? $data[0][0]->qc_completed_date : null) }}">
        </div>
    </div>

    <div class="form-group">

        <?php if (!empty($task_status) && $task_status == 'in_progress') { ?>
        <?php if(auth()->user()->role == 'Admin'
        || auth()->user()->team == 'QM QA') { ?>
        <input type="submit" name="submit" value="Save" style="font-size: medium" class="btn btn-lg btn-primary submit"/>
            <label style="font-size: medium; font-weight:100; font-family: sans-serif; color: #b91d19; padding: 0 0 0 20px;"><b>* All changes must be saved before clicking any action buttons.</b></label>
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
        <?php if(auth()->user()->role == 'Admin'
        || auth()->user()->id == $t_author) { ?>
            <input type="button"
                   value="Decline"
                   data-toggle="modal"
                   data-target="#revision_reason_action_decline_{{$task_id}}"
                   style="margin-top:10px; font-size: medium;"
                   class="btn btn-lg btn-revision submit"/>

{{--        <input type="button"--}}
{{--               value="QC Re-Request"--}}
{{--               onclick="action_start($(this))"--}}
{{--               data-task-id="<?php echo $task_id; ?>"--}}
{{--               style="margin-top:10px; font-size: medium;"--}}
{{--               class="btn btn-lg btn-danger submit"/>--}}
        <?php } ?>
        <?php }?>

        <?php if (!empty($task_status) && $task_status == 'action_review') { ?>
        <?php if(auth()->user()->role == 'Admin'
        || auth()->user()->id == $t_author) { ?>
        <input type="button"
               value="Override / Release"
               onclick="action_complete($(this))"
               data-task-id="<?php echo $task_id; ?>"
               style="margin-top:10px; font-size: medium;"
               class="btn btn-lg btn-complete submit"/>
        <?php } ?>
        <?php }?>



    </div>
</form>



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
        var ship_date = "<?php echo $data[0][0]->ship_date; ?>"
        $('input[id="<?php echo $task_id;?>_ship_date"]').daterangepicker({
            singleDatePicker: true,
            minDate:ship_date,
            locale: {
                format: 'YYYY-MM-DD'
            },
        });
    });

    $(function() {
        var qc_date = "<?php echo $data[0][0]->qc_date; ?>"
        $('input[id="<?php echo $task_id;?>_qc_date"]').daterangepicker({
            singleDatePicker: true,
            minDate:qc_date,
            locale: {
                format: 'YYYY-MM-DD'
            },
        });
    });

    $(function() {
        $('input[id="<?php echo $task_id;?>_qc_completed_date"]').daterangepicker({
            singleDatePicker: true,
            locale: {
                format: 'YYYY-MM-DD'
            },
        });
    });

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

    $(document).ready(function(){
        $("#product_category_auto").autocomplete({
            source: function (request, cb){
                $.ajax({
                    url: "<?php echo url('/admin/project/autocomplete_product_category'); ?>"+"?product_category="+request.term,
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
                                    label: obj.name,
                                    value: obj.name,
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
                    $('#product_category').val(data.name);
                }
            }
        });
    });

</script>

<div class="modal fade"
     id="revision_reason_action_decline_{{$task_id}}"
     tabindex="-1"
     data-backdrop="false"
     role="dialog"
     aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('qc_request.revision_reason_action_decline') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="request_type_id" value="{{$task_id}}">
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
                                        <input  type="radio" name="revision_reason" required
                                                id="revision_{{$task_id}}_{{$val}}" value="{{$val}}">
                                        <label class="form-check-label" for="revision_{{$task_id}}_{{$val}}">
                                            {{ $val }}
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
                    <button type="submit" class="btn btn-revision">Save Decline Reason</button>
                </div>
            </form>
        </div>
    </div>
</div>