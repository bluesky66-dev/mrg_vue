@extends('layouts.app')

@section('title', trans('pulse_survey.review.page_title'))

@section('content')
    <div id="momentum-app"  class="container">
        <momentum-pulse-surveys mode="edit"></momentum-pulse-surveys>
    </div>
@endsection
