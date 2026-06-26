<?php
namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        return view('contact');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'required|email|max:100',
            'phone'   => 'nullable|string|max:30',
            'subject' => 'nullable|string|max:200',
            'message' => 'required|string|min:10|max:2000',
        ]);

        ContactMessage::create($request->only('name', 'email', 'phone', 'subject', 'message'));

        return back()->with('success', '¡Mensaje enviado con éxito! Te responderemos a la brevedad.');
    }

    public function newsletter(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:100',
            'name'  => 'nullable|string|max:100',
        ]);

        NewsletterSubscriber::firstOrCreate(
            ['email' => $request->email],
            ['name' => $request->name, 'is_active' => true, 'subscribed_at' => now()]
        );

        return back()->with('success', '¡Te suscribiste al newsletter de Cateura Accesorios!');
    }
}
