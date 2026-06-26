<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NewsletterAdminController extends Controller
{
    public function index(Request $request)
    {
        if ($request->query('export') === 'csv') {
            return $this->exportCsv();
        }
        $subscribers = NewsletterSubscriber::latest()->paginate(50);
        return view('admin.newsletter.index', compact('subscribers'));
    }

    private function exportCsv(): StreamedResponse
    {
        $subscribers = NewsletterSubscriber::where('is_active', true)->get();
        return response()->streamDownload(function () use ($subscribers) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Email', 'Fecha de suscripción']);
            foreach ($subscribers as $sub) {
                fputcsv($handle, [$sub->email, $sub->created_at->format('Y-m-d')]);
            }
            fclose($handle);
        }, 'newsletter_' . now()->format('Ymd') . '.csv');
    }
}
