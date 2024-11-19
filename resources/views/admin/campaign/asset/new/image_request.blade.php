<?php $asset_type = 'image_request'; ?>

<form method="POST" action="{{ route('campaign.add_image_request') }}" enctype="multipart/form-data">
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
        <label>Client:</label>
        <div class="input-group" title="">
            <input type="text" name="{{ $asset_type }}_client" class="form-control" value=""/>
        </div>
    </div>

    <div class="form-group">
    <label>Launch Date: (Lead Time 14 Days)<span class="req" title="Required"> *</span></label>
    <input type="text" name="{{ $asset_type }}_launch_date" id="launch_date" required autocomplete="off"
           class="form-control @error($asset_type.'_launch_date') is-invalid @enderror @if (!$errors->has($asset_type.'_launch_date') && old($asset_type.'_launch_date')) is-valid @endif"
           value="{{ old($asset_type.'_launch_date', null) }}">
    </div>

    <div class="form-group">
        <label>Image Dimensions:</label>
        <div class="input-group" title="">
            <input type="text" name="{{ $asset_type }}_image_dimensions" class="form-control" value=""/>
        </div>
    </div>

    <div class="form-group">
        <label>Image Ratio:</label>
        <div class="input-group" title="">
            <input type="text" name="{{ $asset_type }}_image_ratio" class="form-control" value=""/>
        </div>
    </div>

    <div class="form-group">
        <label>Image Format:</label>
        <div class="input-group" title="">
            <input type="text" name="{{ $asset_type }}_image_format" class="form-control" value=""/>
        </div>
    </div>

    <div class="form-group">
        <label>Max Filesize:</label>
        <div class="input-group" title="">
            <input type="text" name="{{ $asset_type }}_max_filesize" class="form-control" value=""/>
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
        <label>Description</label>
        {!! Form::textarea($asset_type.'_description', null, ['class' => 'form-control summernote']) !!}
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
    // Lead time +14 days - Image Request (exclude weekend)
    $(function() {
        var count = 14;
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
