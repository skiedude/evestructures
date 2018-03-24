@extends('layouts.app')

@section('content')

<div class="container">
@include ('layouts.errors')
  <div class="panel panel-primary">
    <div class="panel-heading">
      <h3 class="panel-title"><strong>Shareable Extraction URL</strong> -- Only Letters/Numbers allowed. No symbols or spaces</h3>
    </div>
    <div class="panel-body">
      <div class="row">
        <div class="col-sm-8 col-sm-offset-2">
          @foreach($characters as $char)
            @foreach($slugs as $slug)
              @if($char->character_id == $slug->character_id)
                <h3>{{str_replace('_', ' ', $char->character_name)}} </h3>{{ $char->corporation_name }} <button onclick="copyToClipboard('#slug_url')"><i class="fas fa-copy"></i></button>
                <form method="POST" action="{{ url('/extraction/create') }}/{{$char->character_id}}" class="form-inline">
                  {{ csrf_field() }}
                  <div class="form-group">
                    <label for="slug_name">URL: {{env('APP_URL')}}/extraction/{{$char->corporation_id}}/ </label>
                    <input type="text" class="form-control" name="slug_name" id="slug_name" value="{{$slug->slug_name ?? ''}}">
                  </div>
                  <div class="checkbox">
                    <label>
                      <input type="checkbox" name="status" @if($slug->enabled == TRUE) checked @endif > Enabled
                    </label>
                  </div>
                  <div class="form-group">
                    <button type="submit" class="btn btn-success btn-xs">Update</button>
                  </div>
                </form>
                @isset($slug->slug_name)
                  <p style="display:none" id="slug_url">{{env('APP_URL')}}/extraction/{{$char->corporation_id}}/{{$slug->slug_name ?? ''}}</p>
                @endisset
              @endif
            @endforeach <!-- $slugs -->
          @endforeach <!--$characters -->
        </div> <!-- close col-sm-8 -->
      </div><!-- close row -->
  </div> <!-- close panelbody -->
  </div> <!-- close discord_webhook panel -->
</div> <!-- close container -->
<script>
function copyToClipboard(element) {
  var $temp = $("<input>");
  $("body").append($temp);
  $temp.val($(element).text()).select();
  document.execCommand("copy");
  $temp.remove();
}

</script>
@endsection

