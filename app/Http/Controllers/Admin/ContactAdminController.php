<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactAdminController extends Controller
{
    public function index()
    {
        $messages = ContactMessage::latest()->paginate(25);
        return view('admin.contacts.index', compact('messages'));
    }

    public function show(ContactMessage $message)
    {
        $message->update(['read_at' => now()]);
        return view('admin.contacts.show', compact('message'));
    }

    public function destroy(ContactMessage $message)
    {
        $message->delete();
        return back()->with('success', 'Mensaje eliminado.');
    }
}
