<!-- resources/views/index.blade.php -->
@extends('site::layouts.main')

@section('title', 'Index Page')

@section('content')

@include('site::includes.navbar')

@include('site::includes.Home.hero-default')

@include('site::includes.Home.index-body')

@endsection
