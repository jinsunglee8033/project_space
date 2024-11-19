@extends('layouts.dashboard')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>@lang('users.user_management')</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item">Users</div>
        </div>
    </div>

    <div class="section-body">

        @include('admin.users._filter')
        @include('admin.shared.flash')


        <div class="row" style="margin-top: 15px;">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-md">
                                <thead>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Email</th>
                                    <th>Team</th>
                                    <th>Role</th>
                                    <th>Is Active</th>
{{--                                    <th>Role</th>--}}
{{--                                    <th>Access Level</th>--}}
                                    <th width="25%">@lang('general.action_label')</th>
                                </thead>
                                <tbody>
                                    @forelse ($users as $user)
                                        <tr>
                                            <td>{{ $user->first_name}}</td>
                                            <td>{{ $user->last_name}}</td>
                                            <td>{{ $user->email}}</td>
                                            <td>{{ $user->team}}</td>
                                            <td>{{ ucwords($user->role)}}</td>
                                            <td>{{ ucwords($user->is_active) }}</td>
{{--                                            <td>Here!</td>--}}
{{--                                            <td>{{ $user->access_level }}</td>--}}
                                            <td>
{{--                                                @can('edit_users')--}}
                                                    <a class="btn btn-sm" href="{{ url('admin/users/'. $user->id .'/edit')}}"><i class="far fa-edit"></i> @lang('general.btn_edit_label') </a>
{{--                                                @endcan--}}

{{--                                                @can('delete_users')--}}
{{--                                                    <a href="{{ url('admin/users/'. $user->id) }}" class="btn btn-sm" onclick="--}}
{{--                                                        event.preventDefault();--}}
{{--                                                        if (confirm('Do you want to remove this User?')) {--}}
{{--                                                            document.getElementById('delete-role-{{ $user->id }}').submit();--}}
{{--                                                        }">--}}
{{--                                                        <i class="far fa-trash-alt"></i> Delete--}}
{{--                                                    </a>--}}
{{--                                                    <form id="delete-role-{{ $user->id }}" action="{{ url('admin/users/'. $user->id) }}" method="POST">--}}
{{--                                                        <input type="hidden" name="_method" value="DELETE">--}}
{{--                                                        @csrf--}}
{{--                                                    </form>--}}
{{--                                                @endcan--}}
                                            </td>
                                        </tr>
                                    @empty

                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        {{ $users->appends(['team' => !empty($filter['team']) ? $filter['team'] : '', 'role' => !empty($filter['role']) ? $filter['role'] : ''])->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
