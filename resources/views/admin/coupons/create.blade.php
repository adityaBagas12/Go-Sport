@extends('layouts.admin')

@section('title', 'Tambah Voucher - GoSport')
@section('admin-title', 'Tambah Voucher')

@section('content')
    <form action="{{ route('admin.coupons.store') }}" method="POST">
        @csrf
        @include('admin.coupons._form', ['submitLabel' => 'Tambah Voucher'])
    </form>
@endsection
