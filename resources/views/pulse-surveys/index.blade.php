@extends('layouts.app')

@section('title', trans('pulse_survey.index.page_title'))

@section('content')
    <div id="momentum-app"  class="">
        <momentum-pulse-surveys mode="index"></momentum-pulse-surveys>
    </div>
@endsection
