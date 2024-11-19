<?php $request_type = 'new'; ?>

<form method="POST" action="{{ route('mm_request.add_new') }}" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="{{ $request_type }}_t_id" value="{{ $task_id }}" />
    <input type="hidden" name="{{ $request_type }}_request_type" value="{{ $request_type }}" />
    <input type="hidden" name="{{ $request_type }}_author_id" value="{{ Auth::user()->id }}" />

    <div class="new">
        <p style="color: #b91d19; text-align: center; font-weight: bold;">NEW</p>

        <div class="form-group">
            <label>Request Detail: <b style="color: #b91d19">*</b></label>
            <textarea class="form-control" name="{{ $request_type }}_remark" style="height:100px;" required></textarea>
        </div>

        <div class="form-group">
            <label>Materials: <b style="color: #b91d19">*</b></label>
            <textarea class="form-control" name="{{ $request_type }}_materials" style="height:200px;" required></textarea>
        </div>

        <div class="form-group">
            <label>Priority: <b style="color: #b91d19">*</b></label>
            <select class="form-control" name="{{ $request_type }}_priority" onchange="urgent_check($(this))" required>
                {{--            <option value="">Select</option>--}}
                <?php foreach($priorities as $val): ?>
                <option value="{{ $val }}">{{ $val }}</option>
                <?php endforeach ?>
            </select>
        </div>

        <div class="form-group urgent_due_date" hidden>
            <label>Due Date (Urgent): <b style="color: #b91d19">(Select from the calendar)</b></label>
            <input required type="text" name="{{ $request_type }}_due_date_urgent" id="{{ $request_type }}_due_date_urgent"
                   class="form-control @error($request_type.'_due_date_urgent') is-invalid @enderror @if (!$errors->has($request_type.'_due_date_urgent') && old($request_type.'_due_date_urgent')) is-valid @endif"
                   value="{{ old($request_type . '_due_date_urgent', null) }}">
        </div>

        <div class="form-group urgent_due_date" hidden>
            <label>Urgent Reason:</label>
            <select class="form-control" name="{{ $request_type }}_urgent_reason" id="{{ $request_type }}_urgent_reason">
                <option value="">Select</option>
                <?php foreach($urgent_reason_list as $val): ?>
                <option value="{{ $val }}">{{ $val }}</option>
                <?php endforeach ?>
            </select>
        </div>

        <div class="form-group urgent_due_date" hidden>
            <label>Urgent Detail:</label>
            <textarea class="form-control" name="{{ $request_type }}_urgent_detail" id="{{ $request_type }}_urgent_detail" style="height:100px;"></textarea>
        </div>

        <div class="form-group">
            <label>Due Date:</label>
            <input type="text" name="{{ $request_type }}_due_date" id="{{ $request_type }}_due_date" class="form-control" value="" readonly>
        </div>

        <div class="form-group">
            <label>Set-up Plant(s):</label>
            <div class="columns" style="column-count: 3;">
                <?php foreach($mm_request_set_up_plants as $value): ?>
                <div class="col-md">
                    <div class="form-check" style="padding-left: 0px;">
                        <input  type="checkbox"
                                name="{{ $request_type }}_set_up_plant[]"
                                value="{{ $value->name }}" id="new_{{ $value->name }}"
                        >
                        <label class="form-check-label " for="new_{{ $value->name }}">
                            {{ $value->name }}
                        </label>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="form-group">
            <label>Attachment: <b style="color: #b91d19">* (20MB Max)</b></label>
            <input type="file" data-asset="default" name="{{ $request_type }}_attachment[]" required class="form-control p_attachment last_upload" multiple="multiple"/>
            <a href="javascript:void(0);" onclick="another_upload($(this))" class="another_upload">[ Upload Another ]</a>
        </div>
    </div>

    <div class="form-group">
        <input type="submit" name="submit" value="Create" style="margin-top:10px; padding: .55rem 1.5rem; font-size: medium;" class="btn btn-create submit"/>
    </div>

</form>

<script type="text/javascript">
    $(function() {
        var today = new Date();

        if(today.getHours() >= 16){
            var count = 3; // past 4pm
            var count_urgent = 3;
        }else{
            var count = 2; // before 4pm
            var count_urgent = 2;
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
        $('input[id="<?php echo $request_type; ?>_due_date"]').val(today.toISOString().split('T')[0]);

        var today_urgent = new Date();
        $('input[id="{{ $request_type }}_due_date_urgent"]').daterangepicker({
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


