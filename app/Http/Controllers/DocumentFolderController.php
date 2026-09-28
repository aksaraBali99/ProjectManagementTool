<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Document;
use App\Models\DocumentFolder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

/**
 * task #73 phase 2: folders on the Documents page. Folders have no
 * visibility of their own (see DocumentFolder's own docblock) — there is
 * no index/show here; the folder listing lives inside
 * DocumentController::index() alongside the documents in that same
 * folder, since the two are rendered together as one page.
 */
class DocumentFolderController extends Controller
{
    /**
     * organization_id comes from the request body, but is never trusted
     * on its own — Gate::authorize below re-derives whether this specific
     * user can actually manage_documents in that specific company, the
     * same pattern DocumentController::store() already established in
     * Phase 1 (a tampered value fails authorization, it doesn't silently
     * create the folder somewhere else). parent_id, if given, is looked
     * up scoped to that same organization_id — a parent from a different
     * company simply doesn't resolve, so this can never nest a folder
     * under another company's tree.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'organization_id' => ['required', 'integer', 'exists:organizations,id'],
            'parent_id' => ['nullable', 'integer'],
            'name' => ['required', 'string', 'max:255'],
        ], [], [
            'organization_id' => 'company',
        ]);

        Gate::authorize('create', [DocumentFolder::class, $data['organization_id']]);

        $parent = null;
        if (! empty($data['parent_id'])) {
            $parent = DocumentFolder::where('organization_id', $data['organization_id'])->find($data['parent_id']);
            abort_if($parent === null, 404);
        }

        $name = trim($data['name']);
        if ($name === '') {
            return response()->json(['message' => 'The name field is required.', 'errors' => ['name' => ['The name field is required.']]], 422);
        }

        if ($this->siblingNameTaken($data['organization_id'], $parent?->id, $name)) {
            return $this->duplicateNameResponse();
        }

        $folder = DocumentFolder::create([
            'organization_id' => $data['organization_id'],
            'parent_id' => $parent?->id,
            'name' => $name,
            'created_by' => auth()->id(),
        ]);

        AuditLog::create([
            'organization_id' => $folder->organization_id,
            'user_id' => auth()->id(),
            'action' => 'folder.created',
            'entity_type' => 'document_folder',
            'entity_id' => $folder->id,
            'changes' => ['name' => $folder->name, 'parent_id' => $folder->parent_id],
        ]);

        return response()->json(['folder' => $folder->load('creator')], 201);
    }

    public function update(Request $request, DocumentFolder $folder): JsonResponse
    {
        Gate::authorize('update', $folder);

        $data = $request->validate(['name' => ['required', 'string', 'max:255']]);

        $name = trim($data['name']);
        if ($name === '') {
            return response()->json(['message' => 'The name field is required.', 'errors' => ['name' => ['The name field is required.']]], 422);
        }

        if ($name !== $folder->name && $this->siblingNameTaken($folder->organization_id, $folder->parent_id, $name, excludeId: $folder->id)) {
            return $this->duplicateNameResponse();
        }

        $oldName = $folder->name;
        $folder->update(['name' => $name]);

        if ($oldName !== $name) {
            AuditLog::create([
                'organization_id' => $folder->organization_id,
                'user_id' => auth()->id(),
                'action' => 'folder.renamed',
                'entity_type' => 'document_folder',
                'entity_id' => $folder->id,
                'changes' => ['name' => ['old' => $oldName, 'new' => $name]],
            ]);
        }

        return response()->json(['folder' => $folder]);
    }

    /**
     * "Delete only when empty: no child folders and no documents,
     * counting documents the viewer can't see." — both checks are
     * intentionally unfiltered by the viewer's own visibility (a bare
     * ->exists(), not routed through DocumentPolicy::view()), and the
     * error names no counts or contents, so a deleter never learns
     * anything about what they can't already see just by trying to
     * delete the folder holding it.
     */
    public function destroy(DocumentFolder $folder): JsonResponse
    {
        Gate::authorize('delete', $folder);

        $hasChildren = DocumentFolder::where('parent_id', $folder->id)->exists();
        $hasDocuments = Document::where('folder_id', $folder->id)->exists();

        if ($hasChildren || $hasDocuments) {
            return response()->json(['message' => "This folder isn't empty."], 422);
        }

        $name = $folder->name;
        $organizationId = $folder->organization_id;
        $folderId = $folder->id;

        $folder->delete();

        AuditLog::create([
            'organization_id' => $organizationId,
            'user_id' => auth()->id(),
            'action' => 'folder.deleted',
            'entity_type' => 'document_folder',
            'entity_id' => $folderId,
            'changes' => ['name' => $name],
        ]);

        return response()->json(['deleted' => true]);
    }

    /**
     * "unique among siblings (same company and parent), case-insensitive.
     * MySQL unique indexes don't enforce this when parent_id is null, so
     * validate in the app." — LOWER() comparison works identically on
     * both MySQL and SQLite (Pest), so this needs no driver branching.
     */
    private function siblingNameTaken(int $organizationId, ?int $parentId, string $name, ?int $excludeId = null): bool
    {
        return DocumentFolder::where('organization_id', $organizationId)
            ->where('parent_id', $parentId)
            ->when($excludeId !== null, fn ($query) => $query->where('id', '!=', $excludeId))
            ->whereRaw('LOWER(name) = ?', [Str::lower($name)])
            ->exists();
    }

    private function duplicateNameResponse(): JsonResponse
    {
        $message = 'A folder with this name already exists here.';

        return response()->json(['message' => $message, 'errors' => ['name' => [$message]]], 422);
    }
}
