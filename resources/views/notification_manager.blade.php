@extends('layouts.app')

@section('content')
<div class="container">
  <div class="panel panel-primary">
    <div class="panel-heading">
      <h3 class="panel-title"><strong>Notifications</strong></h3>
    </div>
    <div class="panel-body">
    @if(isset($characters) && count($characters))
      @foreach($characters as $char)
        <p><strong>{{str_replace('_', ' ', $char->character_name)}}</strong></p>
        @isset($char->discord_webhook)
          <form method="POST" action="{{ url('/webhook/delete') }}/{{$char->character_id}}">
          <div class="form-group">
          <p><h6>{{$char->discord_webhook}}</h6></p>
            {{ csrf_field() }}
            {{ method_field('DELETE') }}
            <button type="submit" class="btn btn-danger btn-xs">Delete</button>
            </div>
          </form>
        @else
        <p><h6>No webhook set</h6></p>
        @endisset
        <form method="POST" action="{{ url('/webhook') }}/{{$char->character_id}}">
          {{ csrf_field() }}
          <div class="form-group">
            <input type="text" class="form-control" name="discord_webhook" id="discord_webhook" width="10" required>
            <button type="submit" class="btn btn-success btn-xs">@isset($char->discord_webhook) Update @else Add @endisset</button>
          </div>
        </form>
      @endforeach
    @endif
  </div>
  </div> <!-- close discord_webhook panel -->
</div> <!-- close container -->
@endsection

