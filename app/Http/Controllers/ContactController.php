<?php
namespace App\Http\Controllers;

use App\Mail\NewsletterConfirmation;
use App\Models\ContactMessage;
use App\Models\NewsletterSubscriber;
use App\Services\HCaptchaVerifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

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

        if (! $this->passesHCaptcha($request, 'contact')) {
            return back()->withInput()->with('error', 'No pudimos verificar que sos una persona. Intentá de nuevo.');
        }

        ContactMessage::create($request->only('name', 'email', 'phone', 'subject', 'message'));

        return back()->with('success', '¡Mensaje enviado con éxito! Te responderemos a la brevedad.');
    }

    public function newsletter(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:100',
            'name'  => 'nullable|string|max:100',
        ]);

        if (! $this->passesHCaptcha($request, 'newsletter')) {
            return back()->with('error', 'No pudimos verificar que sos una persona. Intentá de nuevo.');
        }

        $existing = NewsletterSubscriber::where('email', $request->email)->first();

        if ($existing && $existing->isConfirmed()) {
            return back()->with('success', 'Ese correo ya está suscripto a nuestro newsletter. ¡Gracias!');
        }

        $subscriber = NewsletterSubscriber::updateOrCreate(
            ['email' => $request->email],
            [
                'name'                => $request->name,
                'is_active'           => false,
                'confirmed_at'        => null,
                'confirmation_token'  => Str::random(64),
                'subscribed_at'       => now(),
            ]
        );

        try {
            Mail::to($subscriber->email)->send(new NewsletterConfirmation($subscriber));
        } catch (\Throwable $e) {
            Log::error('No se pudo enviar el correo de confirmación de newsletter.', [
                'subscriber_id' => $subscriber->id,
                'error'         => $e->getMessage(),
            ]);
            return back()->with('error', 'No pudimos enviar el correo de confirmación. Probá de nuevo en unos minutos.');
        }

        return back()->with('newsletter_pending_confirmation', $subscriber->email);
    }

    /**
     * Confirma la suscripción cuando el suscriptor toca el enlace de su correo.
     * Si el token venció (más de 72hs) el registro ya fue purgado por el
     * comando programado, así que llegar acá con un token viejo da 404.
     */
    public function confirmNewsletter(string $token)
    {
        $subscriber = NewsletterSubscriber::where('confirmation_token', $token)->firstOrFail();

        if (! $subscriber->isConfirmed()) {
            $subscriber->update(['confirmed_at' => now(), 'is_active' => true]);
        }

        return view('newsletter.confirmed', compact('subscriber'));
    }

    private function passesHCaptcha(Request $request, string $form): bool
    {
        if (! HCaptchaVerifier::isEnabledFor($form)) {
            return true;
        }

        return HCaptchaVerifier::verify($request->input('h-captcha-response'));
    }
}
