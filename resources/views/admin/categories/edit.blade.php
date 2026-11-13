@extends('layouts.admin')

@section('title', 'Edit Kategori - GoSport')
@section('admin-title', 'Edit Kategori')

@section('content')
    <form action="{{ route('admin.categories.update', $category) }}" method="POST">
        @csrf
        @method('PUT')
        @include('admin.categories._form', ['submitLabel' => 'Perbarui Kategori'])
    </form>
@endsection
