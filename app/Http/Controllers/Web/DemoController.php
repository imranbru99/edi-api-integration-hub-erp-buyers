<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\EdiTransaction;
use App\Models\Tenant;
use App\Models\User;
use App\Services\Demo\DemoResetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class DemoController extends Controller
{
    public function index(): View
    {
        return view('public.demo.index', [
            'title' => 'Interactive Demo',
            'demoEnabled' => (bool) config('edi-hub.demo.enabled'),
        ]);
    }

    public function showLogin(): View
    {
        return view('public.demo.login', ['title' => 'Demo Sign In']);
    }

    public function login(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $allowed = in_array($data['email'], array_values(config('edi-hub.demo.emails')), true);
        $user = User::query()->where('email', $data['email'])->first();
        $passwordOk = $user && Hash::check($data['password'], $user->password);

        if (! $allowed || ! $user || ! $passwordOk) {
            return back()->withErrors(['email' => 'Use the demo credentials from your .env file.'])->onlyInput('email');
        }

        Auth::login($user);
        $request->session()->put('demo_mode', true);

        return redirect()->route('demo.transactions');
    }

    public function transactions(Request $request): View
    {
        $tenant = Tenant::query()->where('slug', 'global-apparel-manufacturing-ltd')->firstOrFail();

        $transactions = EdiTransaction::withoutGlobalScopes()
            ->where('tenant_id', $tenant->id)
            ->with('tradingPartner')
            ->latest()
            ->paginate(20);

        return view('public.demo.transactions', compact('transactions', 'tenant'));
    }

    public function showTransaction(EdiTransaction $transaction): View
    {
        $transaction = EdiTransaction::withoutGlobalScopes()
            ->with(['tradingPartner', 'events', 'purchaseOrder'])
            ->findOrFail($transaction->id);

        return view('public.demo.transaction-show', ['transaction' => $transaction]);
    }

    public function inspect(Request $request, EdiTransaction $transaction, string $layer): View
    {
        $transaction = EdiTransaction::withoutGlobalScopes()
            ->findOrFail($transaction->id);

        abort_unless(in_array($layer, ['raw', 'parsed', 'canonical'], true), 404);

        return view('public.demo.inspect', compact('transaction', 'layer'));
    }

    public function simulateDelivery(EdiTransaction $transaction): RedirectResponse
    {
        $transaction = EdiTransaction::withoutGlobalScopes()
            ->findOrFail($transaction->id);

        $transaction->update([
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);

        $transaction->events()->create([
            'event' => 'delivered',
            'level' => 'info',
            'message' => 'Simulated ERP delivery (demo)',
            'context' => ['demo' => true],
        ]);

        return back()->with('success', 'Delivery simulation recorded.');
    }

    public function timeline(EdiTransaction $transaction): View
    {
        $transaction = EdiTransaction::withoutGlobalScopes()
            ->with('events')
            ->findOrFail($transaction->id);

        return view('public.demo.timeline', compact('transaction'));
    }

    public function reset(DemoResetService $reset): RedirectResponse
    {
        try {
            $reset->reset();
        } catch (\RuntimeException $e) {
            return back()->withErrors(['reset' => $e->getMessage()]);
        }

        return redirect()->route('demo.index')->with('success', 'Demo tenant has been reset.');
    }
}
