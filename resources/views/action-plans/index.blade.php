@extends('layouts.app')

@section('title', trans('action_plan.index.page_title'))

@section('content')
    <div id="momentum-app"  class="">
        <momentum-action-plans-index></momentum-action-plans-index>
    </div>
@endsection
