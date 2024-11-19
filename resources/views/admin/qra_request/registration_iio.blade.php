<?php $qra_request_type_id = $data[0]->qra_request_type_id; $request_type = $data[0]->request_type;?>

<form method="POST" action="{{ route('qra_request.edit_registration_iio', $qra_request_type_id) }}" enctype="multipart/form-data">
    @csrf
        <input type="hidden" name="{{ $request_type }}_t_id" value="{{ $task_id }}" />
        <input type="hidden" name="{{ $request_type }}_request_type" value="{{ $request_type }}" />
        <input type="hidden" name="{{ $request_type }}_author_id" value="{{ Auth::user()->id }}" />

    <div class="registration_iio">

        <?php if (!empty($data[0]) && $data[0]->status == 'action_requested') { ?>
        <?php if(auth()->user()->role == 'Admin' || auth()->user()->team == 'Legal RA') { ?>

        <input type="button"
               name="action start"
               value="Start"
               onclick="action_start($(this))"
               data-request-type-id="<?php echo $qra_request_type_id; ?>"
               style="margin-top:-7px; float:left; font-size: medium;"
               class="btn btn-lg btn-success submit"/>
        <label style="font-size: medium; font-weight:100; color: #b91d19; padding: 8px 0 15px 20px;"><b>* Click to indicate the start of work.</b></label>

        <?php }?>
        <?php }?>

        <p style="color: #222; text-align: center; font-weight: bold;">REGISTRATION IIO</p>
            <div class="form-group">
                <a href="https://drive.google.com/drive/folders/1kCsyOnjxxQ0Z1wu7rbBRDyfe9gpilTMQ?usp=drive_link" target="_blank" class="badge badge-danger">View Submission Guideline</a>
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

            <hr style="border-width: 1px 0px 0px 0px; border-style:solid; border-color: #e0e0e0;
            height:1px; margin-top: 15px; margin-bottom: 15px; width:100%">

            <div style="background-color: #e9e9e9; padding: 10px 10px 10px 10px; margin: 0 0 10px 0;">
                <div class="form-group">
                    <label>Registration #:</label>
                    <input type="text" name="registration" class="form-control" value="{{ $data[0]->registration }}">
                </div>
            </div>

    </div>

    <div class="form-group">

        <?php if (!empty($data[0]) && $data[0]->status == 'in_progress') { ?>
        <?php if(auth()->user()->role == 'Admin' || auth()->user()->team == 'Legal RA') { ?>

        <input type="submit" name="submit" value="Save" style="margin-top:10px; font-size: large" class="btn btn-lg btn-primary submit"/>
        <label style="font-size: medium; font-family: sans-serif; color: #b91d19; padding: 0 0 0 20px;"><b>* Save all changes before clicking action buttons. *</b></label>
        <br>
        <input type="button"
               name="action review"
               value="Review"
               onclick="action_review($(this))"
               data-request-type-id="<?php echo $qra_request_type_id; ?>"
               style="margin-top:10px; font-size: medium;"
               class="btn btn-lg btn-review submit"/>

        <?php }?>
        <?php }?>

        <?php if (!empty($data[0]) && $data[0]->status == 'action_review') { ?>
        <?php if(auth()->user()->role == 'Admin'
        || auth()->user()->team == $data[0]->team) { ?>
        <input type="button"
               name="action complete"
               value="Complete"
               onclick="action_complete($(this))"
               data-request-type-id="<?php echo $qra_request_type_id; ?>"
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
