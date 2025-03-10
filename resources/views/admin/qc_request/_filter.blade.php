<form method="GET" action="{{ route('qc_request.index') }}">
    <div class="form-row" style="background-color: white; margin: -16px 0px 0px 0px; padding: 0px 0px 0px 12px;">
        <hr width="99%" />
        <div class="form-group col-md-1" style="max-width: 12.666%; margin-top: -10px;" >
            <div class="text-small font-600-bold" style="color: #D733FF;"><i class="fas fa-circle"></i> Vendor Audit</div>
            <div class="text-small font-600-bold" style="color: #0C67EA;"><i class="fas fa-circle"></i> Onsite QC</div>
            <div class="text-small font-600-bold" style="color: #FF9331;"><i class="fas fa-circle"></i> Lab Test</div>
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
            <input type="text" name="material" id="material" class="design-field"
                   placeholder="Type Material #"
                   value="{{ !empty($filter['material']) ? $filter['material'] : old('material') }}">
        </div>
        <div class="form-group col-md-2">
            <input type="text" name="vendor_code" class="design-field"
                    placeholder="Type Vendor Code"
                   value="{{ !empty($filter['vendor_code']) ? $filter['vendor_code'] : old('vendor_code') }}">
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
