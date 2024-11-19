<?php $task_type = 'mm_request'; ?>

<div class="inner_box" style="margin: 30px 15px 0px 20px;">

    <form method="POST" action="{{ route('mm_request.add_mm_request') }}" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="{{ $task_type }}_p_id" value="{{ $project->id }}" />
    <input type="hidden" name="{{ $task_type }}_task_type" value="{{ $task_type }}" />
    <input type="hidden" name="{{ $task_type }}_author_id" value="{{ Auth::user()->id }}" />

    <div class="form-group">
        <label>Request Type:
            <a href="https://drive.google.com/drive/folders/1xkBEXtq8A5XAP20FpuoSLUsFxwKQY8Cj?usp=drive_link" target="_blank" class="badge badge-danger">Download Link</a>
        </label>
        <select class="form-control" name="{{ $task_type }}_request_type" required>
            <option value="">Select</option>
            <?php foreach($mm_request_types as $val): ?>
            <option value="{{ $val }}">{{ $val }}</option>
            <?php endforeach ?>
        </select>
    </div>

    <div class="form-group">
        <label>Request Detail: </label>
        <textarea class="form-control" name="{{ $task_type }}_remark" style="height:100px;" required></textarea>
    </div>

    <div class="form-group">
        <label>Materials:</label>
        <textarea class="form-control" name="{{ $task_type }}_materials" style="height:200px;" required></textarea>
    </div>

    <div class="form-group">
        <label>Priority:</label>
        <select class="form-control" name="{{ $task_type }}_priority" onchange="urgent_check($(this))" required>
{{--            <option value="">Select</option>--}}
            <?php foreach($priorities as $val): ?>
            <option value="{{ $val }}">{{ $val }}</option>
            <?php endforeach ?>
        </select>
    </div>

    <div class="form-group urgent_due_date" hidden>
        <label>Due Date (Urgent):</label>
        <input required type="text" name="{{ $task_type }}_due_date_urgent" id="due_date"
               class="form-control @error($task_type.'_due_date_urgent') is-invalid @enderror @if (!$errors->has($task_type.'_due_date_urgent') && old($task_type.'_due_date_urgent')) is-valid @endif"
               value="{{ old($task_type . '_due_date_urgent', null) }}">
    </div>

    <div class="form-group urgent_due_date">
        <label>Urgent Reason:</label>
        <select class="form-control" name="{{ $task_type }}_urgent_reason" id="{{ $task_type }}_urgent_reason">
            <option value="">Select</option>
            <?php foreach($urgent_reason_list as $val): ?>
            <option value="{{ $val }}">{{ $val }}</option>
            <?php endforeach ?>
        </select>
    </div>

    <div class="form-group urgent_due_date" hidden>
        <label>Urgent Detail:</label>
        <textarea class="form-control" name="{{ $task_type }}_urgent_reason" style="height:100px;"></textarea>
    </div>

    <div class="form-group">
        <label>Due Date</label>
        <input type="text" name="{{ $task_type }}_due_date" class="form-control" value="" readonly>
    </div>

    <div class="form-group">
        <label>Set-up Plant(s):</label>
        <div class="columns" style="column-count: 3;">
            <?php foreach($mm_request_set_up_plants as $value): ?>
            <div class="col-md">
                <div class="form-check" style="padding-left: 0px;">
                    <input  type="checkbox"
                            name="{{ $task_type }}_set_up_plant[]"
                            value="{{ $value->name }}"
                    >
                    <label class="form-check-label " for="{{ $value->name }}">
                        {{ $value->name }}
                    </label>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="form-group">
        <label><b style="color: #b91d19;">*</b> Attachments: <b style="color: #b91d19">(20MB Max)</b></label>
        <input type="file" data-asset="default" name="{{ $task_type }}_p_attachment[]" class="form-control p_attachment last_upload" multiple="multiple" required/>
        <a href="javascript:void(0);" onclick="another_upload($(this))" class="another_upload">[ Upload Another ]</a>
    </div>

    <div class="form-group">
        <input type="submit" name="submit" value="Create" style="margin-top:10px; font-size: medium;" class="btn btn-lg btn-create submit"/>
    </div>

</form>

</div>
<script type="text/javascript">
    $(function() {

        var today = new Date();
        if(today.getHours() >= 16){
            var count = 3; // past 4pm
        }else{
            var count = 2; // before 4pm
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
        $('input[name="<?php echo $task_type; ?>_due_date"]').val(today.toISOString().split('T')[0]);

        var today_urgent = new Date();
        $('input[name="<?php echo $task_type; ?>_due_date_urgent"]').daterangepicker({
            singleDatePicker: true,
            minDate: today_urgent,
            locale: {
                format: 'YYYY-MM-DD'
            },
        });

    });

    function urgent_check(e){
        let priority = $(e).val();

        if(priority == 'Urgent') {
            $('.urgent_due_date').removeAttr('hidden');
        }else{
            $(".urgent_due_date").attr("hidden",true);
        }
    }


</script>
