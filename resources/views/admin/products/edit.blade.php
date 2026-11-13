@extends('layouts.admin')

@section('title', 'Edit Produk - GoSport')
@section('admin-title', 'Edit Produk')

@section('content')
    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.products._form', ['submitLabel' => 'Perbarui Produk'])
    </form>
@endsection
