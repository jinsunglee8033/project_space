@extends('layouts.dashboard')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Brand Management</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ url('admin/brands') }}">Brands</a></div>
            </div>
        </div>
        @if (empty($brand ?? '' ?? ''))
            <form method="POST" action="{{ route('brands.store') }}">
            @else
                <form method="POST" action="{{ route('brands.update', $brands->id) }}">
                    <input type="hidden" name="id" value="{{ $brand->id }}" />
                    @method('PUT')
        @endif
        @csrf
        <div class="section-body">
            <h2 class="section-title">{{ empty($brand) ? 'Add New Brand' : 'Update Brand' }}</h2>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h4>{{ empty($brand) ? __('users.add_card_title') : __('users.update_card_title') }}</h4>
                        </div>

                        <div class="card-body">
                            @include('admin.shared.flash')

                            <div class="col">
                                <div class="form-group">
                                    <label>Brand Name</label>
                                    <input type="text" name="campaign_name"
                                           class="form-control @error('first_name') is-invalid @enderror @if (!$errors->has('campaign_name') && old('campaign_name')) is-valid @endif"
                                           value="{{ old('first_name', !empty($brand) ? $brand->campaign_name : null) }}">
                                    @error('campaign_name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                            </div>

                        </div>

                        <div class="card-footer text-right">
                            <button
                                class="btn btn-primary">{{ empty($brand) ? __('general.btn_create_label') : __('general.btn_update_label') }}</button>
                        </div>
                    </div>

                </div>

            </div>
        </div>
        </form>
    </section>
@endsection
