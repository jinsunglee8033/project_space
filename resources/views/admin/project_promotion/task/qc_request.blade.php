<?php $task_id = $data[0][0]->task_id; $t_type = $data[0][0]->type; $t_author = $data[0][0]->author_id; ?>


<form method="POST" action="{{ route('project.edit_qc_request', $task_id) }}" enctype="multipart/form-data">
    @csrf

    <div class="form-group">
        <label>Ship Date:</label>
        <input type="text" name="ship_date" id="{{$task_id}}_ship_date" placeholder="Ship Date" autocomplete="off"
               class="form-control @error('ship_date') is-invalid @enderror @if (!$errors->has('ship_date') && old('ship_date')) is-valid @endif"
               value="{{ old('ship_date', !empty($data[0][0]) ? $data[0][0]->ship_date : null) }}">
    </div>

    <div class="form-group">
        <label>QC Date:</label>
        <input type="text" name="qc_date" id="{{$task_id}}_qc_date" placeholder="QC Date" autocomplete="off"
               class="form-control @error('qc_date') is-invalid @enderror @if (!$errors->has('qc_date') && old('qc_date')) is-valid @endif"
               value="{{ old('qc_date', !empty($data[0][0]) ? $data[0][0]->qc_date : null) }}">
    </div>

    <div class="form-group">
        <label>PO# (If PO is not released, fill in 'TBD'):</label>
        <input type="text" name="po" class="form-control" value="<?php echo $data[0][0]->po; ?>">
    </div>

    <div class="form-group">
        <label>PO (USD):</label>
        <input type="text" name="po_usd" class="form-control" value="<?php echo $data[0][0]->po_usd; ?>">
    </div>

    <div class="form-group">
        <label>Materials #(Item code, SKU):</label>
        <input type="text" name="materials" class="form-control" value="<?php echo $data[0][0]->materials; ?>">
    </div>

    <div class="form-group">
        <label>Item Type:</label>
        <input type="text" name="item_type" class="form-control" value="<?php echo $data[0][0]->item_type; ?>">
    </div>

    <div class="form-group">
        <label>Vendor Code:</label>
        <input type="text" name="vendor_code" class="form-control" value="<?php echo $data[0][0]->vendor_code; ?>">
    </div>

    <div class="form-group">
        <label>Country:</label>
        <input type="text" name="country" class="form-control" value="<?php echo $data[0][0]->country; ?>">
    </div>

    <div class="form-group">
        <label>Vendor Primary Contact (Name):</label>
        <input type="text" name="vendor_primary_contact_name" class="form-control" value="<?php echo $data[0][0]->vendor_primary_contact_name; ?>">
    </div>

    <div class="form-group">
        <label>Vendor Primary Contact (E-mail):</label>
        <input type="text" name="vendor_primary_contact_email" class="form-control" value="<?php echo $data[0][0]->vendor_primary_contact_email; ?>">
    </div>

    <div class="form-group">
        <label>Vendor Primary Contact (Phone):</label>
        <input type="text" name="vendor_primary_contact_phone" class="form-control" value="<?php echo $data[0][0]->vendor_primary_contact_phone; ?>">
    </div>

    <div class="form-group">
        <label>Facility Address:</label>
        <input type="text" name="facility_address" class="form-control" value="<?php echo $data[0][0]->facility_address; ?>">
    </div>


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


    <?php if (!empty($data[1])): ?>
    <div class="form-group">
{{--        <label>Competitor Analysis & Trend Report: </label>--}}
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
    </div>

    <div class="form-group">
        <label>Upload Visual References: <b style="color: #b91d19">(20MB Max)</b></label>
        <input type="file" data-asset="default" name="p_attachment[]" class="form-control p_attachment last_upload" multiple="multiple"/>
        <a href="javascript:void(0);" onclick="another_upload($(this))" class="another_upload">[ Upload Another ]</a>
    </div>

    <div class="form-group">


        <?php if (!empty($data[2]) && $data[2] == 'action_requested') { ?>
        <?php if(auth()->user()->role == 'Admin'
        || auth()->user()->id == $t_author
        || auth()->user()->team == 'QM QC') { ?>
        <input type="button"
               name="action start"
               value="Start"
               onclick="action_start($(this))"
               data-task-id="<?php echo $task_id; ?>"
               style="margin-top:10px;"
               class="btn btn-info submit"/>
        <?php } ?>
        <?php }?>

        <?php if (!empty($data[2]) && $data[2] == 'in_progress') { ?>
        <?php if(auth()->user()->role == 'Admin'
        || auth()->user()->id == $t_author
        || auth()->user()->team == 'QM QC') { ?>
            <div style="margin: 0 0 -13px 5px;">
                <label style="font-size: 13px; font-family: sans-serif; color: #b91d19;"><b>* Save Changes before Action Review *</b></label>
            </div>
        <input type="button"
               value="Review"
               onclick="action_review($(this))"
               data-task-id="<?php echo $task_id; ?>"
               style="margin-top:10px;"
               class="btn btn-info submit"/>
            <input type="submit" name="submit" value="Save" style="margin-top:10px; font-size: large" class="btn btn-lg btn-review submit"/>
        <?php } ?>
        <?php }?>

        <?php if (!empty($data[2]) && $data[2] == 'action_review') { ?>
        <?php if(auth()->user()->role == 'Admin'
        || auth()->user()->id == $t_author) { ?>
        <input type="button"
               value="Complete"
               onclick="action_complete($(this))"
               data-task-id="<?php echo $task_id; ?>"
               style="margin-top:10px;"
               class="btn btn-complete submit"/>
        <?php } ?>
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
</script>
