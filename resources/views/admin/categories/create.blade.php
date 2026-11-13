@extends('layouts.admin')

@section('title', 'Tambah Kategori - GoSport')
@section('admin-title', 'Tambah Kategori')

@section('content')
    <form action="{{ route('admin.categories.store') }}" method="POST">
        @csrf
        @include('admin.categories._form', ['submitLabel' => 'Tambah Kategori'])
    </form>
@endsection
