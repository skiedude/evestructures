@extends('layouts.app')

@section('content')
@include ('layouts.errors')

<div class="container">
  <div class="panel panel-primary">
    <div class="panel-heading">
      <h3 class="panel-title"><strong>Notifications</strong></h3>
    </div>
    <div class="panel-body">
    @if(isset($notifications) && count($notifications))
      @foreach($notifications as $notify)
        <p><strong>{{str_replace('_', ' ', $notify->character_name)}}</strong></p>
        <form method="POST" action="{{ url('/webhook') }}/{{$notify->char_id}}">
          {{ csrf_field() }}
          <div class="form-group">
            <input type="text" class="form-control" name="discord_webhook" id="discord_webhook" width="10" value="{{$notify->discord_webhook ?? 'Insert Webhook'}}" required>
            <div class="checkbox">
              <label>
                <input type="checkbox" name="low_fuel" value="enable" @if(isset($notify->low_fuel) && $notify->low_fuel == TRUE) checked @endif>
                Low Fuel
              </label>
            </div>
            <div class="checkbox">
              <label>
                <input type="checkbox" name="strct_state" value="enable" @if(isset($notify->strct_state) && $notify->strct_state == TRUE) checked @endif>
               Structure States 
              </label>
            </div>
            <div class="checkbox">
              <label>
                <input type="checkbox" name="extractions" value="enable" @if(isset($notify->extractions) && $notify->extractions == TRUE) checked @endif>
                Extractions
              </label>
            </div>
            <div class="checkbox">
              <label>
                <input type="checkbox" name="unanchor" value="enable" @if(isset($notify->unanchor) && $notify->unanchor == TRUE) checked @endif>
                Unanchor
              </label>
            </div>
            <button type="submit" class="btn btn-success btn-xs">@isset($notify->discord_webhook) Update @else Add @endisset</button>
          </div>
        </form>
        <form method="POST" action="{{ url('/webhook/delete') }}/{{$notify->char_id}}">
        <div class="form-group">
          {{ csrf_field() }}
          {{ method_field('DELETE') }}
          <button type="submit" class="btn btn-danger btn-xs">Delete</button>
        </div>
        </form>
      <hr>
      @endforeach
    @endif
  </div>
  </div> <!-- close discord_webhook panel -->
</div> <!-- close container -->
@endsection

