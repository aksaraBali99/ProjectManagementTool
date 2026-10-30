<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Organization;
use DateTimeInterface;

/**
 * task #72 phase 7: the "Show all" mode shared by Kanban and Projects —
 * ?completed=all shows every completed task for this visit, otherwise the
 * company's window applies. Never saved: a new visit starts with the
 * window again.
 *
 * The Tasks list has its own Completed FILTER (Recent / All / Date range,
 * plus the Status = Completed rule), so it doesn't use this.
 */
trait AppliesCompletedWindow
{
    protected function showsAllCompleted(): bool
    {
        return request()->query('completed') === 'all';
    }

    /** Where the window starts for this request, or null under "Show all". */
    protected function completedWindowStartFor(Organization $organization): ?DateTimeInterface
    {
        return $this->showsAllCompleted() ? null : $organization->completedWindowStart();
    }
}
