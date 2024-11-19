<?php $asset_type = 'social_ad'; ?>

<form method="POST" action="{{ route('campaign.add_social_ad') }}" enctype="multipart/form-data">
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

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Run From: (Lead Time 28 Days)<span class="req" title="Required"> *</span></label>
                <input type="text" name="{{ $asset_type }}_date_from" id="date_from" placeholder="Start date" required autocomplete="off"
                       class="form-control @error('date_from') is-invalid @enderror @if (!$errors->has('date_from') && old('date_from')) is-valid @endif"
                       value="{{ old('date_from', null) }}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Run To: </label>
                <input type="text" name="{{ $asset_type }}_date_to" id="date_to" placeholder="End date" autocomplete="off"
                       class="form-control datepicker @error('date_to') is-invalid @enderror @if (!$errors->has('date_to') && old('date_to')) is-valid @endif"
                       value="{{ old('date_to', null) }}">
            </div>
        </div>
    </div>

    <div class="form-group">
        <label>Category:</label>
        <select class="form-control" name="category" onchange="select_category($(this))" required>
            <option value="">Select</option>
{{--            <option value="Awareness Social Ad" {{ $value == $promotion ? 'selected' : '' }}>{{ $value }}</option>--}}
            <option value="Social Ad Awareness">Social Ad Awareness</option>
            <option value="Social Ad Traffic">Social Ad Traffic</option>
            <option value="Social Ad Conversion">Social Ad Conversion</option>
            <option value="Social Ad Organic">Social Ad Organic</option>
            <option value="Google Ad">Google Ad</option>
        </select>
    </div>

    <div class="form-group skip-creative" hidden>
        <label style="display: inline-flex; align-items: center;">
            <input type="checkbox" name="skip-creative" class="custom-switch-input" onchange="skip_creative($(this))">
            <span class="custom-switch-indicator"></span>
            <span class="custom-switch-description">Skip Creative</span>
        </label>
    </div>

    <div class="form-group checkboxes">
        <label>Include Formats: </label>
        <div class="awareness" hidden style="columns: 2; -webkit-columns: 2; -moz-columns: 2;">
            <input type="checkbox" name="social_ad_include_formats[]" value="FB/IG Video Ad"/>&nbsp;<span>FB/IG Video Ad</span></br>
            <input type="checkbox" name="social_ad_include_formats[]" value="FB/IG GIF Ad"/>&nbsp;<span>FB/IG GIF Ad</span></br>
            <input type="checkbox" name="social_ad_include_formats[]" value="FB/IG Image Ad"/>&nbsp;<span>FB/IG Image Ad</span></br>
            <input type="checkbox" name="social_ad_include_formats[]" value="FB/IG Carousel Ad"/>&nbsp;<span>FB/IG Carousel Ad</span></br>
            <input type="checkbox" name="social_ad_include_formats[]" value="TT Video Ad"/>&nbsp;<span>TT Video Ad</span></br>
            <input type="checkbox" name="social_ad_include_formats[]" value="SC Video Ad"/>&nbsp;<span>SC Video Ad</span></br>
            <input type="checkbox" name="social_ad_include_formats[]" value="Other Ad"/>&nbsp;<span>Other Ad</span></br>
        </div>
        <div class="traffic" hidden style="columns: 2; -webkit-columns: 2; -moz-columns: 2;">
            <input type="checkbox" name="social_ad_include_formats[]" value="FB/IG Video Trfc Ad"/>&nbsp;<span>FB/IG Video Trfc Ad</span></br>
            <input type="checkbox" name="social_ad_include_formats[]" value="FB/IG GIF Trfc Ad"/>&nbsp;<span>FB/IG GIF Trfc Ad</span></br>
            <input type="checkbox" name="social_ad_include_formats[]" value="FB/IG Image Trfc Ad"/>&nbsp;<span>FB/IG Image Trfc Ad</span></br>
            <input type="checkbox" name="social_ad_include_formats[]" value="FB/IG Carousel Trfc Ad"/>&nbsp;<span>FB/IG Carousel Trfc Ad</span></br>
            <input type="checkbox" name="social_ad_include_formats[]" value="TT Video Trfc Ad"/>&nbsp;<span>TT Video Trfc Ad</span></br>
            <input type="checkbox" name="social_ad_include_formats[]" value="SC Video Trfc Ad"/>&nbsp;<span>SC Video Trfc Ad</span></br>
            <input type="checkbox" name="social_ad_include_formats[]" value="Other Trfc Ad"/>&nbsp;<span>Other Trfc Ad</span></br>
        </div>
        <div class="conversion" hidden style="columns: 2; -webkit-columns: 2; -moz-columns: 2;">
            <input type="checkbox" name="social_ad_include_formats[]" value="FB/IG Video Conv Ad"/>&nbsp;<span>FB/IG Video Conv Ad</span></br>
            <input type="checkbox" name="social_ad_include_formats[]" value="FB/IG Stories Conv Ad"/>&nbsp;<span>FB/IG Stories Conv Ad</span></br>
            <input type="checkbox" name="social_ad_include_formats[]" value="FB/IG GIF Conv Ad"/>&nbsp;<span>FB/IG GIF Conv Ad</span></br>
            <input type="checkbox" name="social_ad_include_formats[]" value="FB/IG Image Conv Ad"/>&nbsp;<span>FB/IG Image Conv Ad</span></br>
            <input type="checkbox" name="social_ad_include_formats[]" value="FB/IG Carousel Conv Ad"/>&nbsp;<span>FB/IG Carousel Conv Ad</span></br>
            <input type="checkbox" name="social_ad_include_formats[]" value="FB/IG Catalog Conv Ad"/>&nbsp;<span>FB/IG Catalog Conv Ad</span></br>
            <input type="checkbox" name="social_ad_include_formats[]" value="TT Video Conv Ad"/>&nbsp;<span>TT Video Conv Ad</span></br>
            <input type="checkbox" name="social_ad_include_formats[]" value="SC Video Conv Ad"/>&nbsp;<span>SC Video Conv Ad</span></br>
            <input type="checkbox" name="social_ad_include_formats[]" value="Other Conv Ad"/>&nbsp;<span>Other Conv Ad</span></br>
        </div>
        <div class="organic" hidden style="columns: 2; -webkit-columns: 2; -moz-columns: 2;">
            <input type="checkbox" name="social_ad_include_formats[]" value="FB/IG Video Organ Post"/>&nbsp;<span>FB/IG Video Organ Post</span></br>
            <input type="checkbox" name="social_ad_include_formats[]" value="FB/IG Stories Organ Post"/>&nbsp;<span>FB/IG Stories Organ Post</span></br>
            <input type="checkbox" name="social_ad_include_formats[]" value="IG Reels Organ Post"/>&nbsp;<span>IG Reels Organ Post</span></br>
            <input type="checkbox" name="social_ad_include_formats[]" value="FB/IG GIF Organ Post"/>&nbsp;<span>FB/IG GIF Organ Post</span></br>
            <input type="checkbox" name="social_ad_include_formats[]" value="FB/IG Image Organ Post"/>&nbsp;<span>FB/IG Image Organ Post</span></br>
            <input type="checkbox" name="social_ad_include_formats[]" value="FB/IG Organ Grid"/>&nbsp;<span>FB/IG Organ Grid</span></br>
            <input type="checkbox" name="social_ad_include_formats[]" value="TT Video Organ Post"/>&nbsp;<span>TT Video Organ Post</span></br>
            <input type="checkbox" name="social_ad_include_formats[]" value="SC Video Organ Post"/>&nbsp;<span>SC Video Organ Post</span></br>
            <input type="checkbox" name="social_ad_include_formats[]" value="Other Organ Post"/>&nbsp;<span>Other Organ Post</span></br>
            <input type="checkbox" name="social_ad_include_formats[]" value="FB Cover Image"/>&nbsp;<span>FB Cover Image</span></br>
            <input type="checkbox" name="social_ad_include_formats[]" value="YT Cover Image"/>&nbsp;<span>YT Cover Image</span></br>
            <input type="checkbox" name="social_ad_include_formats[]" value="TW Cover Image"/>&nbsp;<span>TW Cover Image</span></br>
            <input type="checkbox" name="social_ad_include_formats[]" value="PIN Cover Image"/>&nbsp;<span>PIN Cover Image</span></br>
        </div>
        <div class="google" hidden style="columns: 2; -webkit-columns: 2; -moz-columns: 2;">
            <input type="checkbox" name="social_ad_include_formats[]" value="Video 16 x 9"/>&nbsp;<span>Video 16 x 9</span></br>
            <input type="checkbox" name="social_ad_include_formats[]" value="Still Image 1 x 1"/>&nbsp;<span>Still Image 1 x 1</span></br>
            <input type="checkbox" name="social_ad_include_formats[]" value="Still image 4 x 5"/>&nbsp;<span>Still image 4 x 5</span></br>
            <input type="checkbox" name="social_ad_include_formats[]" value="Still image 1 x 1.91"/>&nbsp;<span>Still image 1 x 1.91</span></br>
            <input type="checkbox" name="social_ad_include_formats[]" value="Logo"/>&nbsp;<span>Logo</span></br>
        </div>
    </div>

    <div class="form-group">
        <label>Upload Visual References: <b style="color: #b91d19">(20MB Max)</b></label>
        <input type="file" data-asset="default" name="{{ $asset_type }}_c_attachment[]" class="form-control c_attachment last_upload" multiple="multiple"/>
        <a href="javascript:void(0);" onclick="another_upload($(this))" class="another_upload">[ Upload Another ]</a>
    </div>

    <div class="form-group">
        <label>Google Drive Link:</label>
        <div class="input-group" title="">
            <input type="text" name="{{ $asset_type }}_google_drive_link" class="form-control" placeholder="https://www.example.com" value=""/>
        </div>
    </div>

    <div class="form-group">
        <label>Notes:</label>
        <textarea class="form-control" id="{{ $asset_type }}_note" name="{{ $asset_type }}_note" rows="5" cols="100" style="height:100px;"></textarea>
    </div>

    <div class="form-group">
        <label>Products Featured:</label>
        <textarea class="form-control" name="{{ $asset_type }}_products_featured" rows="5" cols="100" style="height:100px;"></textarea>
    </div>

    <div class="form-group">
        <hr>
        <label style="display: inline-flex; align-items: center;">
            <input type="checkbox" name="{{ $asset_type }}_no_copy_necessary" value="on" class="custom-switch-input" id="social_ad_no_copy" onchange="skip_creative_2($(this))">
            <span class="custom-switch-indicator"></span>
            <span class="custom-switch-description">No Copy Necessary</span>
        </label>
    </div>

    <div class="form-group">
        <label>Meta Copy Inside Graphic / Google Copy: </label>
        <textarea class="form-control" name="{{ $asset_type }}_copy_inside_graphic" rows="5" cols="100"></textarea>
        <input type="checkbox" onchange="copy_requested_toggle($(this))"/>
        <label style="color: #98a6ad">Request Copy</label>
    </div>

    <div style="background-color: #efefef; border-radius: 15px;">
        <i id="arrow-one" class="dropdown fa fa-angle-up" style="margin-left: 20px;">
            <label style="font-family: Helvetica; color: #979797;">Ver 1.</label>
        </i>
        <div class="social-ad-ver-one" style="margin: 20px;">
            <div class="form-group">
                <label data-toggle="tooltip" data-placement="top" data-title="Copy that appears above image or video">Primary Text: <b>(125 characters)</b></label>
                <input type="text" name="{{ $asset_type }}_text" class="form-control" value="">
                <input type="checkbox" onchange="copy_requested_toggle($(this))"/>
                <label style="color: #98a6ad">Request Copy</label>
            </div>

            <div class="form-group">
                <label data-toggle="tooltip" data-placement="top" data-title="Call to action under image or video">Headline: <b>(27 characters / Max 40 characters)</b></label>
                <textarea class="form-control" name="{{ $asset_type }}_headline" rows="5" cols="100"></textarea>
                <input type="checkbox" onchange="copy_requested_toggle($(this))"/>
                <label style="color: #98a6ad">Request Copy</label>
            </div>

            <div class="form-group">
                <label data-toggle="tooltip" data-placement="top" data-title="Sub text below headline that provides a short summary of offering/what might see/expect when click thru">Description: <b>(27 characters)</b></label>
                <input type="text" name="{{ $asset_type }}_newsfeed" class="form-control" value="">
                <input type="checkbox" onchange="copy_requested_toggle($(this))"/>
                <label style="color: #98a6ad">Request Copy</label>
            </div>
        </div>
    </div>

    <div style="background-color: #efefef; border-radius: 15px;">
        <i id="arrow-two" class="dropdown fa fa-angle-down" style="margin-left: 20px;" onclick="click_arrow_social_ad(this, 2)" >
            <label style="font-family: Helvetica;">Ver 2.</label>
        </i>
        <div id="social-ad-ver-2" style="margin: 20px; display: none;">
            <div class="form-group">
                <label data-toggle="tooltip" data-placement="top" data-title="Copy that appears above image or video">Primary Text: <b>(125 characters)</b></label>
                <input type="text" name="{{ $asset_type }}_text_2" class="form-control" value="">
                <input type="checkbox" onchange="copy_requested_toggle($(this))"/>
                <label style="color: #98a6ad">Request Copy</label>
            </div>

            <div class="form-group">
                <label data-toggle="tooltip" data-placement="top" data-title="Call to action under image or video">Headline: <b>(27 characters / max 40 characters)</b></label>
                <textarea class="form-control" name="{{ $asset_type }}_headline_2" rows="5" cols="100"></textarea>
                <input type="checkbox" onchange="copy_requested_toggle($(this))"/>
                <label style="color: #98a6ad">Request Copy</label>
            </div>

            <div class="form-group">
                <label data-toggle="tooltip" data-placement="top" data-title="Sub text below headline that provides a short summary of offering/what might see/expect when click thru">Description: <b>(27 characters)</b></label>
                <input type="text" name="{{ $asset_type }}_newsfeed_2" class="form-control" value="">
                <input type="checkbox" onchange="copy_requested_toggle($(this))"/>
                <label style="color: #98a6ad">Request Copy</label>
            </div>
        </div>
    </div>

    <div style="background-color: #efefef; border-radius: 15px;">
        <i id="arrow-three" class="dropdown fa fa-angle-down" style="margin-left: 20px;" onclick="click_arrow_social_ad(this, 3)">
            <label style="font-family: Helvetica;">Ver 3.</label>
        </i>
        <div id="social-ad-ver-3" style="margin: 20px; display: none;">
            <div class="form-group">
                <label data-toggle="tooltip" data-placement="top" data-title="Copy that appears above image or video">Primary Text: <b>(125 characters)</b></label>
                <input type="text" name="{{ $asset_type }}_text_3" class="form-control" value="">
                <input type="checkbox" onchange="copy_requested_toggle($(this))"/>
                <label style="color: #98a6ad">Request Copy</label>
            </div>

            <div class="form-group">
                <label data-toggle="tooltip" data-placement="top" data-title="Call to action under image or video">Headline: <b>(27 characters / max 40 characters)</b></label>
                <textarea class="form-control" name="{{ $asset_type }}_headline_3" rows="5" cols="100"></textarea>
                <input type="checkbox" onchange="copy_requested_toggle($(this))"/>
                <label style="color: #98a6ad">Request Copy</label>
            </div>

            <div class="form-group">
                <label data-toggle="tooltip" data-placement="top" data-title="Sub text below headline that provides a short summary of offering/what might see/expect when click thru">Description: <b>(27 characters)</b></label>
                <input type="text" name="{{ $asset_type }}_newsfeed_3" class="form-control" value="">
                <input type="checkbox" onchange="copy_requested_toggle($(this))"/>
                <label style="color: #98a6ad">Request Copy</label>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label>Destination URL:</label>
        <div class="input-group" title="">
            <input type="text" name="{{ $asset_type }}_click_through_links" class="form-control" placeholder="https://www.example.com" value=""/>
        </div>
    </div>

    <div class="form-group">
        <label>UTM Code:</label>
        <input type="text" name="{{ $asset_type }}_utm_code" class="form-control" value="">
    </div>

    <div class="form-group">
        <label>Promo Code:</label>
        <input type="text" name="{{ $asset_type }}_promo_code" class="form-control" value="">
    </div>

    <div class="form-group">
        <label>Budget Code(s):</label>
        <input type="text" name="{{ $asset_type }}_budget_code" class="form-control" value="">
    </div>

    <div class="form-group">
        <input type="submit" name="submit" value="Create Asset" style="margin-top:10px;" class="btn btn-primary submit"/>
    </div>

