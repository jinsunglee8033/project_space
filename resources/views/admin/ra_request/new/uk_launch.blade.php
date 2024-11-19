<?php $request_type = 'uk_launch'; ?>

<form method="POST" action="{{ route('ra_request.add_uk_launch') }}" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="{{ $request_type }}_t_id" value="{{ $task_id }}" />
    <input type="hidden" name="{{ $request_type }}_request_type" value="{{ $request_type }}" />
    <input type="hidden" name="{{ $request_type }}_author_id" value="{{ Auth::user()->id }}" />

    <div class="registration_cnf">
        <p style="color: #b91d19; text-align: center; font-weight: bold;">UK LAUNCH (SCPN)</p>
        <div class="form-group">
            <a href="https://drive.google.com/drive/folders/1kCsyOnjxxQ0Z1wu7rbBRDyfe9gpilTMQ?usp=drive_link" target="_blank" class="badge badge-danger">View Submission Guideline</a>
        </div>

        <div class="form-group">
            <label>Requested Due Date: <b style="color: #b91d19">*</b> <b style="color: #b91d19">(The request will take 30 business days to complete after the SCPN request submission by RA marked as “PENDING (3rd Party).”)</b></label>
            <input type="text" name="{{ $request_type }}_due_date" required autocomplete="off"
                   class="form-control @error($request_type.'_due_date') is-invalid @enderror @if (!$errors->has($request_type.'_due_date') && old($request_type.'_due_date')) is-valid @endif"
                   value="{{ old($request_type . '_due_date', null) }}">
        </div>

        <div class="form-group">
            <label><b style="color: #b91d19;">*</b> Upload EU DOC: <b style="color: #b91d19">(20MB Max)</b></label>
            <input type="file" data-asset="default" name="{{ $request_type }}_attachment[]" class="form-control p_attachment last_upload" multiple="multiple" required/>
            <a href="javascript:void(0);" onclick="another_upload($(this))" class="another_upload">[ Upload Another ]</a>
        </div>
    </div>

    <div class="form-group">
        <input type="submit" name="submit" value="Create" style="margin-top:10px; padding: .55rem 1.5rem; font-size: medium;" class="btn btn-lg btn-create submit"/>
    </div>

</form>

<script type="text/javascript">
    $(function() {
        var today = new Date();
        if(today.getHours() >= 16){
            var count = 31; // past 4pm
        }else{
            var count = 30; // before 4pm
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

        $('input[name="<?php echo $request_type; ?>_due_date"]').daterangepicker({
            singleDatePicker: true,
            minDate: today,
            locale: {
                format: 'YYYY-MM-DD'
            },
            isInvalidDate: function(date) {
                return (date.day() == 0 || date.day() == 6);
            },
        });
        $('input[name="<?php echo $request_type; ?>_due_date"]').val('');
    });
</script>