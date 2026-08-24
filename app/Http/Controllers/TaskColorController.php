<?php

namespace App\Http\Controllers;

use App\Enums\Priority;
use App\Enums\TaskStatus;
use App\Http\Requests\TaskColors\UpdateTaskPriorityColorsRequest;
use App\Http\Requests\TaskColors\UpdateTaskStatusColorsRequest;
use App\Models\TaskPriorityColor;
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

        $priorityColors = collect(Priority::cases())->map(fn (Priority $priority) => [
            'value' => $priority->value,
            'label' => $priority->label(),
            'background_color' => $priority->badgeBackground(),
            'text_color' => $priority->badgeText(),
        ]);

        return view('task-colors.edit', [
            'statusColors' => $statusColors,
            'priorityColors' => $priorityColors,
        ]);
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

    public function updatePriority(UpdateTaskPriorityColorsRequest $request): RedirectResponse
    {
        Gate::authorize('task-colors.view');

        $colors = $request->validated()['priority_colors'];

        DB::transaction(function () use ($colors) {
            foreach ($colors as $priority => $pair) {
                TaskPriorityColor::where('priority', $priority)->update([
                    'background_color' => $pair['background_color'],
                    'text_color' => $pair['text_color'],
                ]);
            }
        });

        Priority::forgetColorCache();

        return redirect()->route('task-colors.edit')->with('status', 'Priority colors updated.');
    }
}