</form>

<script type="text/javascript">
    // Lead time +28 days - Social Ads (exclude weekend)
    $(function() {
        var count = 28;
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
        $('input[name="<?php echo $asset_type; ?>_date_from"]').daterangepicker({
            singleDatePicker: true,
            minDate: today,
            locale: {
                format: 'YYYY-MM-DD'
            },
            isInvalidDate: function(date) {
                return (date.day() == 0 || date.day() == 6);
            },
        });
        $('input[name="<?php echo $asset_type; ?>_date_from"]').val('');
    });

    function select_category(e){
        $(".awareness").attr("hidden",true);
        $(".traffic").attr("hidden",true);
        $(".conversion").attr("hidden",true);
        $(".organic").attr("hidden",true);
        $(".google").attr("hidden",true);

        $(".skip-creative").attr("hidden",true);

        let category = $(e).val();
        if(category == 'Social Ad Awareness') {
            $('.awareness').removeAttr('hidden');
        }else if(category == 'Social Ad Traffic'){
            $('.traffic').removeAttr('hidden');
        }else if(category == 'Social Ad Conversion'){
            $('.conversion').removeAttr('hidden');
        }else if(category == 'Social Ad Organic'){
            $('.organic').removeAttr('hidden');
        }else if(category == 'Google Ad'){
            $('.google').removeAttr('hidden');
            $('.skip-creative').removeAttr('hidden');
        }

    }

    function skip_creative(e){
        if ($(e).is(':checked')) {
            $("#social_ad_no_copy").attr("disabled", true);
            $("#social_ad_no_copy").prop("checked", false);
        }else{
            $("#social_ad_no_copy").attr("disabled", false);
            $("#social_ad_no_copy").prop("checked", false);
        }
    }

    function skip_creative_2(e){
        if ($(e).is(':checked')) {
            $("#social_ad_no_copy").prop("checked", true);
        }else{
            $("#social_ad_no_copy").prop("checked", false);
        }
    }

    function click_arrow_social_ad(el,num){

        if($(el).hasClass('fa-angle-up')){
            $(el).toggleClass('with-border');
            $(el).removeClass('fa-angle-up');
            $(el).addClass('fa-angle-down');
            $('#social-ad-ver-'+num).slideUp();
        }else{
            $(el).removeClass('fa-angle-down');
            $(el).addClass('fa-angle-up');
            $('#social-ad-ver-'+num).slideDown();
        }
    }

</script>
