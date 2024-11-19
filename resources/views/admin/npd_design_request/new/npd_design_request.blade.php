<?php $task_type = 'npd_design_request'; ?>

<form method="POST" action="{{ route('npd_design_request.add_npd_design_request') }}" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="{{ $task_type }}_t_id" value="{{ $task_id }}" />
    <input type="hidden" name="{{ $task_type }}_request_type" value="{{ $task_type }}" />
    <input type="hidden" name="{{ $task_type }}_author_id" value="{{ Auth::user()->id }}" />

    <div class="sub_request">
        <p style="color: #b91d19; text-align: center; font-weight: bold;" id="selected_request_type">-</p>

        <?php if($project->team == 'Kiss Nail (ND)' || $project->team == 'Ivy Nail (ND)') {
            $lead_days = 60;
                }else{
            $lead_days = 25;
            }
        ?>
        <div class="form-group">
            <label>Request Type: <b style="color: #b91d19">*</b></label>
            <select class="form-control" name="request_type" id="lead_days" onchange="select_request_type($(this))" required>
                <option value="">Select</option>
                <optgroup label="Division / Brand Design">
                    <option value="New Packages" id="{{$lead_days}}">New Packages</option>
                    <option value="Brand Guide Lines" id="10">Brand Guide Lines</option>
                    <option value="Displays" id="15">Displays</option>
                    <option value="Mailer Box / Insert Cards" id="10">Mailer Box / Insert Cards</option>
                    <option value="Brand Events: MKT Materials" id="10">Brand Events: MKT Materials</option>
                    <option value="AD" id="10">AD</option>
                    <option value="Sign/Light box/Wall graphics" id="10">Sign/Light box/Wall graphics</option>
                    <option value="Brochure" id="15">Brochure</option>
                    <option value="Graphic Bullnose / POP / POG" id="15">Graphic Bullnose / POP / POG</option>
                    <option value="Presentation Board" id="10">Presentation Board</option>
                    <option value="Sales Sheet" id="10">Sales Sheet</option>
                    <option value="New Instructions" id="10">New Instructions</option>
                </optgroup>
                <optgroup label="Production Design">
                    <option value="New Packages - Back" id="15">New Packages - Back</option>
                    <option value="Packages Extension" id="10">Packages Extension</option>
                    <option value="Package Update (Existing Design)" id="10">Package Update (Existing Design)</option>
                    <option value="Intl. Packages: C,CA, G, Y14" id="10">Intl. Packages: C,CA, G, Y14</option>
                    <option value="Generic Bullnose / POP / POG" id="15">Generic Bullnose / POP / POG</option>
                    <option value="Products cards" id="15">Products cards</option>
                    <option value="Silkscreen / Hot Stamping" id="10">Silkscreen / Hot Stamping</option>
                    <option value="Labels" id="10">Labels</option>
                    <option value="Instruction Sheet" id="10">Instruction Sheet</option>
                    <option value="IRI / CIRCANA" id="10">IRI / CIRCANA</option>
                    <option value="Mock up Materials" id="10">Mock up Materials</option>
                    <option value="Generic Materials / Logo Signs" id="10">Generic Materials / Logo Signs</option>
                    <option value="Generic Nail / Lash boards" id="10">Generic Nail / Lash boards</option>
                    <option value="Catalog" id="30">Catalog</option>
                    <option value="Banners" id="10">Banners</option>
                    <option value="Monthly Promotion" id="10">Monthly Promotion</option>
                    <option value="Inner Box" id="10">Inner Box</option>
                </optgroup>
                <optgroup label="Industrial Design">
                    <option value="3D Nail Mold Development" id="20">3D Nail Mold Development</option>
                    <option value="3D Product Design Development" id="40">3D Product Design Development</option>
                    <option value="3D Trade Show Booth Design" id="60">3D Trade Show Booth Design</option>
                    <option value="3D Display Design" id="15">3D Display Design</option>
                    <option value="3D Motion Graphic" id="10">3D Motion Graphic</option>
                    <option value="3D Package Design" id="15">3D Package Design</option>
                    <option value="3D Collateral (Rendering & Animation)" id="5">3D Collateral (Rendering & Animation)</option>
                    <option value="Other 3D Design" id="5">Other 3D Design</option>
                </optgroup>
            </select>
        </div>

        <div class="form-group">
            <label>Objective: <b style="color: #b91d19">*</b></label>
            <textarea class="form-control" name="objective" style="height:100px;" required></textarea>
        </div>

        <div class="form-group">
            <label>Priority:</label>
            <select class="form-control" name="priority" onchange="urgent_check($(this))">
                <?php foreach($priorities as $val): ?>
                <option value="{{ $val }}">{{ $val }}</option>
                <?php endforeach ?>
            </select>
        </div>

        <div class="form-group urgent_due_date" hidden>
            <label>Due Date (Urgent): <b style="color: #b91d19">(Select from the calendar)</b></label>
            <input type="text" name="due_date_urgent" id="new_due_date_urgent"
                   class="form-control @error('due_date_urgent') is-invalid @enderror @if (!$errors->has('due_date_urgent') && old('due_date_urgent')) is-valid @endif"
                   value="{{ old('due_date_urgent', null) }}">
        </div>

        <div class="form-group urgent_due_date" hidden>
            <label>Urgent Reason:</label>
            <textarea class="form-control" name="urgent_reason" style="height:100px;"></textarea>
        </div>

        <div class="form-group">
            <label>Due Date: <b style="color: #b91d19">* Min. </b><b style="color: #b91d19" id="request_lead_days"></b><b style="color: #b91d19"> Days</b></label>
            <input type="text" name="due_date" id="new_due_date" class="form-control" value="" required>
        </div>

        <div class="form-group">
            <label>Scope: <b style="color: #b91d19">*</b></label>
            <select class="form-control" name="scope" required>
                <option value="">Select</option>
                <?php foreach($scope_list as $val): ?>
                <option value="{{ $val }}">{{ $val }}</option>
                <?php endforeach ?>
            </select>
        </div>

        <div class="form-group">
            <label>Artwork Type: <b style="color: #b91d19">*</b></label>
            <select class="form-control" name="artwork_type" required>
                <option value="">Select</option>
                <?php foreach($artwork_type_list as $val): ?>
                <option value="{{ $val }}">{{ $val }}</option>
                <?php endforeach ?>
            </select>
        </div>

