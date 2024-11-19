<?php $task_type = 'concept_development'; ?>

<form method="POST" action="{{ route('project.add_concept_development') }}" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="{{ $task_type }}_p_id" value="{{ $project->id }}" />
    <input type="hidden" name="{{ $task_type }}_task_type" value="{{ $task_type }}" />
    <input type="hidden" name="{{ $task_type }}_author_id" value="{{ Auth::user()->id }}" />

    <div class="form-group">
        <label>Benchmark:</label>
        {!! Form::textarea($task_type.'_benchmark', null, ['class' => 'form-control summernote']) !!}
    </div>

    <div class="form-group">
        <label>Delivery Date (Due Date)</label>
        <input required type="text" name="{{ $task_type }}_due_date" id="new_due_date" required autocomplete="off"
               class="form-control @error($task_type.'_due_date') is-invalid @enderror @if (!$errors->has($task_type.'_due_date') && old($task_type.'_due_date')) is-valid @endif"
               value="{{ old($task_type . '_due_date', null) }}">
    </div>

    <div class="form-group">
        <label>Competitor Analysis & Trend Report: <b style="color: #b91d19">(20MB Max)</b></label>
        <input type="file" data-asset="default" name="{{ $task_type }}_p_attachment[]" class="form-control p_attachment last_upload" multiple="multiple"/>
        <a href="javascript:void(0);" onclick="another_upload($(this))" class="another_upload">[ Upload Another ]</a>
    </div>

    <div class="form-group">
        <input type="submit" name="submit" value="Create" style="margin-top:10px; padding: .55rem 1.5rem; font-size: medium;" class="btn btn-create submit"/>
    </div>

</form>

<script type="text/javascript">
    // Lead time +28 days - Email Blast (exclude weekend)
    $(function() {
        // var count = 28;
        var today = new Date();
        // for (let i = 1; i <= count; i++) {
        //     today.setDate(today.getDate() + 1);
        //     if (today.getDay() === 6) {
        //         today.setDate(today.getDate() + 2);
        //     }
        //     else if (today.getDay() === 0) {
        //         today.setDate(today.getDate() + 1);
        //     }
        // }
        $('input[name="<?php echo $task_type; ?>_due_date"]').daterangepicker({
            singleDatePicker: true,
            minDate: today,
            locale: {
                format: 'YYYY-MM-DD'
            },
            // isInvalidDate: function(date) {
            //     return (date.day() == 0 || date.day() == 6);
            // },
        });
        $('input[name="<?php echo $task_type; ?>_due_date"]').val('');
    });
</script>
