<?php $asset_id = $data[0][0]->asset_id; $c_id = $data[0][0]->id; $a_type = $data[0][0]->type; ?>

<?php if(!empty($data[8]) && (auth()->user()->role == 'admin' || auth()->user()->role == 'copywriter manager' ) && ($data[2] != 'copy_complete') ) { ?>
<div class="card" style="background-color: #f5f6fe; margin-bottom: 3px; margin-top: 3px;">
    <form method="POST" action="{{ route('asset.copy_writer_change') }}" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label style="padding-left: 20px; color: #b91d19;">Copywriter</label>
            <select class="form-control" name="copy_writer">
                <option value="">Select</option>
                @foreach ($copy_writers as $copy_writer)
                    <option value="{{ $copy_writer->first_name }}" {{ $copy_writer->first_name == $data[8] ? 'selected' : '' }}>
                        {{ $copy_writer->first_name }}
                    </option>
                @endforeach
            </select>
        </div>
        <input type="hidden" name="a_id" value="{{ $asset_id }}">
        <input type="hidden" name="c_id" value="{{ $c_id }}">
        <input type="hidden" name="a_type" value="{{ $a_type }}">
        <div class=" text-right">
            <button class="btn btn-primary">Change</button>
        </div>
    </form>
</div>
<?php } ?>

<?php if(!empty($data[6]) && $data[2] == 'to_do' && (auth()->user()->role == 'admin'
    || auth()->user()->role == 'creative director'
    || auth()->user()->role == 'content manager'
    || auth()->user()->role == 'web production manager' )) { ?>
<div class="card" style="background-color: #f5f6fe; margin-bottom: 3px; margin-top: 3px;">
    <form method="POST" action="{{ route('asset.assign') }}" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label style="padding-left: 20px; color: #b91d19;">Assignee Change</label>
            <select class="form-control" name="assignee">
                <option value="">Select</option>
                <?php if($data[7] == 'content'){ ?>
                @foreach ($assignees_content as $designer)
                    <option value="{{ $designer->first_name }}" {{ $designer->first_name == $data[6] ? 'selected' : '' }}>
                        {{ $designer->first_name }}
                    </option>
                @endforeach
                <?php }else if($data[7] == 'web production'){ ?>
                @foreach ($assignees_web as $designer)
                    <option value="{{ $designer->first_name }}" {{ $designer->first_name == $data[6] ? 'selected' : '' }}>
                        {{ $designer->first_name }}
                    </option>
                @endforeach
                <?php }else{ ?>
                @foreach ($assignees_creative as $designer)
                    <option value="{{ $designer->first_name }}" {{ $designer->first_name == $data[6] ? 'selected' : '' }}>
                        {{ $designer->first_name }}
                    </option>
                @endforeach
                <?php } ?>
            </select>
        </div>
        <input type="hidden" name="a_id" value="{{ $asset_id }}">
        <input type="hidden" name="c_id" value="{{ $c_id }}">
        <input type="hidden" name="a_type" value="{{ $a_type }}">
        <div class=" text-right">
            <button class="btn btn-primary">Change</button>
        </div>
    </form>
</div>
<?php } ?>

<?php if( ($data[2] == 'copy_complete' || $data[2] == 'to_do') && (auth()->user()->role == 'admin') && ($data[2] != 'copy_complete') ) { ?>
<div class="card" style="background-color: #f5f6fe; margin-bottom: 3px; margin-top: 25px;">
    <form method="POST" action="{{ route('asset.team_change') }}" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label style="padding-left: 20px; color: #b91d19;">Team Change</label>
            <select class="form-control" name="team_to_change">
                <option value="">Select</option>
                @foreach ($team_to_list as $team_to)
                    <option value="{{ $team_to }}" {{ $team_to == $data[7] ? 'selected' : '' }}>
                        {{ ucfirst($team_to) }}
                    </option>
                @endforeach
            </select>
        </div>
        <input type="hidden" name="a_id" value="{{ $asset_id }}">
        <input type="hidden" name="c_id" value="{{ $c_id }}">
        <input type="hidden" name="a_type" value="{{ $a_type }}">
        <div class=" text-right">
            <button class="btn btn-primary">Change</button>
        </div>
    </form>
</div>
<?php } ?>

