<form method="GET" action="{{ route('npd_design_request.assign_page') }}">
    <div class="form-row" style="background-color: white; margin: -16px 0px 0px 0px; padding: 0px 0px 0px 12px;">
        <hr width="99%" />
        <div class="form-group col-md-1" style="max-width: 12.666%;" >
            <div class="text-small font-600-bold" style="color: #FF0000;"><i class="fas fa-circle"></i> Urgent</div>
            <div class="text-small font-600-bold" style="color: #3d57d9;"><i class="fas fa-circle"></i> Normal</div>
        </div>
        <div class="form-group col-md-2">
            <select class="design-select" name="design_group">
                <option value="">Select Design Group</option>
                @foreach ($design_group_list as $value)
                    <option value="{{ $value }}" @if( $value == $design_group) selected="selected" @endif >
                        {{$value }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-md-2">
            <select class="design-select" name="team">
                <option value="">Select Team</option>
                @foreach ($teams as $value)
                    <option value="{{ $value->name }}" @if( $value->name == $team) selected="selected" @endif >
                        {{$value->name}}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-md-2">
            <select class="design-select" name="brand">
                <option value="">Select Brand</option>
                @foreach ($brands as $value)
                    <option value="{{ $value->name }}" @if( $value->name == $brand) selected="selected" @endif >
                        {{$value->name}}
                    </option>
                @endforeach
            </select>
        </div>
{{--        <div class="form-group col-md-2">--}}
{{--            <select class="design-select" name="assignee">--}}
{{--                <option value="">Select Assignee</option>--}}
{{--                @foreach ($assignee_list as $value)--}}
{{--                    <option value="{{ $value->id }}" @if( $value->id == $assignee) selected="selected" @endif >--}}
{{--                        {{ $value->first_name }} {{ $value->last_name }}--}}
{{--                    </option>--}}
{{--                @endforeach--}}
{{--            </select>--}}
{{--        </div>--}}
        <div class="form-group col-md-2">
            <button class="btn btn-project-apply">Apply</button>
        </div>
        <div class="form-group col-md-4">
        </div>
        <div class="form-group col-md-1">
        </div>
{{--        <div class="form-group col-md-2">--}}
{{--            <div class="follow" style="float:right; margin-top: -5px; padding-right: 15px;">--}}
{{--                <i class="left" onclick="moveScrollRight()" ></i>--}}
{{--                <i class="right" onclick="moveScrollLeft()" ></i>--}}
{{--            </div>--}}
{{--        </div>--}}
    </div>
</form>
