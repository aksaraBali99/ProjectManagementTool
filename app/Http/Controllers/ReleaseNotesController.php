<?php

namespace App\Http\Controllers;

use App\Support\ReleaseNotes;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/**
 * task #124: Settings > Release notes, owner and super_admin only
 * ('release-notes.view'). Shows resources/release-notes.json as it is in
 * the code this server runs.
 */
class ReleaseNotesController extends Controller
{
    public function index(ReleaseNotes $notes): View
    {
        Gate::authorize('release-notes.view');

        return view('release-notes.index', [
            'releases' => $notes->releases(),
            'error' => $notes->error(),
            'types' => ReleaseNotes::TYPES,
        ]);
    }
}
