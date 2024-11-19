@extends('layouts.dashboard')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Form Request</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ url('admin/create_qr_code') }}">QR Code Request</a></div>
            </div>
        </div>

        <div class="section-body">
            <h2 class="section-title">QR Code Request</h2>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h4>QR Code Request</h4>
                        </div>
                        @include('admin.shared.flash')
                        <form method="POST" action="{{ route('form.update_qr_code',$qr_code->id) }}" enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">
                                <div class="col">
                                    <div class="form-group">
                                        <label>Your Name <span class="text-danger">*</span></label>
                                        <input type="text" name="name"
                                               class="form-control @error('name') is-invalid @enderror @if (!$errors->has('name') && old('name')) is-valid @endif"
                                               value="{{ $qr_code->name }}"
                                               >
                                        @error('name')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label>Your Email <span class="text-danger">*</span></label>
                                        <input type="text" name="email"
                                               class="form-control @error('email') is-invalid @enderror @if (!$errors->has('email') && old('email')) is-valid @endif"
                                               value="{{ $qr_code->email }}">
                                        @error('email')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label>QR Code For <span class="text-danger">*</span></label>
                                        <textarea class="form-control @error('qr_code_for') is-invalid @enderror @if (!$errors->has('qr_code_for') && old('qr_code_for')) is-valid @endif"
                                                  id="qr_code_for" name="qr_code_for" rows="5" cols="100" style="height:100px;">{{ $qr_code->qr_code_for }}</textarea>
                                        @error('qr_code_for')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label>Brands <span class="text-danger">*</span></label>
                                        <select class="form-control @error('brand') is-invalid @enderror @if (!$errors->has('brand') && old('brand')) is-valid @endif"
                                                name="brand" id="brand">
                                            <option value="">Select</option>
                                            @foreach ($brands as $key => $value)
                                                <option value="{{$value}}" {{ $value == $qr_code->brand ? 'selected' : '' }}>
                                                    {{ $value }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('brand')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label>Department <span class="text-danger">*</span></label>
                                        <select class="form-control @error('department') is-invalid @enderror @if (!$errors->has('department') && old('department')) is-valid @endif"
                                                name="department" id="department">
                                            <option value="">Select</option>
                                            @foreach ($departments as $value)
                                                <option value="{{$value}}" {{ $value == $qr_code->department ? 'selected' : '' }}>
                                                    {{ $value }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('department')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label>Where do you want to link? <span class="text-danger">*</span></label>
                                        <input type="text" name="link_to"
                                               class="form-control @error('link_to') is-invalid @enderror @if (!$errors->has('link_to') && old('link_to')) is-valid @endif"
                                               value="{{ $qr_code->link_to }}">
                                        @error('link_to')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label>When do you need the QR image(s)? <span class="text-danger">*</span></label>
                                        <input type="text" name="date_1" id="date_1"
                                               class="form-control datepicker @error('date_1') is-invalid @enderror @if (!$errors->has('date_1') && old('date_1')) is-valid @endif"
                                               value="{{ $qr_code->date_1 }}">
                                        @error('date_1')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label>QR Code Live Date <span class="text-danger">*</span></label>
                                        <input type="text" name="date_2" id="date_2"
                                               class="form-control datepicker @error('date_2') is-invalid @enderror @if (!$errors->has('date_2') && old('date_2')) is-valid @endif"
                                               value="{{ $qr_code->date_2 }}">
                                        @error('date_2')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label>QR Code Expire Date</label>
                                        <input type="text" name="date_3" id="date_3"
                                               class="form-control datepicker"
                                               value="{{ $qr_code->date_3 }}">
                                        @error('date_3')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label>Any specific requirements or information?</label>
                                        <textarea class="form-control" id="information" name="information" rows="5" cols="100" style="height:100px;">{{ $qr_code->information }}</textarea>
                                    </div>

                                    <?php if (!empty($attachment[0])): ?>
                                    <label>Attachments: </label>
                                    <br/>
                                        <?php
                                        $file_ext = $attachment[0]['file_ext'];
                                        if(strpos($file_ext, ".") !== false){
                                            $file_ext = substr($file_ext, 1);
                                        }
                                        $not_image = ['pdf','doc','docx','pptx','ppt','mp4','xls','xlsx','csv','zip'];
                                        $file_icon = '/storage/'.$file_ext.'.png';
                                        $attachment_link = '/storage' . $attachment[0]['attachment'];
                                        $open_link = 'open_download';
                                        ?>
                                        <div class="attachment_wrapper">
                                            <?php $name = explode('/', $attachment[0]['attachment']); ?>
                                            <?php $name = $name[count($name)-1]; ?>
                                            <?php $date = date('m/d/Y g:ia', strtotime($attachment[0]['date_created'])); ?>
                                            <div class="attachement">{{ $name }}</div>
                                            <a onclick="remove_file($(this))"
                                               class="delete attachement close"
                                               title="Delete"
                                               data-file-name="<?php echo $name; ?>"
                                               data-attachment-id="<?php echo $attachment[0]['attachment_id']; ?>">
                                                <i class="fa fa-times"></i>
                                            </a>
                                            <img title="<?php echo $name . ' (' . date('m/d/Y g:ia', strtotime($date)) . ')'; ?>"
                                                 data-file-date="<?php echo $date; ?>"
                                                 <?php
                                                 if (!in_array($file_ext, $not_image)) {
                                                 $file_icon = $attachment_link;
                                                 $open_link = 'open_image';
                                                 ?>
                                                 data-toggle="modal"
                                                 data-target="#exampleModal_<?php echo $attachment[0]['attachment_id']; ?>"
                                                 <?php
                                                 }
                                                 ?>
                                                 onclick="<?php echo $open_link; ?>('<?php echo $attachment_link; ?>')"
                                                 src="<?php echo $file_icon; ?>"
                                                 class="thumbnail"/>
                                        </div>
                                    <?php endif; ?>

                                    <div class="form-group">
                                        <label>Upload QR Code </label>
                                        <input type="file" id="c_attachment[]" name="c_attachment[]"
                                               data-asset="default" multiple="multiple"
                                               class="form-control c_attachment last_upload @error('c_attachment') is-invalid @enderror @if (!$errors->has('c_attachment') && old('c_attachment')) is-valid @endif"
                                               value="{{ old('c_attachment') }}">
                                        @error('c_attachment')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label>URL Destination Link</label>
                                        <input type="text" name="url_destination_link"
                                               class="form-control @error('url_destination_link') is-invalid @enderror @if (!$errors->has('url_destination_link') && old('url_destination_link')) is-valid @endif"
                                               value="{{ $qr_code->url_destination_link }}">
                                        @error('url_destination_link')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label>Short URL</label>
                                        <input type="text" name="short_url"
                                               class="form-control @error('short_url') is-invalid @enderror @if (!$errors->has('short_url') && old('short_url')) is-valid @endif"
                                               value="{{ $qr_code->short_url }}">
                                        @error('short_url')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>

                                    <div class="card-footer text-right">
                                        <button class="btn btn-primary">Save</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>

    </section>

    <?php if (!empty($attachment[0])): ?>
    <div class="modal fade"
         id="exampleModal_<?php echo $attachment[0]['attachment_id']; ?>"
         tabindex="-1"
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
                <?php $name = explode('/', $attachment[0]['attachment']); ?>
                <?php $name = $name[count($name)-1]; ?>
                <div class="modal-title text-lg-center">{{ $name }}</div>
                <div class="modal-body">
                    <img class="img-fluid" src="<?php echo '/storage' . $attachment[0]['attachment']; ?>">
                </div>
                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-primary"
                            data-dismiss="modal"
                            onclick="open_download('<?php echo '/storage' . $attachment[0]['attachment']; ?>')"
                    >
                        Download
                    </button>
                    <button type="button"
                            class="btn btn-danger"
                            data-dismiss="modal">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script type="text/javascript">

        function remove_file(el) {
            if (confirm("Are you sure to Delete File?") == true) {
                let id = $(el).attr('data-attachment-id');
                $.ajax({
                    url: "<?php echo url('/admin/campaign/fileRemove'); ?>"+"/"+id,
                    type: "GET",
                    datatype: "json",
                    success: function(response) {
                        if(response == 'success'){
                            $(el).parent().remove();
                        }else{
                            alert(response);
                        }
                    },
                })
            }
        }

        function open_download(link) {
            let click_link = document.createElement('a');
            click_link.href = link;
            image_arr = link.split('/');
            link = image_arr[image_arr.length-1];
            click_link.download = link;
            document.body.appendChild(click_link);
            click_link.click();
        }
    </script>

@endsection
