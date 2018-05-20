@extends('general')

@section('title', 'List of Publications')

@section('css')
    <!-- All css imports or <style></style> here. -->

    <link rel="stylesheet" type="text/css" href="/css/pagination.css">
@endsection

@section('content')
    <!--All body code here.-->

    <div id="pagination-box-style" class="ajax-pagination">
        @include('lists.pag.publications')
    </div>

    @include('pagination-ajax', ['class_name' => 'ajax-pagination', 'object_title' => 'List of Publications'])

@endsection