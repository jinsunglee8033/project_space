<?php $task_type = 'product_brief'; ?>

<form method="POST" action="{{ route('project.add_product_brief') }}" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="{{ $task_type }}_p_id" value="{{ $project->id }}" />
    <input type="hidden" name="{{ $task_type }}_task_type" value="{{ $task_type }}" />
    <input type="hidden" name="{{ $task_type }}_author_id" value="{{ Auth::user()->id }}" />

    <div class="form-group">
        <label>Product Name:</label>
        <input type="text" name="{{ $task_type }}_product_name" class="form-control" value="" required>
    </div>

    <div class="form-group">
        <label>Material Number:</label>
        <input type="text" name="{{ $task_type }}_material_number" class="form-control" value="">
    </div>

    <div class="form-group">
        <label>Total SKU Count:</label>
        <input type="text" name="{{ $task_type }}_total_sku_count" class="form-control" value="">
    </div>

    <div class="form-group">
        <label>Target Receiving Date:</label>
        <input required type="text" name="{{ $task_type }}_target_receiving_date" id="new_target_receiving_date" required autocomplete="off"
               class="form-control @error($task_type.'_target_receiving_date') is-invalid @enderror @if (!$errors->has($task_type.'_target_receiving_date') && old($task_type.'_target_receiving_date')) is-valid @endif"
               value="{{ old($task_type . '_target_receiving_date', null) }}">
    </div>

    <div class="form-group">
        <label>Door #:</label>
        <input type="text" name="{{ $task_type }}_door" class="form-control" value="">
    </div>

    <div class="form-group">
        <label>NSP ($):</label>
        <input type="text" name="{{ $task_type }}_nsp" class="form-control" value="">
    </div>

    <div class="form-group">
        <label>SRP ($):</label>
        <input type="text" name="{{ $task_type }}_srp" class="form-control" value="">
    </div>

    <div class="form-group">
        <label>Category:</label>
        <select id="{{ $task_type }}_category" class="form-control @error($task_type.'_category') is-invalid @enderror @if (!$errors->has($task_type.'_category') && old($task_type.'_category')) is-valid @endif"
                name="{{ $task_type }}_category" required>
            <option value="">Select</option>
            @if(empty(old($task_type.'_category')))
                @foreach ($categories as $value)
                    <option value="{{ $value }}">
                        {{ $value }}
                    </option>
                @endforeach
            @else
                @foreach ($categories as $value)
                    <option value="{{ $value }}" {{ $value == old($task_type.'_category') ? 'selected' : '' }}>
                        {{ $value }}
                    </option>
                @endforeach
            @endif
        </select>
    </div>

    <div class="form-group">
        <label>Sub-Category:</label>
        @if(empty(old('sub_category')))
            <select id="{{ $task_type }}_sub_category" name="{{ $task_type }}_sub_category" required
                    class="form-control @error($task_type.'_sub_category') is-invalid @enderror @if (!$errors->has($task_type.'_sub_category') && old($task_type.'_sub_category')) is-valid @endif" >
            </select>
        @else
            <select id="{{ $task_type }}_sub_category" name="{{ $task_type }}_sub_category" required
                    class="form-control @error($task_type.'_sub_category') is-invalid @enderror @if (!$errors->has($task_type.'_sub_category') && old($task_type.'_sub_category')) is-valid @endif" >
                <option value="{{ old($task_type.'_sub_category') }}" selected>
                    {{ old($task_type.'_sub_category') }}
                </option>
            </select>
        @endif
    </div>

    <div class="form-group">
        <label>Franchise:</label>
        <select class="form-control" name="{{ $task_type }}_franchise">
            <option value="">Select</option>
            @foreach ($franchises as $value)
                <option value="{{ $value }}" {{ $value == old('franchise') ? 'selected' : '' }}>
                    {{ $value }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label>Shade Names:</label>
        <input type="text" name="{{ $task_type }}_shade_names" class="form-control" value="">
    </div>

    <div class="form-group">
        <label>Claim Weight:</label>
        <input type="text" name="{{ $task_type }}_claim_weight" class="form-control" value="">
    </div>

    <div class="form-group">
        <label>Testing Claims:</label>
        {!! Form::textarea($task_type.'_testing_claims', null, ['class' => 'form-control summernote']) !!}
    </div>

    <div class="form-group">
        <label>Concept & Key Benefits:</label>
        {!! Form::textarea($task_type.'_concept', null, ['class' => 'form-control summernote']) !!}
    </div>

    <div class="form-group">
        <label>Key or Concept Ingredients:</label>
        <textarea class="form-control" name="{{ $task_type }}_key" style="height:100px;"></textarea>
    </div>

    <div class="form-group">
        <label>Product Format: </label>
        <div class="columns" style="column-count: 4;">
        <?php foreach($product_formats as $value): ?>
        <div class="col-md">
            <div class="form-check" style="padding-left: 0px;">
                <input  type="checkbox"
                        name="{{ $task_type }}_product_format[]"
                        value="{{ $value }}"
                >
                <label class="form-check-label " for="{{ $value }}">
                    {{ $value }}
                </label>
            </div>
        </div>
        <?php endforeach; ?>
        </div>
    </div>

    <div class="form-group">
        <label>Texture:</label>
        <div class="columns" style="column-count: 4;">
            <?php foreach($textures as $value): ?>
            <div class="col-md">
                <div class="form-check" style="padding-left: 0px;">
                    <input  type="checkbox"
                            name="{{ $task_type }}_texture[]"
                            value="{{ $value }}"
                    >
                    <label class="form-check-label " for="{{ $value }}">
                        {{ $value }}
                    </label>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="form-group">
        <label>Finish:</label>
        <div class="columns" style="column-count: 4;">
            <?php foreach($finishes as $value): ?>
            <div class="col-md">
                <div class="form-check" style="padding-left: 0px;">
                    <input  type="checkbox"
                            name="{{ $task_type }}_finish[]"
                            value="{{ $value }}"
                    >
                    <label class="form-check-label " for="{{ $value }}">
                        {{ $value }}
                    </label>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="form-group">
        <label>Coverage:</label>
        <select class="form-control" name="{{ $task_type }}_coverage">
            <option value="">Select</option>
            @foreach ($coverages as $value)
                <option value="{{ $value }}" {{ $value == old('coverage') ? 'selected' : '' }}>
                    {{ $value }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label data-toggle="modal"
               data-target="#Modal_ban_list_new">Must Ban Ingredients: <b style="color: #b91d19">(Click for checking ban list)</b></label>
        <textarea class="form-control" name="{{ $task_type }}_must_ban" style="height:100px;"></textarea>
    </div>

    <div class="form-group">
        <label>Highlights:</label>
        <div class="columns" style="column-count: 2;">
            <?php foreach($highlightss as $value): ?>
            <div class="col-md">
                <div class="form-check" style="padding-left: 0px;">
                    <input  type="checkbox"
                            name="{{ $task_type }}_highlights[]"
                            value="{{ $value }}"
                    >
                    <label class="form-check-label " for="{{ $value }}">
                        {{ $value }}
                    </label>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="form-group">
        <label>Attachments: <b style="color: #b91d19">(20MB Max)</b></label>
        <input type="file" data-asset="default" name="{{ $task_type }}_p_attachment[]" class="form-control p_attachment last_upload" multiple="multiple"/>
        <a href="javascript:void(0);" onclick="another_upload($(this))" class="another_upload">[ Upload Another ]</a>
    </div>

    <div class="form-group">
        <input type="submit" name="submit" value="Create" style="margin-top:10px; padding: .55rem 1.5rem; font-size: medium;" class="btn btn-create submit"/>
    </div>

</form>

<div class="modal fade"
     id="Modal_ban_list_new"
     tabindex="-1"
     data-backdrop="false"
     role="dialog"
     aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog"
         role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button"
                        class="close"
                        data-dismiss="modal"
                        aria-label="Close">
                                    <span aria-hidden="true">
                                      ×
                                  </span>
                </button>
            </div>
            <!--Modal body with image-->
            <div class="modal-body">
                <img class="img-fluid" src="<?php echo '/storage/JOAH_Banned_List.jpg'; ?>">
            </div>
            <div class="modal-footer">
                <button type="button"
                        class="btn btn-danger"
                        data-dismiss="modal">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(function() {
        var today = new Date();
        $('input[name="<?php echo $task_type; ?>_target_receiving_date"]').daterangepicker({
            singleDatePicker: true,
            minDate: today,
            locale: {
                format: 'YYYY-MM-DD'
            },
        });
        $('input[name="<?php echo $task_type; ?>_target_receiving_date"]').val('');
    });

    // Dynamic Brand drop box. Manually for now.
    const categorySelect = document.getElementById('<?php echo $task_type; ?>_category');
    const subCategorySelect = document.getElementById('<?php echo $task_type; ?>_sub_category');

    categorySelect.addEventListener('change', function() {
        const selectedOption = categorySelect.value;
        subCategorySelect.innerHTML = '';
        if (selectedOption === 'Select') {
            const option1 = document.createElement('option');
            option1.text = '';
            option1.value = '';
            subCategorySelect.add(option1);
        }else if (selectedOption === 'Face') {
            const option1 = document.createElement('option');
            option1.text = 'Foundation';
            option1.value = 'Foundation';
            const option2 = document.createElement('option');
            option2.text = 'BB & CC Cream';
            option2.value = 'BB & CC Cream';
            const option3 = document.createElement('option');
            option3.text = 'Tinted Moisturizer';
            option3.value = 'Tinted Moisturizer';
            const option4 = document.createElement('option');
            option4.text = 'Concealer';
            option4.value = 'Concealer';
            const option5 = document.createElement('option');
            option5.text = 'Face Primer';
            option5.value = 'Face Primer';
            const option6 = document.createElement('option');
            option6.text = 'Setting Spray';
            option6.value = 'Setting Spray';
            const option7 = document.createElement('option');
            option7.text = 'Setting Powder';
            option7.value = 'Setting Powder';
            const option8 = document.createElement('option');
            option8.text = 'Highlighter';
            option8.value = 'Highlighter';
            const option9 = document.createElement('option');
            option9.text = 'Color Corrector';
            option9.value = 'Color Corrector';
            const option10 = document.createElement('option');
            option10.text = 'Blush';
            option10.value = 'Blush';
            const option11 = document.createElement('option');
            option11.text = 'Bronzer/Contour';
            option11.value = 'Bronzer/Contour';
            subCategorySelect.add(option1);
            subCategorySelect.add(option2);
            subCategorySelect.add(option3);
            subCategorySelect.add(option4);
            subCategorySelect.add(option5);
            subCategorySelect.add(option6);
            subCategorySelect.add(option7);
            subCategorySelect.add(option8);
            subCategorySelect.add(option9);
            subCategorySelect.add(option10);
            subCategorySelect.add(option11);
        } else if (selectedOption === 'Eye') {
            const option1 = document.createElement('option');
            option1.text = 'Shadow';
            option1.value = 'Shadow';
            const option2 = document.createElement('option');
            option2.text = 'Mascara';
            option2.value = 'Mascara';
            const option3 = document.createElement('option');
            option3.text = 'Lash Primer/Serum';
            option3.value = 'Lash Primer/Serum';
            const option4 = document.createElement('option');
            option4.text = 'Eyeliner';
            option4.value = 'Eyeliner';
            const option5 = document.createElement('option');
            option5.text = 'Eyebrow';
            option5.value = 'Eyebrow';
            const option6 = document.createElement('option');
            option6.text = 'Eye Primer';
            option6.value = 'Eye Primer';
            subCategorySelect.add(option1);
            subCategorySelect.add(option2);
            subCategorySelect.add(option3);
            subCategorySelect.add(option4);
            subCategorySelect.add(option5);
            subCategorySelect.add(option6);
        } else if (selectedOption === 'Lip') {
            const option1 = document.createElement('option');
            option1.text = 'Lip';
            option1.value = 'Lip';
            const option2 = document.createElement('option');
            option2.text = ' Lip Gloss';
            option2.value = ' Lip Gloss';
            const option3 = document.createElement('option');
            option3.text = 'Lipstick';
            option3.value = 'Lipstick';
            const option4 = document.createElement('option');
            option4.text = 'Liquid Lipstick';
            option4.value = 'Liquid Lipstick';
            const option5 = document.createElement('option');
            option5.text = 'Lip Tint/Stain';
            option5.value = 'Lip Tint/Stain';
            const option6 = document.createElement('option');
            option6.text = 'Lip Oil';
            option6.value = 'Lip Oil';
            const option7 = document.createElement('option');
            option7.text = 'Lip Balm';
            option7.value = 'Lip Balm';
            const option8 = document.createElement('option');
            option8.text = 'Lip Plumper';
            option8.value = 'Lip Plumper';
            const option9 = document.createElement('option');
            option9.text = 'Lip Liner';
            option9.value = 'Lip Liner';
            const option10 = document.createElement('option');
            option10.text = 'Lip scrub';
            option10.value = 'Lip scrub';
            const option11 = document.createElement('option');
            option11.text = 'Lip Mask';
            option11.value = 'Lip Mask';
            const option12 = document.createElement('option');
            option12.text = 'Lip Primer';
            option12.value = 'Lip Primer';
            subCategorySelect.add(option1);
            subCategorySelect.add(option2);
            subCategorySelect.add(option3);
            subCategorySelect.add(option4);
            subCategorySelect.add(option5);
            subCategorySelect.add(option6);
            subCategorySelect.add(option7);
            subCategorySelect.add(option8);
            subCategorySelect.add(option9);
            subCategorySelect.add(option10);
            subCategorySelect.add(option11);
            subCategorySelect.add(option12);
        } else if (selectedOption === 'Acc & Tool') {
            const option1 = document.createElement('option');
            option1.text = 'Face Brush';
            option1.value = 'Face Brush';
            const option2 = document.createElement('option');
            option2.text = 'Eye Brush';
            option2.value = 'Eye Brush';
            const option3 = document.createElement('option');
            option3.text = 'Lip Brush';
            option3.value = 'Lip Brush';
            const option4 = document.createElement('option');
            option4.text = 'Sponge';
            option4.value = 'Sponge';
            const option5 = document.createElement('option');
            option5.text = 'Applicator';
            option5.value = 'Applicator';
            const option6 = document.createElement('option');
            option6.text = 'Brush Cleanser';
            option6.value = 'Brush Cleanser';
            const option7 = document.createElement('option');
            option7.text = 'Beauty Device';
            option7.value = 'Beauty Device';
            subCategorySelect.add(option1);
            subCategorySelect.add(option2);
            subCategorySelect.add(option3);
            subCategorySelect.add(option4);
            subCategorySelect.add(option5);
            subCategorySelect.add(option6);
            subCategorySelect.add(option7);
        } else if (selectedOption === 'Moisturizer') {
            const option1 = document.createElement('option');
            option1.text = 'Face Moisturizer';
            option1.value = 'Face Moisturizer';
            const option2 = document.createElement('option');
            option2.text = 'Night Cream';
            option2.value = 'Night Cream';
            const option3 = document.createElement('option');
            option3.text = 'Face Essence/Serum';
            option3.value = 'Face Essence/Serum';
            const option4 = document.createElement('option');
            option4.text = 'Face Ampoule';
            option4.value = 'Face Ampoule';
            const option5 = document.createElement('option');
            option5.text = 'Eye Cream';
            option5.value = 'Eye Cream';
            const option6 = document.createElement('option');
            option6.text = 'Face Mist';
            option6.value = 'Face Mist';
            subCategorySelect.add(option1);
            subCategorySelect.add(option2);
            subCategorySelect.add(option3);
            subCategorySelect.add(option4);
            subCategorySelect.add(option5);
            subCategorySelect.add(option6);
        } else if (selectedOption === 'Cleanser') {
            const option1 = document.createElement('option');
            option1.text = 'Face Cleanser';
            option1.value = 'Face Cleanser';
            const option2 = document.createElement('option');
            option2.text = 'Face Exfoliator/Peel';
            option2.value = 'Face Exfoliator/Peel';
            const option3 = document.createElement('option');
            option3.text = 'Makeup Removers';
            option3.value = 'Makeup Removers';
            const option4 = document.createElement('option');
            option4.text = 'Makeup Wipes';
            option4.value = 'Makeup Wipes';
            const option5 = document.createElement('option');
            option5.text = 'Toner';
            option5.value = 'Toner';
            subCategorySelect.add(option1);
            subCategorySelect.add(option2);
            subCategorySelect.add(option3);
            subCategorySelect.add(option4);
            subCategorySelect.add(option5);
        } else if (selectedOption === 'Mask') {
            const option1 = document.createElement('option');
            option1.text = 'Sheet Mask';
            option1.value = 'Sheet Mask';
            const option2 = document.createElement('option');
            option2.text = 'Face Mask';
            option2.value = 'Face Mask';
            const option3 = document.createElement('option');
            option3.text = 'Eye Mask';
            option3.value = 'Eye Mask';
            subCategorySelect.add(option1);
            subCategorySelect.add(option2);
            subCategorySelect.add(option3);
        } else if (selectedOption === 'Treatments') {
            const option1 = document.createElement('option');
            option1.text = 'Spot/Blemish Patch';
            option1.value = 'Spot/Blemish Patch';
            const option2 = document.createElement('option');
            option2.text = 'Spot/Blemish Ointment';
            option2.value = 'Spot/Blemish Ointment';
            subCategorySelect.add(option1);
            subCategorySelect.add(option2);
        } else if (selectedOption === 'Sun') {
            const option1 = document.createElement('option');
            option1.text = 'Sunscreen - Face';
            option1.value = 'Sunscreen - Face';
            const option2 = document.createElement('option');
            option2.text = 'Sunscreen - Body';
            option2.value = 'Sunscreen - Body';
            const option3 = document.createElement('option');
            option3.text = 'After Sun Care';
            option3.value = 'After Sun Care';
            subCategorySelect.add(option1);
            subCategorySelect.add(option2);
            subCategorySelect.add(option3);
        } else if (selectedOption === 'Body') {
            const option1 = document.createElement('option');
            option1.text = 'Body Scrub';
            option1.value = 'Body Scrub';
            const option2 = document.createElement('option');
            option2.text = 'Body Moisturizer';
            option2.value = 'Body Moisturizer';
            const option3 = document.createElement('option');
            option3.text = 'Body Wash';
            option3.value = 'Body Wash';
            const option4 = document.createElement('option');
            option4.text = 'Hand Cream';
            option4.value = 'Hand Cream';
            const option5 = document.createElement('option');
            option5.text = 'Neck Cream';
            option5.value = 'Neck Cream';
            const option6 = document.createElement('option');
            option6.text = 'Foot Cream';
            option6.value = 'Foot Cream';
            subCategorySelect.add(option1);
            subCategorySelect.add(option2);
            subCategorySelect.add(option3);
            subCategorySelect.add(option4);
            subCategorySelect.add(option5);
            subCategorySelect.add(option6);
        } else if (selectedOption === 'Self-Tanning') {
            const option1 = document.createElement('option');
            option1.text = 'Self-tanner';
            option1.value = 'Self-tanner';
            subCategorySelect.add(option1);
        }
    });

</script>
