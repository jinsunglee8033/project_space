@extends('layouts.dashboard')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Asset Lead Time *</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item active">Asset Lead Time</div>
        </div>
    </div>
    <div class="section-body">

        <div class="table-responsive">
            <br><br>
            <table style="text-align: center;
                    border-collapse:separate;
                    background-color: #f4f6f9">
                <tbody>
                <tr>
                    <th></th>
                    <th style="width:100px; color: #000000; border-radius: 5px; background-color: #e0e0e0;">
                        Copywriter Assign
                    </th>
                    <th style="width:100px; color: #000000; border-radius: 5px; background-color: #e0e0e0;">
                        Copy
                    </th>
                    <th style="width:100px; color: #000000; border-radius: 5px; background-color: #e0e0e0;">
                        Copy Review
                    </th>
                    <th style="width:100px; color: #000000; border-radius: 5px; background-color: #e0e0e0;">
                        Creator Assign
                    </th>
                    <th style="width:100px; color: #000000; border-radius: 5px; background-color: #e0e0e0;">
                        Creative Work
                    </th>
                    <th style="width:100px; color: #000000; border-radius: 5px; background-color: #e0e0e0;">
                        Final Review
                    </th>
                    <th style="width:100px; color: #000000; border-radius: 5px; background-color: #e0e0e0;">
                        Development
                    </th>
                    <th style="width:100px; color: #000000; border-radius: 5px; background-color: #e0e0e0;">
                        KDO
                    </th>
                    <th style="width:100px; color: #000000; border-radius: 5px; background-color: #e0e0e0;">
                        Time to Spare
                    </th>
                    <th style="color: #fffdfd; border-radius: 5px; background-color: #000000;">
                        Total Days
                    </th>
                </tr>

                @foreach ($assets as $asset)
                    <tr>
                        <td style="padding: 0 10px; height: 40px; color: #ffffff; font-weight: bold; border-radius: 5px; background-color: #424242;">{{ $asset['asset_name'] }}</td>
                        <td style="padding: 0 10px; height: 40px; color: #666666; font-weight: bold; border-radius: 5px; background-color: #ffffff;">{{ $asset['copywriter_assign'] }}</td>
                        <td style="padding: 0 10px; height: 40px; color: #666666; font-weight: bold; border-radius: 5px; background-color: #ffffff;">{{ $asset['copy'] }}</td>
                        <td style="padding: 0 10px; height: 40px; color: #666666; font-weight: bold; border-radius: 5px; background-color: #ffffff;">{{ $asset['copy_review'] }}</td>
                        <td style="padding: 0 10px; height: 40px; color: #666666; font-weight: bold; border-radius: 5px; background-color: #ffffff;">{{ $asset['creator_assign'] }}</td>
                        <td style="padding: 0 10px; height: 40px; color: #666666; font-weight: bold; border-radius: 5px; background-color: #ffffff;">{{ $asset['creative_work'] }}</td>
                        <td style="padding: 0 10px; height: 40px; color: #666666; font-weight: bold; border-radius: 5px; background-color: #ffffff;">{{ $asset['final_review'] }}</td>
                        <td style="padding: 0 10px; height: 40px; color: #666666; font-weight: bold; border-radius: 5px; background-color: #ffffff;">{{ $asset['development'] }}</td>
                        <td style="padding: 0 10px; height: 40px; color: #666666; font-weight: bold; border-radius: 5px; background-color: #ffffff;">{{ $asset['kdo'] }}</td>
                        <td style="padding: 0 10px; height: 40px; color: #666666; font-weight: bold; border-radius: 5px; background-color: #ffffff;">{{ $asset['time_to_spare'] }}</td>
                        <td style="padding: 0 10px; height: 40px; color: #ffffff; font-weight: bold; border-radius: 5px; background-color: #9a9a9a;">{{ $asset['total'] }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

</section>

@endsection
