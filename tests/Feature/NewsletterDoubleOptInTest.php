<?php

namespace Tests\Feature;

use App\Mail\NewsletterConfirmation;
use App\Models\NewsletterSubscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class NewsletterDoubleOptInTest extends TestCase
{
    use RefreshDatabase;

    public function test_subscribing_creates_an_unconfirmed_subscriber(): void
    {
        Mail::fake();

        $this->post(route('newsletter.subscribe'), ['email' => 'nueva@test.com'])->assertRedirect();

        $subscriber = NewsletterSubscriber::where('email', 'nueva@test.com')->first();
        $this->assertNotNull($subscriber);
        $this->assertFalse($subscriber->isConfirmed());
        $this->assertFalse($subscriber->is_active);
        $this->assertNotNull($subscriber->confirmation_token);
    }

    public function test_subscribing_sends_a_confirmation_email_with_a_working_link(): void
    {
        Mail::fake();

        $this->post(route('newsletter.subscribe'), ['email' => 'nueva@test.com']);

        $subscriber = NewsletterSubscriber::where('email', 'nueva@test.com')->first();

        Mail::assertSent(NewsletterConfirmation::class, function ($mail) use ($subscriber) {
            return $mail->subscriber->is($subscriber)
                && str_contains($mail->render(), $subscriber->confirmation_token);
        });
    }

    public function test_subscribing_shows_a_prominent_check_your_email_message(): void
    {
        Mail::fake();

        $response = $this->post(route('newsletter.subscribe'), ['email' => 'nueva@test.com']);

        $response->assertSessionHas('newsletter_pending_confirmation', 'nueva@test.com');

        // El banner "vistoso" vive en el layout global; confirmamos que se
        // renderiza al seguir la redirección.
        $followUp = $this->get(route('home'));
        $followUp->assertSee('Revisá tu correo');
        $followUp->assertSee('nueva@test.com');
        $followUp->assertSee('72 horas');
    }

    public function test_confirming_the_link_activates_the_subscriber(): void
    {
        Mail::fake();
        $this->post(route('newsletter.subscribe'), ['email' => 'nueva@test.com']);
        $subscriber = NewsletterSubscriber::where('email', 'nueva@test.com')->first();

        $response = $this->get(route('newsletter.confirm', $subscriber->confirmation_token));

        $response->assertOk();
        $subscriber->refresh();
        $this->assertTrue($subscriber->isConfirmed());
        $this->assertTrue($subscriber->is_active);
    }

    public function test_an_invalid_confirmation_token_returns_404(): void
    {
        $this->get(route('newsletter.confirm', 'token-que-no-existe'))->assertNotFound();
    }

    public function test_subscribing_again_with_an_already_confirmed_email_does_not_resend_or_reset_it(): void
    {
        Mail::fake();
        $subscriber = NewsletterSubscriber::create([
            'email' => 'confirmada@test.com',
            'is_active' => true,
            'confirmed_at' => now()->subDays(10),
            'subscribed_at' => now()->subDays(10),
        ]);

        $response = $this->post(route('newsletter.subscribe'), ['email' => 'confirmada@test.com']);

        $response->assertSessionHas('success');
        $response->assertSessionMissing('newsletter_pending_confirmation');
        Mail::assertNotSent(NewsletterConfirmation::class);

        $subscriber->refresh();
        $this->assertTrue($subscriber->isConfirmed());
    }

    public function test_subscribing_again_before_confirming_resends_a_fresh_confirmation(): void
    {
        Mail::fake();
        $this->post(route('newsletter.subscribe'), ['email' => 'pendiente@test.com']);
        $first = NewsletterSubscriber::where('email', 'pendiente@test.com')->first();
        $firstToken = $first->confirmation_token;

        $this->post(route('newsletter.subscribe'), ['email' => 'pendiente@test.com']);

        $this->assertSame(1, NewsletterSubscriber::where('email', 'pendiente@test.com')->count());
        $second = NewsletterSubscriber::where('email', 'pendiente@test.com')->first();
        $this->assertNotSame($firstToken, $second->confirmation_token);
        Mail::assertSent(NewsletterConfirmation::class, 2);
    }

    public function test_unconfirmed_subscribers_older_than_72_hours_are_purged(): void
    {
        $expired = NewsletterSubscriber::create([
            'email' => 'vencida@test.com',
            'confirmed_at' => null,
            'subscribed_at' => now()->subHours(80),
        ]);
        $expired->forceFill(['created_at' => now()->subHours(80)])->save();

        $recent = NewsletterSubscriber::create([
            'email' => 'reciente@test.com',
            'confirmed_at' => null,
            'subscribed_at' => now()->subHours(2),
        ]);

        $confirmedOld = NewsletterSubscriber::create([
            'email' => 'confirmada-vieja@test.com',
            'confirmed_at' => now()->subDays(30),
            'subscribed_at' => now()->subDays(30),
        ]);
        $confirmedOld->forceFill(['created_at' => now()->subDays(30)])->save();

        $this->artisan('app:purge-unconfirmed-newsletter-subscribers')->assertExitCode(0);

        $this->assertDatabaseMissing('newsletter_subscribers', ['email' => 'vencida@test.com']);
        $this->assertDatabaseHas('newsletter_subscribers', ['email' => 'reciente@test.com']);
        $this->assertDatabaseHas('newsletter_subscribers', ['email' => 'confirmada-vieja@test.com']);
    }

    public function test_csv_export_only_includes_confirmed_subscribers(): void
    {
        NewsletterSubscriber::create(['email' => 'confirmada@test.com', 'is_active' => true, 'confirmed_at' => now(), 'subscribed_at' => now()]);
        NewsletterSubscriber::create(['email' => 'pendiente@test.com', 'is_active' => false, 'confirmed_at' => null, 'subscribed_at' => now()]);

        $admin = \App\Models\User::factory()->create();
        \App\Models\UserRole::create(['user_id' => $admin->id, 'role' => 'admin']);
        $this->actingAs($admin);

        $response = $this->get(route('admin.newsletter.index', ['export' => 'csv']));
        $content = $response->streamedContent();

        $this->assertStringContainsString('confirmada@test.com', $content);
        $this->assertStringNotContainsString('pendiente@test.com', $content);
    }
}
