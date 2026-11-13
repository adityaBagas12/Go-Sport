@extends('layouts.admin')

@section('title', 'Tambah Produk - GoSport')
@section('admin-title', 'Tambah Produk')

@section('content')
    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('admin.products._form', ['submitLabel' => 'Tambah Produk'])
    </form>
@endsection
