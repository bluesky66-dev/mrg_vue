@extends('layouts.login')

@section('title', trans('login.page_title'))

@section('content')

<div id="momentum-login" class="column">

    <div style="text-align:right;">
        <momentum-culture-picker></momentum-culture-picker>
    </div>
        
    <div class="col column justify-center items-center" >
        <momentum-login></momentum-login>
    </div>
    
</div>
@endsection
