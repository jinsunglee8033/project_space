<?php $request_type = 'sample'; ?>

<form method="POST" action="{{ route('pe_request.add_sample') }}" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="{{ $request_type }}_t_id" value="{{ $task_id }}" />
    <input type="hidden" name="{{ $request_type }}_request_type" value="{{ $request_type }}" />
    <input type="hidden" name="{{ $request_type }}_author_id" value="{{ Auth::user()->id }}" />

    <div class="sample">
        <p style="color: #b91d19; text-align: center; font-weight: bold;">Sample</p>

        <div class="form-group">
            <label>Request Detail: <b style="color: #b91d19">*</b></label>
            <textarea class="form-control" style="height:100px;"
                      id="{{ $request_type }}_request_detail"
                      name="{{ $request_type }}_request_detail" required></textarea>
{{--            {!! Form::textarea($request_type.'_request_detail', null, ['class' => 'form-control summernote']) !!}--}}
        </div>

        <div class="form-group">
            <label>Total Quantity: <b style="color: #b91d19">*</b></label>
            <input type="text" name="{{ $request_type }}_total_quantity" class="form-control" value="" required>
        </div>

        <div class="form-group">
            <label>Item Number for Molded Part: <b style="color: #b91d19">*</b></label>
            <input type="text" name="{{ $request_type }}_item_number" class="form-control" value="" required>
        </div>

        <div class="form-group">
            <label>Color & Pattern #: <b style="color: #b91d19">*</b></label>
            <textarea class="form-control" style="height:100px;"
                      id="{{ $request_type }}_color_pattern"
                      name="{{ $request_type }}_color_pattern" required></textarea>
        </div>

        <div class="form-group">
            <label>Tooling Budget Code: <b style="color: #b91d19">*</b></label>
            <input type="text" name="{{ $request_type }}_tooling_budget_code" class="form-control" value="" required>
        </div>

        <div class="form-group">
            <label>Due Date: <b style="color: #b91d19">*</b> <b style="color: #b91d19">(Select from the calendar)</b></label>
            <input type="text" name="{{ $request_type }}_due_date" class="form-control" value="" required>
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


