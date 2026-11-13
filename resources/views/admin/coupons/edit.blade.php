@extends('layouts.admin')

@section('title', 'Edit Voucher - GoSport')
@section('admin-title', 'Edit Voucher')

@section('content')
    <form action="{{ route('admin.coupons.update', $coupon) }}" method="POST">
        @csrf
        @method('PUT')
        @include('admin.coupons._form', ['submitLabel' => 'Perbarui Voucher'])
    </form>
@endsection
