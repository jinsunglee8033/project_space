@extends('layouts.dashboard')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Product Segment Management</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ url('admin/product_segment') }}">Product Segment</a></div>
                <div class="breadcrumb-item">Edit Product Segment</div>
            </div>
        </div>
        @if (empty($product_segment ?? '' ?? ''))
            <form method="POST" action="{{ route('product_segment.store') }}">
            @else
                <form method="POST" action="{{ route('product_segment.update', $product_segment->id) }}">
                    <input type="hidden" name="id" value="{{ $product_segment->id }}" />
                    @method('PUT')
        @endif
        @csrf
        <div class="section-body">
            <h2 class="section-title">{{ empty($product_segment) ? 'New Product Segment' : 'Update' }}</h2>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h4>{{ empty($product_segment) ? 'Add New Product Segment' : 'Update Product Segment' }}</h4>
                        </div>

                        <div class="card-body">
                            @include('admin.shared.flash')

                            <div class="col">
                                <div class="form-group">
                                    <label>Name</label>
                                    <input type="text" name="name"
                                           class="form-control @error('name') is-invalid @enderror @if (!$errors->has('name') && old('name')) is-valid @endif"
                                           value="{{ old('name', !empty($product_segment) ? $name : null) }}">
                                    @error('name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>


                                <div class="form-group">
                                    <label>Is Active</label>
                                    <select class="form-control" name="is_active">
                                        @foreach ($is_active_list as $key => $value)
                                            <option value="{{ $value }}" {{ $value == $is_active ? 'selected' : '' }}>
                                                {{ $key }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>


                            </div>

                        </div>

                        <div class="card-footer text-right">
                            <button
                                class="btn btn-primary">{{ empty($product_segment) ? __('general.btn_create_label') : __('general.btn_update_label') }}</button>
                        </div>
                    </div>

                </div>

{{--                <div class="col">--}}
{{--                    <div class="card">--}}
{{--                        <div class="card-header">--}}
{{--                            Brand--}}
{{--                        </div>--}}
{{--                        <div class="card-body">--}}


{{--                            --}}

{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}

{{--                <div class="col">--}}
{{--                    <div class="card">--}}
{{--                        <div class="card-header">--}}
{{--                            <h4>{{ __('users.set_user_permissions_label') }}</h4>--}}
{{--                        </div>--}}
{{--                        <div class="card-body">--}}
{{--                            @include('admin.roles._permissions')--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}

            </div>
        </div>
        </form>
    </section>
@endsection
