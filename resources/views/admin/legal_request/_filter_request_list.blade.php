<form method="GET" action="{{ route('legal_request.registration_list') }}">
    <div class="form-row" style="background-color: white; margin: -16px 0px 0px 0px; padding: 0px 0px 0px 12px;">
        <hr width="99%" />
{{--        <div class="form-group col-md-2">--}}
{{--            <input type="text" name="q" class="design-field" id="q" placeholder="Materials" value="{{ !empty($filter['q']) ? $filter['q'] : '' }}">--}}
{{--        </div>--}}
        <div class="form-group col-md-2">
            <select class="design-select" name="assignee">
                <option value="">Select Assignee</option>
                @foreach ($legal_request_assignee_list as $value)
                    <option value="{{ $value->id }}" @if( $value->id == $assignee) selected="selected" @endif >
                        {{$value->first_name}} {{$value->last_name}}
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
            <select class="design-select" name="status">
                <option value="">Select Status</option>
                @foreach ($status_list as $value)
                    <option value="{{ $value }}" @if( $value == $status) selected="selected" @endif >
                        {{ strtoupper(str_replace('_', ' ', $value)) }}
                    </option>
                @endforeach
            </select>
        </div>

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
