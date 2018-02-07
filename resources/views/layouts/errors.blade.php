@isset($alert)
<div class="container">
	<div class="alert alert-danger alert-dismissible" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<strong>Warning!</strong> {{$alert}}
	</div>
</div>
@endisset

@isset($success)
<div class="container">
	<div class="alert alert-success alert-dismissible" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		 {{$success}}
	</div>
</div>
@endisset

@isset($warning)
<div class="container">
	<div class="alert alert-warning alert-dismissible" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		{{$warning}}
	</div>
</div>
@endisset

@if($errors->any())
<div class="container">
	<div class="alert alert-danger alert-dismissible" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		@foreach($errors->all() as $error)
		{{$error}}
		@endforeach
	</div>
</div>
@endif

