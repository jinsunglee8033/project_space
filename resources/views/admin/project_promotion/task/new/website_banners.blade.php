<?php $asset_type = 'website_banners'; ?>

<form method="POST" action="{{ route('campaign.add_website_banners') }}" enctype="multipart/form-data">
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
        <label>Deadline Date: (Lead Time 29 Days)<span class="req" title="Required"> *</span></label>
        <input type="text" name="{{ $asset_type }}_launch_date" id="{{ $asset_type }}_launch_date" required autocomplete="off"
               class="form-control @error($asset_type.'_launch_date') is-invalid @enderror @if (!$errors->has($asset_type.'_launch_date') && old($asset_type.'_launch_date')) is-valid @endif"
               value="{{ old('launch_date', null) }}">
    </div>

    <div class="form-group checkboxes">
        <label>Banner: * (Choose one or more)</label>
        <a href="javascript:void(0)" class="kiss-info-icon" tabindex="-1" title="Choose one or more"></a><br/>
        <?php foreach($banner as $checkbox_field): ?>
            <input
                type="checkbox"
                name="{{ $asset_type }}_banner[]"
                value="<?php echo $checkbox_field; ?>"
            />
            <span> <?php echo $checkbox_field; ?></span><br/>
        <?php endforeach; ?>
    </div>

    <div class="form-group">
        <label>Details:</label>
        <textarea class="form-control" id="{{ $asset_type }}_details" name="{{ $asset_type }}_details" rows="5" cols="100"></textarea>
    </div>

    <div class="form-group">
        <hr>
        <label style="display: inline-flex; align-items: center;">
            <input type="checkbox" name="{{ $asset_type }}_no_copy_necessary" value="on" class="custom-switch-input">
            <span class="custom-switch-indicator"></span>
            <span class="custom-switch-description">No Copy Necessary</span>
        </label>
    </div>

    <div class="form-group">
        <label>Copy Needed: (if applicable)</label>
        {!! Form::textarea($asset_type.'_copy', null, ['class' => 'form-control summernote']) !!}
        <input type="checkbox" onchange="copy_requested_toggle($(this))"/>
        <label style="color: #98a6ad">Request Copy</label>
    </div>

    <div class="form-group">
        <label>Products Featured:</label>
        <input type="text" name="{{ $asset_type }}_products_featured" class="form-control" value="">
        <input type="checkbox" onchange="copy_requested_toggle($(this))"/>
        <label style="color: #98a6ad">Request Copy</label>
    </div>

    <div class="form-group">
        <label>Destination URL:</label>
        <div class="input-group" title="">
            <input type="text" name="{{ $asset_type }}_click_through_links" class="form-control" placeholder="https://www.example.com" value=""/>
        </div>
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
    // Lead time +29 days - Web Banners (exclude weekend)
    $(function() {
        var count = 29;
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
