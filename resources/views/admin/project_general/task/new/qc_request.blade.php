<?php $task_type = 'qc_request'; ?>

<form method="POST" action="{{ route('project.add_qc_request') }}" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="{{ $task_type }}_p_id" value="{{ $project->id }}" />
    <input type="hidden" name="{{ $task_type }}_task_type" value="{{ $task_type }}" />
    <input type="hidden" name="{{ $task_type }}_author_id" value="{{ Auth::user()->id }}" />

    <div class="form-group">
        <label>Ship Date (ETD):</label>
        <input required type="text" name="{{ $task_type }}_ship_date" id="ship_date" autocomplete="off"
               class="form-control @error($task_type.'_ship_date') is-invalid @enderror @if (!$errors->has($task_type.'_ship_date') && old($task_type.'_ship_date')) is-valid @endif"
               value="{{ old($task_type . '_ship_date', null) }}">
    </div>

    <div class="form-group">
        <label>QC Date (Requested):</label>
        <input required type="text" name="{{ $task_type }}_qc_date" id="qc_date" autocomplete="off"
               class="form-control @error($task_type.'_qc_date') is-invalid @enderror @if (!$errors->has($task_type.'_qc_date') && old($task_type.'_qc_date')) is-valid @endif"
               value="{{ old($task_type . '_qc_date', null) }}">
    </div>

    <div class="form-group">
        <label>PO# (If PO is not released, fill in 'TBD'):</label>
        <input type="text" name="{{ $task_type }}_po" class="form-control" value="" required>
    </div>

    <div class="form-group">
        <label>PO (USD):</label>
        <input type="text" name="{{ $task_type }}_po_usd" class="form-control" value="">
    </div>

    <div class="form-group">
        <label>Materials# (Item code, SKU):</label>
        <input type="text" name="{{ $task_type }}_materials" class="form-control" value="" required>
    </div>

    <div class="form-group">
        <label>Item Type:</label>
        <input type="text" name="{{ $task_type }}_item_type" class="form-control" value="" required>
    </div>

    <div class="form-group">
        <label>Vendor Code:</label>
        <input type="text" name="{{ $task_type }}_vendor_code" class="form-control" value="" required>
    </div>

    <div class="form-group">
        <label>Country:</label>
        <input type="text" name="{{ $task_type }}_country" class="form-control" value="" required>
    </div>

    <div class="form-group">
        <label>Vendor Primary Contact (Name):</label>
        <input type="text" name="{{ $task_type }}_vendor_primary_contact_name" class="form-control" value="" required>
    </div>

    <div class="form-group">
        <label>Vendor Primary Contact (E-mail):</label>
        <input type="text" name="{{ $task_type }}_vendor_primary_contact_email" class="form-control" value="" >
    </div>

    <div class="form-group">
        <label>Vendor Primary Contact (Phone):</label>
        <input type="text" name="{{ $task_type }}_vendor_primary_contact_phone" class="form-control" value="" >
    </div>

    <div class="form-group">
        <label>Facility Address:</label>
        <input type="text" name="{{ $task_type }}_facility_address" class="form-control" value="" >
    </div>

    <div class="form-group">
        <label>Performed_by:</label>
        <select class="form-control" name="{{ $task_type }}_performed_by">
            <option value="">Select</option>
            <?php foreach($performed_bys as $val): ?>
            <option value="{{ $val }}">{{ $val }}</option>
            <?php endforeach ?>
        </select>
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
        $('input[name="<?php echo $task_type; ?>_ship_date"]').daterangepicker({
            singleDatePicker: true,
            minDate: today,
            locale: {
                format: 'YYYY-MM-DD'
            },
        });
        $('input[name="<?php echo $task_type; ?>_qc_date"]').daterangepicker({
            singleDatePicker: true,
            minDate: today,
            locale: {
                format: 'YYYY-MM-DD'
            },
        });
        $('input[name="<?php echo $task_type; ?>_ship_date"]').val('');
        $('input[name="<?php echo $task_type; ?>_qc_date"]').val('');
    });
</script>
