@extends('general')

@section('title', 'List of Sent Messages')

@section('css')
    <!-- All css imports or <style></style> here. -->

    <link rel="stylesheet" type="text/css" href="/css/pagination.css">
    <link rel="stylesheet" type="text/css" href="/css/app.css">
@endsection

@section('content')
    <!--All body code here.-->

    <div id="pagination-box-style" class="ajax-pagination">
        @include('message.messages-sent-pag')
    </div>

    @include('pagination-ajax', ['class_name' => 'ajax-pagination', 'object_title' => 'List of Genres'])

@endsection