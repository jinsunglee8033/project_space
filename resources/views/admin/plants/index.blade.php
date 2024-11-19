@extends('layouts.dashboard')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Plants</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item">Plants</div>
        </div>
    </div>

    <div class="section-body">

        @include('admin.plants._filter')
        @include('admin.shared.flash')


        <div class="row" style="margin-top: 15px;">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-md">
                                <thead>
                                    <th>No.</th>
                                    <th>Name</th>
                                    <th>Is Active</th>
                                    <th width="25%">@lang('general.action_label')</th>
                                </thead>
                                <tbody>
                                    @forelse ($plants as $plant)
                                        <tr>
                                            <td>{{ $plant->id}}</td>
                                            <td>{{ $plant->name}}</td>
                                            <td>{{ $plant->is_active}}</td>
                                            <td>
                                                <a class="btn btn-sm" href="{{ url('admin/plants/'. $plant->id .'/edit')}}">
                                                <i class="far fa-edit"></i> @lang('general.btn_edit_label')</a>
                                            </td>
                                        </tr>
                                    @empty

                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        {{ $plants->appends(['q' => !empty($filter['q']) ? $filter['q'] : ''])->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