{{--        <div class="form-group">--}}
{{--            <label>Sales Channel:</label>--}}
{{--            <select class="form-control" name="sales_channel" required>--}}
{{--                <option value="">Select</option>--}}
{{--                <?php foreach($sales_channel_list as $val): ?>--}}
{{--                <option value="{{ $val }}">{{ $val }}</option>--}}
{{--                <?php endforeach ?>--}}
{{--            </select>--}}
{{--        </div>--}}

        <div class="form-group">
            <label>Sales Channel:</label>
            <div class="columns" style="column-count: 2;">
                <?php foreach($sales_channel_list as $value): ?>
                <div class="col-md">
                    <div class="form-check" style="padding-left: 0px;">
                        <input  type="checkbox"
                                name="sales_channel[]"
                                value="{{ $value }}" id="new_{{$value}}"
                        >
                        <label class="form-check-label " for="new_{{ $value }}">
                            {{ $value }}
                        </label>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="form-group">
            <label>If others, specify:</label>
            <textarea class="form-control" name="if_others_sales_channel" style="height:50px;"></textarea>
        </div>

        <div class="form-group">
            <label>Target Audience: </label>
            <textarea class="form-control" name="target_audience" style="height:100px;"></textarea>
        </div>

        <div class="form-group">
            <label>Head Copy: </label>
            <textarea class="form-control" name="head_copy" style="height:100px;"></textarea>
        </div>

        <div class="form-group">
            <label>References:</label>
            {!! Form::textarea('reference', null, ['class' => 'form-control summernote']) !!}
        </div>

        <div class="form-group">
            <label>Material Number: <b style="color: #b91d19">*</b></label>
            <input type="text" name="material_number" class="form-control" value="" required>
        </div>

        <div class="form-group">
            <label>Component Number: <b style="color: #b91d19">*</b></label>
            <input type="text" name="component_number" class="form-control" value="" required>
        </div>

        <div class="form-group">
            <label>Attachment: <b style="color: #b91d19">(20MB Max)</b></label>
            <input type="file" data-asset="default" name="{{ $task_type }}_attachment[]" class="form-control p_attachment last_upload" multiple="multiple"/>
            <a href="javascript:void(0);" onclick="another_upload($(this))" class="another_upload">[ Upload Another ]</a>
        </div>
    </div>

    <div class="form-group">
        <input type="submit" name="submit" value="Create" style="margin-top:10px; padding: .55rem 1.5rem; font-size: medium;" class="btn btn-create submit"/>
    </div>

</form>

<script type="text/javascript">
    $(function() {
        var today_urgent = new Date();
        $('input[id="new_due_date_urgent"]').daterangepicker({
            singleDatePicker: true,
            minDate: today_urgent,
            locale: {
                format: 'YYYY-MM-DD'
            },
            isInvalidDate: function(date) {
                return (date.day() == 0 || date.day() == 6);
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

    function select_request_type(e){
        let request_type = $(e).val();
        $("#new_sub_request").fadeIn(600);
        $("#selected_request_type").text(request_type);

        let selectBox = document.getElementById("lead_days");
        let selectedOption = selectBox.options[selectBox.selectedIndex];
        let selectedOptionId = selectedOption.id;

        var today = new Date();
        var count = selectedOptionId;

        for (let i = 1; i <= count; i++) {
            today.setDate(today.getDate() + 1);
            if (today.getDay() === 6) {
                today.setDate(today.getDate() + 2);
            }
            else if (today.getDay() === 0) {
                today.setDate(today.getDate() + 1);
            }
        }

        if(request_type == 'New Packages' || request_type == 'New Packages - Back'){
            $('input[id="new_due_date"]').daterangepicker({
                singleDatePicker: true,
                minDate: today,
                maxDate: today,
                locale: {
                    format: 'YYYY-MM-DD'
                },
                isInvalidDate: function (date) {
                    return (date.day() == 0 || date.day() == 6);
                },
            });
        }else {
            $('input[id="new_due_date"]').daterangepicker({
                singleDatePicker: true,
                minDate: today,
                locale: {
                    format: 'YYYY-MM-DD'
                },
                isInvalidDate: function (date) {
                    return (date.day() == 0 || date.day() == 6);
                },
            });
        }

        $("#request_lead_days").text(count);

    }
</script>


