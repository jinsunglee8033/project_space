<?php $asset_type = 'roll_over'; ?>

<form method="POST" action="{{ route('campaign.add_roll_over') }}" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="{{ $asset_type }}_c_id" value="{{ $campaign->id }}" />
    <input type="hidden" name="{{ $asset_type }}_asset_type" value="{{ $asset_type }}" />
    <input type="hidden" name="{{ $asset_type }}_author_id" value="{{ Auth::user()->id }}" />

    <div class="form-group">
        <label class="form-label">Asset Creation Team:</label>
        <div class="selectgroup w-100">
            <label class="selectgroup-item">
                <input type="radio" name="{{ $asset_type }}_team_to" value="creative" class="selectgroup-input" checked="">
                <span class="selectgroup-button">Creative</span>
            </label>
            <label class="selectgroup-item">
                <input type="radio" name="{{ $asset_type }}_team_to" value="content" class="selectgroup-input">
                <span class="selectgroup-button">Content</span>
            </label>
            <label class="selectgroup-item">
                <input type="radio" name="{{ $asset_type }}_team_to" value="web production" class="selectgroup-input">
                <span class="selectgroup-button">Web Production</span>
            </label>
        </div>
    </div>

    <div class="form-group">
        <label>SKU:</label>
        <div class="input-group" title="">
            <input type="text" name="{{ $asset_type }}_sku" class="form-control" value=""/>
        </div>
    </div>

    <div class="form-group">
        <label>Products Featured:</label>
        <textarea class="form-control" name="{{ $asset_type }}_products_featured" rows="5" cols="100" style="height:100px;"></textarea>
    </div>

    <div class="form-group">
        <label>Launch Date: (Lead Time 15 Days)<span class="req" title="Required"> *</span></label>
        <input type="text" name="{{ $asset_type }}_launch_date" id="{{ $asset_type }}_launch_date" required autocomplete="off"
               class="form-control @error($asset_type.'_launch_date') is-invalid @enderror @if (!$errors->has($asset_type.'_launch_date') && old($asset_type.'_launch_date')) is-valid @endif"
               value="{{ old($asset_type.'_launch_date', null) }}">
    </div>

    <div class="form-group">
        <label>Notes</label>
        {!! Form::textarea($asset_type.'_notes', null, ['class' => 'form-control summernote']) !!}
    </div>

    <div class="form-group">
        <label>Upload Visual References: <b style="color: #b91d19">(20MB Max)</b></label>
        <input type="file" data-asset="default" name="{{ $asset_type }}_c_attachment[]" class="form-control c_attachment last_upload" multiple="multiple"/>
        <a href="javascript:void(0);" onclick="another_upload($(this))" class="another_upload">[ Upload Another ]</a>
    </div>

    <div class="form-group">
        <input type="submit" name="submit" value="Create Asset" style="margin-top:10px;" class="btn btn-primary submit"/>
    </div>

</form>

<script type="text/javascript">
    // Lead time +15 days - Roll Over (exclude weekend)
    $(function() {
        var count = 15;
        var today = new Date();
        for (let i = 1; i <= count; i++) {
            today.setDate(today.getDate() + 1);
            if (today.getDay() === 6) {
                today.setDate(today.getDate() + 2);
            }
            else if (today.getDay() === 0) {
                today.setDate(today.getDate() + 1);
            }
        }
        $('input[name="<?php echo $asset_type; ?>_launch_date"]').daterangepicker({
            singleDatePicker: true,
            minDate: today,
            locale: {
                format: 'YYYY-MM-DD'
            },
            isInvalidDate: function(date) {
                return (date.day() == 0 || date.day() == 6);
            },
        });
        $('input[name="<?php echo $asset_type; ?>_launch_date"]').val('');
    });
</script>

