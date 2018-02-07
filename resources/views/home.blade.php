@extends('layouts.app')

@section('content')

<div class="container">
	@include ('layouts.errors');
	<button onclick="topFunction()" id="scrollBtn" class="btn btn-info"><i class="fa fa-arrow-up" aria-hidden="true"></i></button>

	<!-- Account delete modal -->
  <div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" id="delete_account" aria-labelledby="modal_account">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-body">
          <p>Are you sure you want to delete your account? All characters, structures and tokens will be revoked.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <a href="/account/delete"><button type="button" class="btn btn-danger">Confirm</button></a>
        </div>
      </div> <!-- /.modal-content -->
    </div> <!-- /.modal-dialog -->
  </div> <!-- /.modal -->
	<!-- Account delete modal -->

	<div class="row">
		<div class="col-sm-8">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<a data-toggle="collapse" href="#webhookCollapse" aria-expanded="false" aria-controls="webhookCollapse" class="link-unstyled">
						<h3 class="panel-title"><strong>Discord Webhooks <span class="caret"></span></strong></h3>
					</a>			
				</div>
				<div class="panel-body">
				<div class="collapse" id="webhookCollapse">
				@if(isset($characters) && count($characters))
					@foreach($characters as $char)
						<p><strong>{{str_replace('_', ' ', $char->character_name)}}</strong></p>
						@isset($char->discord_webhook)
							<form method="POST" action="/webhook/delete/{{$char->character_id}}">
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
						<form method="POST" action="/webhook/{{$char->character_id}}">
							{{ csrf_field() }}
							<div class="form-group">
								<input type="text" class="form-control" name="discord_webhook" id="discord_webhook" width="10" required>
								<button type="submit" class="btn btn-success btn-xs">@isset($char->discord_webhook) Update @else Add @endisset</button>
							</div>
						</form>
					@endforeach
				@endif
				</div>
			</div>
			</div> <!-- close discord_webhook panel -->

			<div class="panel panel-primary">
				<div class="panel-heading"><h3 class="panel-title"><strong>Characters</strong></h3></div>
				<table class="table table-responsive table-condensed table-striped">
					<tbody>
						@if(isset($characters) && count($characters))
						<thead>
						<tr>
							<th></th>
							<th>Name</th>
							<th>Corporation</th>
							<th class="text-center" colspan="2">Actions</th>
						</tr>
						</thead>
						@foreach($characters as $char) <!-- Loop over characters -->
						<tr>
							<td><img src="https://imageserver.eveonline.com/Character/{{$char->character_id}}_32.jpg"></td>
							<td><a href="#{{$char->character_name}}">{{str_replace('_', ' ', $char->character_name)}}</a></td>
							<td>{{$char->corporation_name}}</td>
							<td align="right"><a href="/fetch/{{$char->character_id}}"><button class="btn btn-default">Fetch</button></a>
							<button class="btn btn-danger" type="button" data-toggle="modal" data-target="#delete_{{$char->character_id}}">Delete</button>
							</td>
						</tr>
						<div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" id="delete_{{$char->character_id}}" aria-labelledby="modal_{{$char->character_id}}">
						  <div class="modal-dialog" role="document">
						    <div class="modal-content">
						      <div class="modal-body">
						        <p>Are you sure you want to delete {{$char->character_name}}. This also removes all structures attached to this character.</p>
						      </div>
						      <div class="modal-footer">
						        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
						        <a href="/delete/{{$char->character_id}}"><button type="button" class="btn btn-danger">Confirm</button></a>
						      </div>
						    </div> <!-- /.modal-content -->
						  </div> <!-- /.modal-dialog -->
						</div> <!-- /.modal -->
						@endforeach
						@else
						<tr>
							<td>No characters found</td>
						</tr>
						@endif
					</tbody>
				</table>
			</div> <!-- close character panel -->
			@if(isset($structures) && count($structures))
			@foreach($characters as $char) <!-- Loop over characters -->
			<div class="panel panel-primary" id="{{$char->character_name}}">
				<div class="panel-heading"><h3 class="panel-title"> <img src="https://imageserver.eveonline.com/Character/{{$char->character_id}}_32.jpg">&nbsp<strong>{{str_replace('_', ' ', $char->character_name)}}</strong> | {{$char->corporation_name}}</h3></div>
				<table class="table table-condensed">
					<tbody>
						<thead>
						<tr>
							<th></th>
							<th>Station Name</th>
							<th>System</th>
							<th>Fuel</th>
						</tr>
						</thead>

						@foreach($structures as $str) <!-- Loop over structures -->
						@if($str->character_id == $char->character_id)
						<tr>
							<td><img src="https://imageserver.eveonline.com/Type/{{$str->type_id}}_32.png"></td>
							<td><a href="/home/structure/{{$str->structure_id}}">{{$str->structure_name}}</a></td>
							<td>{{$str->system_name}}</td>
							@if(is_null($str->fuel_days_left))
							<td>{{$str->fuel_expires}}</td>
							@else
							<td class="@if($str->fuel_days_left <= 1)one_day @elseif($str->fuel_days_left < 30)thirty_less @elseif($str->fuel_days_left >= 30)thirty_plus @else @endif">{{$str->fuel_time_left}}</td>
							@endif
						</tr>
						@endif
						@endforeach
					
					</tbody>
				</table>
			</div><!-- close structure panel -->
			@endforeach
			@else
			<div class="panel panel-primary">
				<div class="panel-heading"><h3 class="panel-title">Structures</h3></div>
				<table class="table table-condensed">
					<tbody>
					<thead>
					<tr>
						<th></th>
						<th>Station Name</th>
						<th>System</th>
						<th>Fuel</th>
					</tr>
					</thead>
					<tr>
						<td colspan="4">No structures found</td>
					</tr>
					</tbody>
				</table>
			</div>
			@endif

		@if(env('APP_ENV') == 'prod')
			@includeIf('google.ads_home')
		@endif

		</div> <!-- close col-sm-8 -->

	  <div class="col-sm-3 col-sm-offset-1">
			<div class="panel panel-warning">
				<div class="panel-heading"><h3 class="panel-title"><strong>Add Character</strong></h3></div>
				<a href="/sso/login">
					<img src="/images/small_black_login.png" alt="Login Button" class="pager center-block">
				</a> 
			</div> <!-- close add character panel -->
		</div> <!-- close col-sm-3 -->
	</div> <!-- close row -->
</div> <!-- close container-fluid -->
@endsection
