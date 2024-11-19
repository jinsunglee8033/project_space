@extends('layouts.dashboard')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>@lang('users.user_management')</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ url('admin/users') }}">Users</a></div>
                <div class="breadcrumb-item">Edit User</div>
            </div>
        </div>
        @if (empty($user ?? '' ?? ''))
            <form method="POST" action="{{ route('users.store') }}">
            @else
                <form method="POST" action="{{ route('users.update', $user->id) }}">
                    <input type="hidden" name="id" value="{{ $user->id }}" />
                    @method('PUT')
        @endif
        @csrf
        <div class="section-body">
            <h2 class="section-title">{{ empty($user) ? __('users.user_add_new') : __('users.user_update') }}</h2>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h4>{{ empty($user) ? __('users.add_card_title') : __('users.update_card_title') }}</h4>
                        </div>

                        <div class="card-body">
                            @include('admin.shared.flash')

                            <div class="col">
                                <div class="form-group">
                                    <label>First Name</label>
                                    <input type="text" name="first_name"
                                           class="form-control @error('first_name') is-invalid @enderror @if (!$errors->has('first_name') && old('first_name')) is-valid @endif"
                                           value="{{ old('first_name', !empty($user) ? $user->first_name : null) }}">
                                    @error('first_name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Last Name</label>
                                    <input type="text" name="last_name"
                                           class="form-control @error('last_name') is-invalid @enderror @if (!$errors->has('last_name') && old('last_name')) is-valid @endif"
                                           value="{{ old('last_name', !empty($user) ? $user->last_name : null) }}">
                                    @error('last_name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>@lang('users.email_label')</label>
                                    <input type="text" name="email"
                                           class="form-control @error('email') is-invalid @enderror @if (!$errors->has('email') && old('email')) is-valid @endif"
                                           value="{{ old('email', !empty($user) ? $user->email : null) }}">
                                    @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>@lang('users.password_label')</label>
                                    <input type="password" name="password"
                                           class="form-control @error('password') is-invalid @enderror @if (!$errors->has('password') && old('password')) is-valid @endif">
                                    @error('password')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>@lang('users.password_confirmation_label')</label>
                                    <input type="password" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror @if (!$errors->has('password_confirmation') &&
                                    old('password_confirmation')) is-valid @endif">
                                    @error('password_confirmation')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Team</label>
                                    <select class="form-control" name="team">
                                        <option>Select Team</option>

                                        @foreach ($teams as $value)
                                            <option value="{{ $value }}" {{ $value == $team ? 'selected' : '' }}>
                                                {{ $value }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>@lang('users.role_label')</label>
                                    <select class="form-control" name="role">
                                        <option>@lang('users.select_role_label')</option>

                                        @foreach ($roles_ as $key => $value)
                                            <option value="{{ $value }}" {{ strtolower($value) == $role_ ? 'selected' : '' }}>
                                                {{ $key }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label style="color: #b91d19;">Brand (Only for Copywriter & Creative Director) </label>
                                    <div class="row">
                                        <?php if (isset($brands)): ?>
                                            @foreach($brands as $brand)
                                            <?php $checkbox_fields = explode(', ', $user_brand); ?>
                                                <div class="col-sm-6">
                                                    <div class="form-check">
                                                        <input  <?php if (in_array($brand['campaign_name'], $checkbox_fields)) echo "checked" ?>
                                                                type="checkbox"
                                                                name="user_brand[]"
                                                                value="{{ $brand['campaign_name'] }}"
                                                        >
                                                        <label class="form-check-label " for="{{ $brand['campaign_name'] }}">
                                                        {{ $brand['campaign_name'] }}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        <?php endif; ?>
                                    </div>
                                </div>

                            </div>

                        </div>

                        <div class="card-footer text-right">
                            <button
                                class="btn btn-primary">{{ empty($user) ? __('general.btn_create_label') : __('general.btn_update_label') }}</button>
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
