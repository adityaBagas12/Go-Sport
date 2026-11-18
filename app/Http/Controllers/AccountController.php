<?php

namespace App\Http\Controllers;

use App\Services\Payment\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AccountController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = $request->user();
        $orders = $user->orders()->latest()->take(5)->get();
        $wishlistCount = $user->wishlists()->count();
        $addressCount = $user->addresses()->count();

        return view('account.dashboard', compact('orders', 'wishlistCount', 'addressCount'));
    }

    public function orders(Request $request)
    {
        $orders = $request->user()->orders()->with(['items', 'payment'])->latest()->paginate(10);

        foreach ($orders as $order) {
            if ($order->status === 'menunggu_pembayaran' && $order->payment?->payment_method === 'midtrans') {
                app(PaymentService::class)->syncWithMidtrans($order->payment);
                $order->refresh();
            }
        }

        return view('account.orders', compact('orders'));
    }

    public function profile()
    {
        return view('account.profile');
    }

    public function updateProfile(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $request->user()->update($data);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if (! password_verify($request->current_password, $request->user()->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini salah.']);
        }

        $request->user()->update(['password' => $request->password]);

        return back()->with('success', 'Password berhasil diubah.');
    }

    public function addresses(Request $request)
    {
        $addresses = $request->user()->addresses()->latest()->get();

        return view('account.addresses', compact('addresses'));
    }

    public function storeAddress(Request $request)
    {
        $data = Validator::make($request->all(), [
            'label' => 'required|string|max:100',
            'recipient_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'province' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'full_address' => 'required|string|max:1000',
        ])->validate();

        $isDefault = $request->user()->addresses()->count() === 0;

        $address = $request->user()->addresses()->create([...$data, 'is_default' => $isDefault]);

        if ($request->boolean('is_default')) {
            $request->user()->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
        }

        return back()->with('success', 'Alamat berhasil ditambahkan.');
    }

    public function setDefaultAddress(Request $request, $id)
    {
        $request->user()->addresses()->where('id', '!=', $id)->update(['is_default' => false]);
        $request->user()->addresses()->where('id', $id)->update(['is_default' => true]);

        return back()->with('success', 'Alamat utama diperbarui.');
    }

    public function destroyAddress(Request $request, $id)
    {
        $request->user()->addresses()->where('id', $id)->delete();

        return back()->with('success', 'Alamat dihapus.');
    }
}
