@extends('layouts.app')

@section('content')

<div class="container">
@include ('layouts.errors')
  <div class="panel panel-primary">
    <div class="panel-heading">
      <h3 class="panel-title">Moon Extractions for {{$character->corporation_name}}</h3>
    </div>
    <div class="panel-body">
    <table class="table-stripedi table-responsive table">
      <thead>
      <tr>
        <th>Location</th>
        <th>Ores</th>
        <th>Estimated Value</th>
        <th>Fracture Time</th>
      </tr>
      </thead>
    @foreach($extractions as $extraction)
      <tr>
        <td>{{$extraction->moon_name}}</td>
        <td>{{$extraction->ores}}</td>
        <td>${{number_format($extraction->value)}}</td>
        <td>@if($extraction->fracture_pref == 'auto_fracture') {{$extraction->natural_decay_time}} @else {{$extraction->chunk_arrival_time}} @endif</td>
      </tr>
    @endforeach
    </table>
  </div> <!-- close panelbody -->
  </div> <!-- close discord_webhook panel -->
</div> <!-- close container -->
@endsection