<form method="POST" action="{{ route('campaign.edit_a_content', $asset_id) }}" enctype="multipart/form-data">
    @csrf

    <?php if (!empty($data[5])) { ?>
    <div class="form-group" style="padding-left: 10px;">
        <label style="color: #a50018; font-size: medium;"> * Decline Reason from Copy Review:</label>
        <textarea class="form-control" id="concept" name="concept" readonly style="height: 100px;">{{ $data[5] }}</textarea>
    </div>
    <?php } ?>

    <?php if (!empty($data[3])) { ?>
    <div class="form-group" style="padding-left: 10px;">
        <label style="color: #a50018; font-size: medium;"> * Decline Reason from Creator:</label>
        <textarea class="form-control" id="concept" name="concept" readonly style="height: 100px;">{{ $data[3] }}</textarea>
    </div>
    <?php } ?>

    <?php if (!empty($data[4])) { ?>
    <div class="form-group" style="padding-left: 10px;">
        <label style="color: #a50018; font-size: medium;"> * Decline Reason:</label>
        <textarea class="form-control" id="concept" name="concept" readonly style="height: 100px;">{{ $data[4] }}</textarea>
    </div>
    <?php } ?>

    <div class="form-group">
        <label class="form-label">Asset Creation Team:</label>
        <div class="selectgroup w-100">
            <label class="selectgroup-item">
                <input type="radio" name="team_to" value="creative" class="selectgroup-input" disabled <?php echo ($data[7] == 'creative') ? "checked" : ""; ?> >
                <span class="selectgroup-button">Creative</span>
            </label>
            <label class="selectgroup-item">
                <input type="radio" name="team_to" value="content" class="selectgroup-input" disabled <?php echo ($data[7] == 'content') ? "checked" : ""; ?>>
                <span class="selectgroup-button">Content</span>
            </label>
            <label class="selectgroup-item">
                <input type="radio" name="team_to" value="web production" class="selectgroup-input" disabled <?php echo ($data[7] == 'web production') ? "checked" : ""; ?>>
                <span class="selectgroup-button">Web Production</span>
            </label>
        </div>
    </div>

    <div class="form-group">
        <label>Launch Date: </label>
        <input type="text" name="launch_date" id="{{$asset_id}}_launch_date" placeholder="Launch Date" autocomplete="off"
               class="form-control @error('launch_date') is-invalid @enderror @if (!$errors->has('launch_date') && old('launch_date')) is-valid @endif"
               value="{{ old('launch_date', !empty($data[0][0]) ? $data[0][0]->launch_date : null) }}">
    </div>

    <?php
    $time_to_spare = ($data[9]->time_to_spare == 'N/A') ? 0 : $data[9]->time_to_spare;
    $kdo = ($data[9]->kdo == 'N/A') ? 0 : $data[9]->kdo;
    $development = ($data[9]->development == 'N/A') ? 0 : $data[9]->development;
    $final_review = ($data[9]->final_review == 'N/A') ? 0 : $data[9]->final_review;
    $creative_work = ($data[9]->creative_work == 'N/A') ? 0 : $data[9]->creative_work;
    $creator_assign = ($data[9]->creator_assign == 'N/A') ? 0 : $data[9]->creator_assign;
    $copy_review = ($data[9]->copy_review == 'N/A') ? 0 : $data[9]->copy_review;
    $copy = ($data[9]->copy == 'N/A') ? 0 : $data[9]->copy;
    $copywriter_assign = ($data[9]->copywriter_assign == 'N/A') ? 0 : $data[9]->copywriter_assign;

    $step_8 = $time_to_spare + $kdo;
    $step_7 = $step_8 + $development;
    $step_6 = $step_7 + $final_review;
    $step_5 = $step_6 + $creative_work;
    $step_4 = $step_5 + $creator_assign;
    $step_3 = $step_4 + $copy_review;
    $step_2 = $step_3 + $copy;
    $step_1 = $step_2 + $copywriter_assign;
    ?>

    <div class="form-group">
        <table class="reminder_table">
            <?php if($data[0][0]->no_copy_necessary != 'on') { ?>
            <tr>
                <td><span class="lead-time"><b>&nbspCopywriter Assign Start&nbsp</b></span></td>
                <td style="color: #b91d19"><span><b><?php echo date('m/d/Y', strtotime($data[0][0]->launch_date . ' -' . $step_1 . ' weekday')); ?></b></span></td>
            </tr>
            <tr>
                <td><span class="lead-time"><b>&nbspCopy Start&nbsp</b></span></td>
                <td style="color: #b91d19"><span><b><?php echo date('m/d/Y', strtotime($data[0][0]->launch_date . ' -' . $step_2 . ' weekday')); ?></b></span></td>
            </tr>
            <tr>
                <td><span class="lead-time"><b>&nbspCopy Review Start&nbsp</b></span></td>
                <td style="color: #b91d19"><span><b><?php echo date('m/d/Y', strtotime($data[0][0]->launch_date . ' -' . $step_3 . ' weekday')); ?></b></span></td>
            </tr>
            <?php } ?>
            <tr>
                <td><span class="lead-time"><b>&nbspCreator Assign Start&nbsp</b></span></td>
                <td style="color: #b91d19"><span><b><?php echo date('m/d/Y', strtotime($data[0][0]->launch_date . ' -' . $step_4 . ' weekday')); ?></b></span></td>
            </tr>
            <tr>
                <td><span class="lead-time"><b>&nbspCreative Work Start&nbsp</b></span></td>
                <td style="color: #b91d19"><span><b><?php echo date('m/d/Y', strtotime($data[0][0]->launch_date . ' -' . $step_5 . ' weekday')); ?></b></span></td>
            </tr>
            <tr>
                <td><span class="lead-time"><b>&nbspCreative Review Start&nbsp</b></span></td>
                <td style="color: #b91d19"><span><b><?php echo date('m/d/Y', strtotime($data[0][0]->launch_date . ' -' . $step_6 . ' weekday')); ?></b></span></td>
            </tr>
            <tr>
                <td><span class="lead-time"><b>&nbspDevelopment Start&nbsp</b></span></td>
                <td style="color: #b91d19"><span><b>N/A</b></span></td>
            </tr>
            <tr>
                <td><span class="lead-time"><b>&nbspE-Commerce Start&nbsp</b></span></td>
                <td style="color: #b91d19"><span><b><?php echo date('m/d/Y', strtotime($data[0][0]->launch_date . ' -' . $step_8 . ' weekday')); ?></b></span></td>
            </tr>
        </table>
    </div>


    <div class="form-group">
        <label>Product Line:</label>
        <input type="text" name="product_line" class="form-control" value="<?php echo $data[0][0]->product_line; ?>">
    </div>

    <div class="form-group">
        <label>Invision Link:</label>
        <input type="text" name="invision_link" class="form-control" value="<?php echo $data[0][0]->invision_link; ?>">
    </div>

    <div class="form-group">
        <label>Products Featured:</label>
        <textarea class="form-control" id="products_featured" name="products_featured" style="height:100px;">{{ $data[0][0]->products_featured }}</textarea>
    </div>

    <div class="form-group">
        <hr>
        <label style="display: inline-flex; align-items: center;">
            <input type="checkbox" name="no_copy_necessary" class="custom-switch-input" <?php echo ($data[0][0]->no_copy_necessary == 'on') ? "checked" : ""; ?>>
            <span class="custom-switch-indicator"></span>
            <span class="custom-switch-description">No Copy Necessary</span>
        </label>
    </div>

    <div class="form-group">
        <label>Note</label>
        {!! Form::textarea('note', !empty($data[0][0]) ? $data[0][0]->note : null, ['class' => 'form-control summernote']) !!}
    </div>

    <?php if (!empty($data[1])): ?>
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
    <?php endif; ?>

    <div class="form-group">
        <label>Upload Visual References: <b style="color: #b91d19">(20MB Max)</b></label>
        <input type="file" data-asset="default" name="c_attachment[]" class="form-control c_attachment last_upload" multiple="multiple"/>
        <a href="javascript:void(0);" onclick="another_upload($(this))" class="another_upload">[ Upload Another ]</a>
    </div>

    <div class="form-group">
        <?php if (!empty($data[2]) && $data[2] == 'copy_to_do') { ?>
        <?php if(auth()->user()->role == 'copywriter'
        || auth()->user()->role == 'copywriter manager'
        || auth()->user()->role == 'admin') { ?>
        <input type="button"
               name="copy start"
               value="Copy Start"
               onclick="copy_work_start($(this))"
               data-asset-id="<?php echo $asset_id; ?>"
               style="margin-top:10px;"
               class="btn btn-success submit"/>
        <?php } ?>
        <?php }?>

        <?php if (!empty($data[2]) && $data[2] == 'copy_in_progress') { ?>
        <?php if(auth()->user()->role == 'copywriter'
            || auth()->user()->role == 'copywriter manager'
            || auth()->user()->role == 'admin') { ?>
        <input type="button"
               value="Copy Review"
               onclick="change_to_copy_review($(this))"
               data-asset-id="<?php echo $asset_id; ?>"
               style="margin-top:10px;"
               class="btn btn-success submit"/>
        <?php } ?>
        <?php }?>

        <?php if (!empty($data[2]) && $data[2] == 'copy_review') { ?>
        <?php if(auth()->user()->role == 'ecommerce specialist'
        || auth()->user()->role == 'marketing'
        || auth()->user()->role == 'social media manager'
        || auth()->user()->role == 'admin') { ?>
        <input type="button"
               value="Copy Complete"
               onclick="change_to_copy_complete($(this))"
               data-asset-id="<?php echo $asset_id; ?>"
               style="margin-top:10px;"
               class="btn btn-info submit"/>
        <?php } ?>
        <?php }?>

        <?php if (!empty($data[2]) && $data[2] == 'to_do') { ?>
        <?php if(auth()->user()->role == 'graphic designer'
            || auth()->user()->role == 'content creator'
            || auth()->user()->role == 'web production'
            || auth()->user()->role == 'creative director'
            || auth()->user()->role == 'content manager'
            || auth()->user()->role == 'web production manager'
            || auth()->user()->role == 'admin') { ?>
        <input type="button"
               name="start"
               value="Start Asset"
               onclick="work_start($(this))"
               data-asset-id="<?php echo $asset_id; ?>"
               style="margin-top:10px;"
               class="btn btn-success submit"/>
        <?php } ?>
        <?php }?>

            <?php if (!empty($data[2]) && $data[2] == 'in_progress') { ?>
            <?php if(auth()->user()->role == 'graphic designer'
            || auth()->user()->role == 'content creator'
            || auth()->user()->role == 'web production'
            || auth()->user()->role == 'creative director'
            || auth()->user()->role == 'content manager'
            || auth()->user()->role == 'web production manager'
            || auth()->user()->role == 'admin') { ?>

            <div>
                <label style="font-size: medium;">Save changes before submitting for approval</label>
            </div>

            <input type="button"
                   value="Submit for Approval"
                   onclick="work_done($(this))"
                   data-asset-id="<?php echo $asset_id; ?>"
                   style="margin-top:10px;"
                   class="btn btn-info submit"/>
            <?php } ?>
            <?php }?>

            <?php if (!empty($data[2]) && $data[2] == 'done') { ?>
                <?php if(auth()->user()->role == 'graphic designer'
                || auth()->user()->role == 'ecommerce specialist'
                || auth()->user()->role == 'marketing'
                || auth()->user()->role == 'social media manager'
                || auth()->user()->role == 'creative director'
                || auth()->user()->role == 'content manager'
                || auth()->user()->role == 'web production manager'
                || auth()->user()->role == 'admin') { ?>
                <input type="button"
                       value="Final Approval"
                       onclick="final_approval($(this))"
                       data-asset-id="<?php echo $asset_id; ?>"
                       style="margin-top:10px;"
                       class="btn btn-primary submit"/>
                <?php } ?>
            <?php }?>

            <?php if (!empty($data[2]) && ( $data[2] != 'final_approval' ) ) { ?>
                <input type="submit" name="submit" value="Save" style="margin-top:10px; font-size: large" class="btn btn-lg btn-primary submit"/>
                <input type="hidden" name="status" value="{{ $data[2] }}"/>
            <?php }?>
    </div>
