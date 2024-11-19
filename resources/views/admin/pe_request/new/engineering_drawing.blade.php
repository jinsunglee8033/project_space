<?php $request_type = 'engineering_drawing'; ?>

<form method="POST" action="{{ route('pe_request.add_engineering_drawing') }}" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="{{ $request_type }}_t_id" value="{{ $task_id }}" />
    <input type="hidden" name="{{ $request_type }}_request_type" value="{{ $request_type }}" />
    <input type="hidden" name="{{ $request_type }}_author_id" value="{{ Auth::user()->id }}" />

    <div class="engineering_drawing">
        <p style="color: #b91d19; text-align: center; font-weight: bold;">Engineering Drawing</p>

        <div class="form-group">
            <label>Request Detail: <b style="color: #b91d19">*</b></label>
            <textarea class="form-control" style="height:100px;"
                      id="{{ $request_type }}_request_detail"
                      name="{{ $request_type }}_request_detail" required></textarea>
{{--            {!! Form::textarea($request_type.'_request_detail', null, ['class' => 'form-control summernote']) !!}--}}
        </div>

        <div class="form-group">
            <label>Due Date: <b style="color: #b91d19">*</b> <b style="color: #b91d19">(Select from the calendar)</b></label>
            <input type="text" name="{{ $request_type }}_due_date" id="new_due_date" required autocomplete="off"
                   class="form-control @error($request_type.'_due_date') is-invalid @enderror @if (!$errors->has($request_type.'_due_date') && old($request_type.'_due_date')) is-valid @endif"
                   value="{{ old($request_type . '_due_date', null) }}">
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
    // Lead time +28 days - Email Blast (exclude weekend)
    $(function() {

        $('input[name="<?php echo $request_type; ?>_due_date"]').daterangepicker({
            singleDatePicker: true,
            locale: {
                format: 'YYYY-MM-DD'
            },
            // isInvalidDate: function(date) {
            //     return (date.day() == 0 || date.day() == 6);
            // },
        });
        $('input[name="<?php echo $request_type; ?>_due_date"]').val('');
    });
</script>


