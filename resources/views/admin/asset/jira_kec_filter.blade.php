
<form method="GET" action="{{ route('asset.jira_kec') }}">
    <div class="form-row" style="background-color: white; margin: -16px 0px 0px 0px; padding: 0px 0px 0px 12px;">
        <hr width="99%" />
        <div class="form-group col-md-1" style="max-width: 12.666%; margin-top: -10px;" >
            <div class="text-small font-600-bold" style="color: #03b06d;"><i class="fas fa-circle"></i> Creative</div>
            <div class="text-small font-600-bold" style="color: #9f76c2;"><i class="fas fa-circle"></i> Content</div>
            <div class="text-small font-600-bold" style="color: #017cc2;"><i class="fas fa-circle"></i> Web</div>
        </div>

        <div class="form-group col-md-2">
            <select class="design-select" name="team">
                    <option value="">Select Team</option>
                @foreach ($teams as $key => $value)
                    <option value="{{ $value }}" @if( $value == $team_) selected="selected" @endif >
                        {{ucfirst($value)}}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group col-md-2">
            <select class="design-select" name="brand">
                <option value="">Select Brand</option>
                @foreach ($brands as $key => $value)
                    <option value="{{ $key }}" @if( $key == $brand_) selected="selected" @endif >
                        {{$value}}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group col-md-2">
            <input type="text" name="q" class="design-field" id="q" placeholder="Author Name" value="{{ !empty($filter['q']) ? $filter['q'] : '' }}">
        </div>

        <div class="form-group col-md-2">
            <input type="text" name="asset_id" class="design-field" id="asset_id" placeholder="Asset ID" value="{{ !empty($filter['asset_id']) ? $filter['asset_id'] : '' }}">
        </div>

        <div class="form-group col-md-1">
            <button class="design-btn">Apply</button>
        </div>

        <div class="form-group col-md-2">
            <div class="follow" style="float:right; margin-top: -5px; padding-right: 15px;">
                <i class="left" onclick="moveScrollRight()" ></i>
                <i class="right" onclick="moveScrollLeft()" ></i>
            </div>
        </div>
    </div>
</form>
