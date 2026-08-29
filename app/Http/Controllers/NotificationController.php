<?php

namespace App\Http\Controllers;

use App\Models\DatabaseNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Mark notification as read, delete it, and redirect to the target page.
     */
    public function readAndRedirect(Request $request, string $id): RedirectResponse
    {
        $user = $request->user();
        $notification = $user->notifications()->where('id', $id)->firstOrFail();

        // 1. Resolve destination target URL
        $targetUrl = $this->resolveDestinationUrl($notification, $user);

        // 2. Delete notification so it is cleared/removed
        $notification->delete();

        return redirect()->to($targetUrl);
    }

    /**
     * Mark all unread notifications as read.
     */
    public function markAllAsRead(Request $request): RedirectResponse
    {
        $request->user()->notifications()->whereNull('read_at')->update([
            'read_at' => now(),
        ]);

        return back()->with('status', 'Semua notifikasi telah ditandai dibaca.');
    }

    /**
     * Clear / delete all notifications for the authenticated user.
     */
    public function clearAll(Request $request): RedirectResponse
    {
        $request->user()->notifications()->delete();

        return back()->with('status', 'Semua notifikasi berhasil dibersihkan.');
    }

    /**
     * Delete a specific notification.
     */
    public function destroy(Request $request, string $id): RedirectResponse
    {
        $request->user()->notifications()->where('id', $id)->delete();

        return back()->with('status', 'Notifikasi berhasil dihapus.');
    }

    /**
     * Smart URL resolver based on notification type, data, and user role.
     */
    private function resolveDestinationUrl(DatabaseNotification $notification, $user): string
    {
        $data = $notification->data ?? [];
        $type = strtolower($notification->type ?? '');

        // If explicit url is provided in payload data
        if (! empty($data['url'])) {
            return $data['url'];
        }

        $isAdmin = method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['admin', 'super-admin']);
        $isMitra = method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['mitra-owner', 'mitra-staff']);

        // 1. KYC Notifications
        if (str_contains($type, 'kyc')) {
            return $isAdmin ? route('admin.kyc.index') : route('mitra.kyc.index');
        }

        // 2. Tourism / Wisata
        if (str_contains($type, 'tourism')) {
            if ($isAdmin) {
                if (! empty($data['catalog_entity_id'])) {
                    return route('admin.tourism.show', $data['catalog_entity_id']);
                }
                return route('admin.tourism.index');
            }
            return route('mitra.tourism.index');
        }

        // 3. Accommodation / Penginapan
        if (str_contains($type, 'accommodation')) {
            if ($isAdmin) {
                if (! empty($data['catalog_entity_id'])) {
                    return route('admin.accommodation.show', $data['catalog_entity_id']);
                }
                return route('admin.accommodation.index');
            }
            return route('mitra.accommodation.index');
        }

        // 4. Culinary
        if (str_contains($type, 'culinary')) {
            if ($isAdmin) {
                if (! empty($data['catalog_entity_id'])) {
                    return route('admin.culinary.show', $data['catalog_entity_id']);
                }
                return route('admin.culinary.index');
            }
            return route('mitra.culinary.index');
        }

        // 5. Event
        if (str_contains($type, 'event')) {
            if ($isAdmin) {
                if (! empty($data['catalog_entity_id'])) {
                    return route('admin.event.show', $data['catalog_entity_id']);
                }
                return route('admin.event.index');
            }
            return route('mitra.event.index');
        }

        // 6. Rental
        if (str_contains($type, 'rental')) {
            if ($isAdmin) {
                if (! empty($data['catalog_entity_id'])) {
                    return route('admin.rental.show', $data['catalog_entity_id']);
                }
                return route('admin.rental.index');
            }
            return route('mitra.rental.index');
        }

        // 7. Feature Requests
        if (str_contains($type, 'feature')) {
            return $isAdmin ? route('admin.features.index') : route('mitra.features.index');
        }

        // 8. Withdrawals / Penarikan Saldo
        if (str_contains($type, 'withdrawal')) {
            return $isAdmin ? route('admin.withdrawals.index') : route('mitra.withdrawals.index');
        }

        // 9. Bank Accounts
        if (str_contains($type, 'bank')) {
            return $isAdmin ? route('admin.mitras.index') : route('mitra.bank-accounts.index');
        }

        // 10. Orders / Payments
        if (str_contains($type, 'order') || str_contains($type, 'payment') || str_contains($type, 'ticket')) {
            if ($isMitra) {
                return route('mitra.orders.index');
            }
            if ($isAdmin) {
                return route('admin.dashboard');
            }
            return route('consumer.orders.index');
        }

        // Default Fallback
        return route('post-login');
    }
}
