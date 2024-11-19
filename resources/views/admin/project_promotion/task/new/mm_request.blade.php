<?php $task_type = 'mm_request'; ?>

<form method="POST" action="{{ route('project.add_mm_request') }}" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="{{ $task_type }}_p_id" value="{{ $project->id }}" />
    <input type="hidden" name="{{ $task_type }}_task_type" value="{{ $task_type }}" />
    <input type="hidden" name="{{ $task_type }}_author_id" value="{{ Auth::user()->id }}" />

    <div class="form-group">
        <label>Materials:</label>
        <input type="text" name="{{ $task_type }}_materials" class="form-control" value="" required>
    </div>

    <div class="form-group">
        <label>Priority:</label>
        <select class="form-control" name="{{ $task_type }}_priority">
            <option value="">Select</option>
            <?php foreach($priorities as $val): ?>
            <option value="{{ $val }}">{{ $val }}</option>
            <?php endforeach ?>
        </select>
    </div>

    <div class="form-group">
        <label>Due Date</label>
        <input required type="text" name="{{ $task_type }}_due_date" id="due_date" required autocomplete="off"
               class="form-control @error($task_type.'_due_date') is-invalid @enderror @if (!$errors->has($task_type.'_due_date') && old($task_type.'_due_date')) is-valid @endif"
               value="{{ old($task_type . '_due_date', null) }}">
    </div>

    <div class="form-group">
        <label>Request Type: <a target="_blank" href="https://drive.google.com/drive/folders/1xkBEXtq8A5XAP20FpuoSLUsFxwKQY8Cj?usp=drive_link" >Download Link</a></label>
        <select class="form-control" name="{{ $task_type }}_request_type">
            <option value="">Select</option>
            <?php foreach($mm_request_types as $val): ?>
            <option value="{{ $val }}">{{ $val }}</option>
            <?php endforeach ?>
        </select>
    </div>

    <div class="form-group">
        <label>Set-up Plant(s):</label>
        <div class="columns" style="column-count: 3;">
            <?php foreach($mm_request_set_up_plants as $value): ?>
            <div class="col-md">
                <div class="form-check" style="padding-left: 0px;">
                    <input  type="checkbox"
                            name="{{ $task_type }}_set_up_plant[]"
                            value="{{ $value }}"
                    >
                    <label class="form-check-label " for="{{ $value }}">
                        {{ $value }}
                    </label>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="form-group">
        <label>Remark:</label>
        <textarea class="form-control" style="height:100px;"
                  id="{{ $task_type }}_remark"
                  name="{{ $task_type }}_remark"></textarea>
    </div>

    <div class="form-group">
        <label>Attachments: <b style="color: #b91d19">(20MB Max)</b></label>
        <input type="file" data-asset="default" name="{{ $task_type }}_p_attachment[]" class="form-control p_attachment last_upload" multiple="multiple"/>
        <a href="javascript:void(0);" onclick="another_upload($(this))" class="another_upload">[ Upload Another ]</a>
    </div>

    <div class="form-group">
        <input type="submit" name="submit" value="Create" style="margin-top:10px;" class="btn btn-create submit"/>
    </div>

</form>

<script type="text/javascript">
    $(function() {
        var today = new Date();
        $('input[name="<?php echo $task_type; ?>_due_date"]').daterangepicker({
            singleDatePicker: true,
            minDate: today,
            locale: {
                format: 'YYYY-MM-DD'
            },
        });
        $('input[name="<?php echo $task_type; ?>_due_date"]').val('');
    });
</script>
