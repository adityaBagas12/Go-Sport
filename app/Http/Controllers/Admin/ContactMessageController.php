<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'all');
        $search = $request->query('search');

        $query = ContactMessage::query();

        if ($status === 'unread') {
            $query->where('is_read', false);
        } elseif ($status === 'read') {
            $query->where('is_read', true);
        }

        if ($search) {
            $query->where(function ($sub) use ($search) {
                $sub->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%");
            });
        }

        $messages = $query->latest()->paginate(15)->withQueryString();
        $unreadCount = ContactMessage::unread()->count();
        $totalCount = ContactMessage::count();

        return view('admin.messages.index', compact('messages', 'unreadCount', 'totalCount', 'status', 'search'));
    }

    public function show(ContactMessage $message)
    {
        $message->markAsRead();
        $message->load(['user', 'repliedByUser']);

        return view('admin.messages.show', [
            'contactMessage' => $message,
            'message' => $message,
        ]);
    }

    public function reply(Request $request, ContactMessage $message)
    {
        $validated = $request->validate([
            'reply' => ['required', 'string', 'max:5000'],
        ]);

        $message->markAsReplied($validated['reply'], $request->user()->id);

        return back()->with('success', 'Balasan berhasil dikirim! Customer dapat langsung membaca jawaban ini di akun mereka.');
    }

    public function destroy(ContactMessage $message)
    {
        $message->delete();

        return redirect()->route('admin.messages.index')->with('success', 'Pesan kontak berhasil dihapus.');
    }
}
