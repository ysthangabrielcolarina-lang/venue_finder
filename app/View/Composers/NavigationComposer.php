<?php

namespace App\View\Composers;

use App\Models\User;
use Illuminate\View\View;

// Builds the data for the shared menu (partials/role-nav) so the Blade file only displays it.
class NavigationComposer
{
    // Top bar color for each role.
    private const BARS = [
        'customer' => 'bg-blue-600',
        'owner' => 'bg-emerald-600',
        'admin' => 'bg-purple-600',
    ];

    public function compose(View $view): void
    {
        $user = auth()->user();

        $view->with([
            'user' => $user,
            'current' => $user
                ? ['label' => $user->roleLabel(), 'bar' => self::BARS[$user->role] ?? 'bg-gray-600']
                : null,
            'buttons' => $this->buttons($user),
            'action' => $this->action($user),
        ]);
    }

    // The row of buttons under the menu bar. Logged-out visitors get none.
    private function buttons(?User $user): array
    {
        if (! $user) {
            return [];
        }

        // Each item: [label, address, route names that mean "you are here"]
        $items = [
            ['Dashboard', route('dashboard'), ['dashboard']],
            ['Venues', route('venues.index'), ['venues.*']],
        ];

        if ($user->isCustomer()) {
            $items[] = ['My reservations', route('reservations.index'), ['reservations.*']];
        } elseif ($user->isStaff()) {
            $items[] = $user->isOwner()
                ? ['Owner area', route('owner.index'), ['owner.*']]
                : ['Admin area', route('admin.index'), ['admin.*']];
            $items[] = ['Reservations', route('reservations.index'), ['reservations.*']];
        }

        return array_map(fn ($item) => [
            'label' => $item[0],
            'url' => $item[1],
            'active' => request()->routeIs(...$item[2]),
        ], $items);
    }

    // The one main (green) button for each role.
    private function action(?User $user): ?array
    {
        if (! $user) {
            return null;
        }

        if ($user->isCustomer()) {
            return ['label' => 'Reserve a venue', 'url' => route('venues.index')];
        }

        if ($user->isStaff()) {
            return ['label' => '+ Add venue', 'url' => route('venues.create')];
        }

        return null;
    }
}
