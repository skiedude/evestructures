@extends('layouts.app')

@section('content')
<div class="container">
	@include ('layouts.errors')
	<div class="row">
		<div class="col-sm-8 col-sm-offset-2">
			<div class="panel panel-primary">
				<div class="panel-heading"> 
					<a href="/home" style="color:white" ><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Back</a>
				</div>
					<div class="media" style="padding:10px">
						<div class="media-left">
							<img src="https://imageserver.eveonline.com/Type/{{$structure->type_id}}_64.png">
						</div>
						<div class="media-body">
							<h3 class="media-heading">{{$structure->structure_name}}</h3>
							
							<div class="row">
								<div class="col-sm-6">
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
								<div class="col-sm-6">
									<div class="panel panel-success">
										<table class="table table-condensed table-bordered">
											<tr>
												<th>Info Name</th>
												<th>Value</th>
											</tr>
											<tr>
												<td>Unanchors</td>
												<td>{{$structure->unanchors_at}}</td>
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
												<td>Current State Starts</td>
												<td>{{$state[0]->state_timer_start}}</td>
											</tr>
											<tr>
												<td>Current State Ends</td>
												<td>{{$state[0]->state_timer_end}}</td>
											</tr>
										</table>
									</div>
								</div>
							</div>
						</div>

					</div> <!-- end media -->
			</div> <!-- end panel -->

		@if(env('APP_ENV') == 'prod')
			@includeIf('google.ads_structures')
		@endif

		</div>
	</div> <!-- end row -->
</div> <!-- close container -->
@endsection
