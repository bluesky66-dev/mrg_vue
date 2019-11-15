@extends('layouts.app')

@section('title', trans('action_plan.review.page_title'))

@section('content')
    <div id="momentum-app"  class="">
        <momentum-action-plans-edit mode="review"></momentum-action-plans-edit>
    </div>
@endsection
