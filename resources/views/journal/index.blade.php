@extends('layouts.app')

@section('title', trans('journal.page_title'))

@section('content')
    <div id="momentum-app"  class="">
        <momentum-journal></momentum-journal>
    </div>
@endsection
