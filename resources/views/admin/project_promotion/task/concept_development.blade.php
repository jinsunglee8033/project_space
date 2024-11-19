<?php $task_id = $data[0][0]->task_id; $t_type = $data[0][0]->type; $t_author = $data[0][0]->author_id; ?>


<form method="POST" action="{{ route('project.edit_concept_development', $task_id) }}" enctype="multipart/form-data">
    @csrf

    <div class="form-group">
        <label>Benchmark:</label>
        {!! Form::textarea('benchmark', !empty($data[0][0]) ? $data[0][0]->benchmark : null, ['class' => 'form-control summernote']) !!}
    </div>

    <div class="form-group">
        <label>Delivery Date</label>
        <input type="text" name="delivery_date" id="{{$task_id}}_delivery_date" placeholder="Delivery Date" autocomplete="off"
               class="form-control @error('delivery_date') is-invalid @enderror @if (!$errors->has('delivery_date') && old('delivery_date')) is-valid @endif"
               value="{{ old('delivery_date', !empty($data[0][0]) ? $data[0][0]->delivery_date : null) }}">
    </div>

    <?php if (!empty($data[1])): ?>
    <div class="form-group">
        <label>Competitor Analysis & Trend Report: </label>
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

        <?php if (!empty($data[2]) && $data[2] == 'in_progress') { ?>
        <?php if(auth()->user()->role == 'Admin'
        || auth()->user()->id == $t_author) { ?>
            <div>
                <label style="font-size: medium;">Save changes before submitting for done</label>
            </div>
        <input type="button"
               value="Submit for Done"
               onclick="task_done($(this))"
               data-task-id="<?php echo $task_id; ?>"
               style="margin-top:10px;"
               class="btn btn-info submit"/>
        <?php } ?>
        <?php }?>

        <?php if (!empty($data[2]) && ( $data[2] != 'done' ) ) { ?>
        <input type="submit" name="submit" value="Save" style="margin-top:10px; font-size: large" class="btn btn-lg btn-primary submit"/>
        <input type="hidden" name="status" value="{{ $data[2] }}"/>
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
        var lead_time = "<?php echo $data[0][0]->delivery_date; ?>"
        $('input[id="<?php echo $task_id;?>_delivery_date"]').daterangepicker({
            singleDatePicker: true,
            minDate:lead_time,
            locale: {
                format: 'YYYY-MM-DD'
            },
        });
    });
</script>
