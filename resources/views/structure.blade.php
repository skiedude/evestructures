@extends('layouts.app')

@section('content')
<div class="container">
  @include ('layouts.errors')
  <div class="row">
    <div class="col-sm-12">
      <div class="panel panel-primary">
        <div class="panel-heading"> 
          <a href="{{ url('/home') }}" style="color:white" ><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Back</a>
        </div>
          <div class="media" style="padding:10px">
            <div class="media-left">
              <img src="https://imageserver.eveonline.com/Type/{{$structure->type_id}}_64.png">
            </div>
            <div class="media-body">
              <h3 class="media-heading">{{$structure->structure_name}}</h3>
              <div class="row">
                
                <div class="col-sm-4">
                  <div class="panel panel-success">
                    <table class="table table-condensed table-bordered">
                      <tr>
                        <th colspan="2">Info</th>
                      </tr>
                      <tr>
                        <td>Current State</td>
                        <td>{{ucwords($state->state)}}</td>
                      </tr>
                      <tr>
                        <td>Current State Start</td>
                        <td>@isset($state->state_timer_start) {{$state->state_timer_start}} @else n/a @endisset</td>
                      </tr>
                      <tr>
                        <td>Current State End</td>
                        <td>@isset($state->state_timer_end) {{$state->state_timer_end}} @else n/a @endisset</td>
                      </tr>
                      <tr>
                        <td>System</td>
                        <td>{{$structure->system_name}}</td>
                      </tr>
                      <tr>
                        <td>Fuel Expires</td>
                        <td>{{$structure->fuel_expires}}</td>
                      </tr>
                      <tr>
                        <td>Unanchors at</td>
                        <td>{{$structure->unanchors_at}}</td>
                      </tr>
                    </table>
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="panel panel-success">
                    <table class="table table-condensed table-bordered">
                      <tr>
                        <th colspan="2">Reinforce Schedule</th>
                      </tr>
                      <tr>
                        <td>Reinforce Day</td>
                        <td>{{$vul->day}}</td>
                      </tr>
                      <tr>
                        <td>Reinforce Hour</td>
                        <td>{{$vul->hour}}:00</td>
                      </tr>
                      <tr>
                        <td>Pending Reinforce Day</td>
                        <td>{{$vul->next_day}}</td>
                      </tr>
                      <tr>
                        <td>Pending Reinforce Hour</td>
                        <td>@isset($vul->next_hour) {{$vul->next_hour}}:00 @else @endisset</td>
                      </tr>
                      <tr>
                        <td>Pending Reinforce Applies</td>
                        <td>{{$vul->next_reinforce_apply}}</td>
                      </tr>

                    </table>
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="panel panel-success">
                    <table class="table table-condensed table-bordered">
                      <tr>
                        <th>Service Name</th>
                        <th>Status</th>
                      </tr>
                      @if(count($services))
                      @foreach($services as $svc)
                      <tr>
                        <td>{{$svc->name}}</td>
                        <td @if($svc->state == "online") style="color:green;" @else style="color:red" @endif>
                          <strong>{{$svc->state}}</strong>
                        </td>
                      </tr>
                      @endforeach
                      @endif
                    </table>
                  </div>
                </div>
              </div> <!-- end row -->
              @if(!is_null($extraction))
              <div class="row">
                <div class="col-sm-4">
                  <div class="panel panel-success">
                    <table class="table table-condensed table-bordered">
                      <tr>
                        <th colspan="3">Extraction</th>
                      </tr>
                      <tr>
                        <td>Location</td>
                        <td colspan="2">{{$extraction->moon_name}}</td>
                      </tr>
                      <tr>
                        <td>Extraction Start</td>
                        <td>{{$extraction->extraction_start_time}}</td>
                        <td>{{$extraction->extraction_start_time->diffForHumans()}}</td>
                      </tr>
                      <tr>
                        <td>Chunk Arrival</td>
                        <td>{{$extraction->chunk_arrival_time}}</td>
                        <td>{{$extraction->chunk_arrival_time->diffForHumans()}}</td>
                      </tr>
                      <tr>
                        <td>Auto Fracture</td>
                        <td>{{$extraction->natural_decay_time}}</td>
                        <td>{{$extraction->natural_decay_time->diffForHumans()}}</td>
                      </tr>
                    </table>
                  </div>
                </div>
              </div> <!-- end row -->
              @endif
            </div> <!-- end media-body-->

          </div> <!-- end media -->
      </div> <!-- end panel -->

    @if(env('APP_ENV') == 'prod')
      @includeIf('google.ads_structures')
    @endif

    </div>
  </div> <!-- end row -->
</div> <!-- close container -->
@endsection
