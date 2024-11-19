<?php $request_type = 'trademark'; ?>

<form method="POST" action="{{ route('legal_request.add_trademark') }}" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="{{ $request_type }}_t_id" value="{{ $task_id }}" />
    <input type="hidden" name="{{ $request_type }}_request_type" value="{{ $request_type }}" />
    <input type="hidden" name="{{ $request_type }}_author_id" value="{{ Auth::user()->id }}" />

    <div class="trademark">
        <p style="color: #b91d19; text-align: center; font-weight: bold;">TRADEMARK</p>

        <div class="form-group">
            <label>Description:</label>
{{--            <textarea class="form-control" style="height:100px;"--}}
{{--                      id="{{ $request_type }}_request_description"--}}
{{--                      name="{{ $request_type }}_request_description" required></textarea>--}}
            {!! Form::textarea($request_type.'_request_description', null, ['class' => 'form-control summernote']) !!}
        </div>

        <div class="form-group">
            <label>Trademark Owner: <b style="color: #b91d19">*</b></label>
            <select class="form-control" name="{{ $request_type }}_trademark_owner" required>
                <option value="">Select</option>
                <?php foreach($trademark_owner_list as $val): ?>
                <option value="{{ $val }}">{{ $val }}</option>
                <?php endforeach ?>
            </select>
        </div>

        <div class="form-group">
            <label>Description Of Goods: <b style="color: #b91d19">*</b></label>
            <input type="text" name="{{ $request_type }}_description_of_goods" onkeydown="return event.key !== 'Enter';" class="form-control" value="" required>
        </div>

        <div class="form-group">
            <label>Priority: <b style="color: #b91d19">*</b></label>
            <select class="form-control" name="{{ $request_type }}_priority" onchange="urgent_check($(this))" required>
                <?php foreach($priorities as $val): ?>
                <option value="{{ $val }}">{{ $val }}</option>
                <?php endforeach ?>
            </select>
        </div>

        <div class="form-group urgent_due_date" hidden>
            <label>Due Date (Urgent): <b style="color: #b91d19">(Select from the calendar)</b></label>
            <input type="text" name="{{ $request_type }}_due_date_urgent" id="due_date"
                   class="form-control @error($request_type.'_due_date_urgent') is-invalid @enderror @if (!$errors->has($request_type.'_due_date_urgent') && old($request_type.'_due_date_urgent')) is-valid @endif"
                   value="{{ old($request_type . '_due_date_urgent', null) }}">
        </div>

        <div class="form-group urgent_due_date" hidden>
            <label>Urgent Reason:</label>
            <textarea class="form-control" name="{{ $request_type }}_urgent_reason" style="height:100px;"></textarea>
        </div>

        <div class="form-group">
            <label>Due Date: <b style="color: #b91d19">*</b></label>
            <input type="text" name="{{ $request_type }}_due_date" required autocomplete="off"
                   class="form-control @error($request_type.'_due_date') is-invalid @enderror @if (!$errors->has($request_type.'_due_date') && old($request_type.'_due_date')) is-valid @endif"
                   value="{{ old($request_type . '_due_date', null) }}">
        </div>

        <div class="form-group">
            <label>Target Region:</label>
            <div class="columns" style="column-count: 4;">
                <?php foreach($target_regions as $value): ?>
                <div class="col-md">
                    <div class="form-check" style="padding-left: 0px;">
                        <input  type="checkbox"
                                name="{{ $request_type }}_target_region[]"
                                value="{{ $value }}" id="trademark_{{$value}}"
                        >
                        <label class="form-check-label " for="trademark_{{ $value }}">
                            {{ $value }}
                        </label>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="form-group">
            <label>If others, please specify:</label>
            <input type="text" name="{{ $request_type }}_if_selected_others" onkeydown="return event.key !== 'Enter';" class="form-control" value="">
        </div>

        <div class="form-group">
            <label>Attachment: <b style="color: #b91d19">(20MB Max)</b></label>
            <input type="file" data-asset="default" name="{{ $request_type }}_attachment[]" class="form-control p_attachment last_upload" multiple="multiple"/>
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
            var count = 10; // past 4pm
            var count_urgent = 3;
        }else{
            var count = 9; // before 4pm
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
        $('input[name="<?php echo $request_type; ?>_due_date"]').val(today.toISOString().split('T')[0]);

        var today_urgent = new Date();
        for (let i = 1; i <= count_urgent; i++) {
            today_urgent.setDate(today_urgent.getDate() + 1);
            if (today_urgent.getDay() === 6) {
                today_urgent.setDate(today_urgent.getDate() + 2);
            }
            else if (today_urgent.getDay() === 0) {
                today_urgent.setDate(today_urgent.getDate() + 1);
            }
        }
        $('input[name="<?php echo $request_type; ?>_due_date_urgent"]').daterangepicker({
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


