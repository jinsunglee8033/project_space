<form method="GET" action="{{ route('mm_request.board') }}">
    <div class="form-row" style="background-color: white; margin: -16px 0px 0px 0px; padding: 0px 0px 0px 12px;">
        <hr width="99%" />
        <div class="form-group col-md-2" style="max-width: 12.666%; margin-top: -10px;" >
            <div class="text-small font-600-bold" style="color: #D733FF;"><i class="fas fa-circle"></i> New</div>
            <div class="text-small font-600-bold" style="color: #0C67EA;"><i class="fas fa-circle"></i> Update</div>
            <div class="text-small font-600-bold" style="color: #FF9331;"><i class="fas fa-circle"></i> Dimensions & Logistics</div>
            <div class="text-small font-600-bold" style="color: #FF332E;"><i class="fas fa-circle"></i> Price</div>
        </div>

        <div class="form-group col-md-2">
            <select class="design-select" name="assignee">
                <option value="">Select Assignee</option>
                @foreach ($mm_request_assignee_list as $value)
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
            <select class="design-select" name="brand">
                <option value="">Select Brand</option>
                @foreach ($brands as $value)
                    <option value="{{ $value->name }}" @if( $value->name == $brand) selected="selected" @endif >
                        {{$value->name}}
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
