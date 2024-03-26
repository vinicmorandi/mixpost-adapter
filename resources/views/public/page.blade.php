@extends('mixpost::layouts.public')

@section('title')
    {{ $page->meta_title ? : $page->name }}
@endsection

@section('meta')
    @if($page->meta_description)
        <meta name="description" content="{{ $page->meta_description }}">
    @endif
@endsection

@section('content')
    <div class="page">
        @includeWhen($page->layout === 'default', 'mixpost::page_layouts.default')
        @includeWhen($page->layout === 'medium', 'mixpost::page_layouts.medium')
        @includeWhen($page->layout === 'small', 'mixpost::page_layouts.small')
    </div>
@endsection
