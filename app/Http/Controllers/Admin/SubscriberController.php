<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SubscriberController extends Controller
{
    public function index(Request $request): View
    {
        $query = NewsletterSubscriber::query()->latest('subscribed_at');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where('email', 'like', "%{$search}%");
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $subscribers = $query->paginate(20)->withQueryString();
        $totalSubscribers = NewsletterSubscriber::count();
        $activeSubscribers = NewsletterSubscriber::where('is_active', true)->count();

        return view('admin.subscribers.index', compact('subscribers', 'totalSubscribers', 'activeSubscribers'));
    }

    public function exportCsv(): StreamedResponse
    {
        $fileName = 'subscribers_'.date('Y-m-d_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');

            // Add UTF-8 BOM for Excel compatibility
            fwrite($handle, "\xEF\xBB\xBF");

            // CSV Column Headers
            fputcsv($handle, ['ID', 'Email', 'Tanggal Berlangganan', 'Status']);

            NewsletterSubscriber::orderBy('subscribed_at', 'desc')->chunk(500, function ($subscribers) use ($handle) {
                foreach ($subscribers as $sub) {
                    fputcsv($handle, [
                        $sub->id,
                        $sub->email,
                        $sub->subscribed_at?->format('Y-m-d H:i:s') ?? '',
                        $sub->is_active ? 'Aktif' : 'Nonaktif',
                    ]);
                }
            });

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function destroy(NewsletterSubscriber $subscriber): RedirectResponse
    {
        $email = $subscriber->email;
        $subscriber->delete();

        return redirect()->route('admin.subscribers.index')
            ->with('success', "Subscriber '{$email}' berhasil dihapus.");
    }
}