</form>

<?php if (!empty($data[2]) && $data[2] == 'copy_review') { ?>
<?php if(auth()->user()->role == 'graphic designer'
|| auth()->user()->role == 'ecommerce specialist'
|| auth()->user()->role == 'marketing'
|| auth()->user()->role == 'social media manager'
|| auth()->user()->role == 'admin') { ?>
<form method="POST" action="{{ route('asset.decline_copy') }}" enctype="multipart/form-data">
    @csrf
    <div class="card-body">
        <div class="form-group">
            <label>Decline Reason for Copy Review:</label>
            <textarea class="form-control" id="decline_copy" name="decline_copy" rows="15" cols="100" style="min-height: 200px;"></textarea>
        </div>
    </div>
    <input type="hidden" name="a_id" value="{{ $asset_id }}">
    <input type="hidden" name="c_id" value="{{ $c_id }}">
    <input type="hidden" name="a_type" value="{{ $a_type }}">
    <div class="card-footer text-right">
        <button class="btn btn-primary">Decline</button>
    </div>
</form>
<?php } ?>
<?php } ?>

<?php if (!empty($data[2]) && $data[2] == 'done') { ?>
    <?php if(auth()->user()->role == 'ecommerce specialist'
    || auth()->user()->role == 'marketing'
    || auth()->user()->role == 'social media manager'
    || auth()->user()->role == 'admin') { ?>
        <form method="POST" action="{{ route('asset.decline_kec') }}" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <div class="form-group">
                    <label>Decline Reason:</label>
                    <textarea class="form-control" id="decline_kec" name="decline_kec" rows="15" cols="100" style="min-height: 200px;"></textarea>
                </div>
            </div>
            <input type="hidden" name="a_id" value="{{ $asset_id }}">
            <input type="hidden" name="c_id" value="{{ $c_id }}">
            <input type="hidden" name="a_type" value="{{ $a_type }}">
            <div class="card-footer text-right">
                <button class="btn btn-primary">Decline</button>
            </div>
        </form>
    <?php } ?>
<?php } ?>

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
    // Lead time +41 days - A+ Content
    $(function() {
        var lead_time = "<?php echo $data[0][0]->launch_date; ?>"

        $('input[id="<?php echo $asset_id;?>_launch_date"]').daterangepicker({
            singleDatePicker: true,
            minDate:lead_time,
            locale: {
                format: 'YYYY-MM-DD'
            },
        });
    });
</script>
