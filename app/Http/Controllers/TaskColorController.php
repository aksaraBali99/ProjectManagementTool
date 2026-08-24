<?php

namespace App\Http\Controllers;

use App\Enums\TaskStatus;
use App\Http\Requests\TaskColors\UpdateTaskStatusColorsRequest;
use App\Models\TaskStatusColor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class TaskColorController extends Controller
{
    public function edit(): View
    {
        Gate::authorize('task-colors.view');

        $statusColors = collect(TaskStatus::cases())->map(fn (TaskStatus $status) => [
            'value' => $status->value,
            'label' => $status->label(),
            'background_color' => $status->badgeBackground(),
            'text_color' => $status->badgeText(),
        ]);

        return view('task-colors.edit', ['statusColors' => $statusColors]);
    }

    public function updateStatus(UpdateTaskStatusColorsRequest $request): RedirectResponse
    {
        Gate::authorize('task-colors.view');

        $colors = $request->validated()['colors'];

        DB::transaction(function () use ($colors) {
            foreach ($colors as $status => $pair) {
                TaskStatusColor::where('status', $status)->update([
                    'background_color' => $pair['background_color'],
                    'text_color' => $pair['text_color'],
                ]);
            }
        });

        TaskStatus::forgetColorCache();

        return redirect()->route('task-colors.edit')->with('status', 'Status colors updated.');
    }
}
