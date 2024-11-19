@extends('layouts.dashboard')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Brands</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item">Brands</div>
        </div>
    </div>

    <div class="section-body">

        @include('admin.brands._filter')
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
                                    @forelse ($brands as $brand)
                                        <tr>
                                            <td>{{ $brand->id}}</td>
                                            <td>{{ $brand->name}}</td>
                                            <td>{{ $brand->is_active }}</td>
                                            <td>
                                                <a class="btn btn-sm" href="{{ url('admin/brands/'. $brand->id .'/edit')}}">
                                                <i class="far fa-edit"></i> @lang('general.btn_edit_label')</a>
                                            </td>
                                        </tr>
                                    @empty

                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        {{ $brands->appends(['q' => !empty($filter['q']) ? $filter['q'] : ''])->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
