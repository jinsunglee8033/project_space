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
                        <form method="POST" action="{{ route('form.store_qr_code') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">
                                <div class="col">
                                    <div class="form-group">
                                        <label>Your Name <span class="text-danger">*</span></label>
                                        <input type="text" name="name"
                                               class="form-control @error('name') is-invalid @enderror @if (!$errors->has('name') && old('name')) is-valid @endif"
                                               value="{{ old('name') }}"
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
                                               value="{{ old('email') }}">
                                        @error('email')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label>QR Code For <span class="text-danger">*</span></label>
                                        <textarea class="form-control @error('qr_code_for') is-invalid @enderror @if (!$errors->has('qr_code_for') && old('qr_code_for')) is-valid @endif"
                                                  id="qr_code_for" name="qr_code_for" rows="5" cols="100" style="height:100px;">{{ old('qr_code_for') }}</textarea>
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
                                                <option value="{{$value}}" {{ $value == old('brand') ? 'selected' : '' }}>
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
                                            @foreach ($departments as $key => $value)
                                                <option value="{{$value}}" {{ $value == old('department') ? 'selected' : '' }}>
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
                                               value="{{ old('link_to') }}">
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
                                               value="{{ old('date_1') }}">
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
                                               value="{{ old('date_2') }}">
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
                                               value="{{ old('date_3') }}">
                                        @error('date_3')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label>Any specific requirements or information?</label>
                                        <textarea class="form-control" id="information" name="information" rows="5" cols="100" style="height:100px;">{{ old('information') }}</textarea>
                                    </div>

                                    <div class="card-footer text-right">
                                        <button class="btn btn-primary">Create</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>

    </section>
@endsection
