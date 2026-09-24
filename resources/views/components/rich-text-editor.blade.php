{{--
    The one rich-text editor (TipTap) used for Task Description and for every
    Comment box, new or edit. Blade only emits a placeholder root plus, when
    $name is given, a hidden input that the editor keeps in sync — resources/
    js/rich-text-editor.js builds the toolbar and editing surface into it once
    the element is on screen (see app.js). Later media phases change that one
    module, not the pages that use this component.

    $value is whatever is stored: legacy plain text, editor HTML, or null.
    RichText::toHtml() turns any of them into valid, sanitized HTML for the
    editor to load — that conversion is what keeps every pre-editor
    description/comment readable. The hidden input starts with the same
    converted value, so a form still submits the right content even if the
    editor script never loads.

    $mentions: [['id' => .., 'name' => ..], ...] enables @mention
    autocomplete (comments). $compact: shorter surface for inline use.

    Image upload target (resources/js/rich-text-editor.js reads these fresh
    on every click of the toolbar's image button — see buildImageUpload()):
      - $imageTaskId + $imageContext ('description'|'comment'): an existing
        task — Edit Task's Description, or any comment box. POSTs to
        /tasks/{id}/images.
      - $imagePendingId (+ optional initial $imageProjectId/
        $imageDepartmentId): the Add Task page, before the task exists.
        POSTs to /pending-task-images. That page keeps the project/
        department data attributes updated itself as its selects change —
        see tasks/create.blade.php.
      Neither pair given: the image button hides itself.

    Audio upload target (task #4 phase 4) — same shape as image, same two
    targets: $audioTaskId + $audioContext (POSTs to /tasks/{id}/audio),
    $audioPendingId (+ optional initial $audioProjectId/$audioDepartmentId,
    POSTs to /pending-task-audio). tasks/create.blade.php passes the SAME
    pendingMediaId as both :image-pending-id and :audio-pending-id — one id
    covers every category the Add Task page's editor uploads, since
    FileStorageService::reconcilePendingFiles() moves everything a
    description references in one generic pass on save, not per category.

    Video upload target (task #4 phase 5) — same shape again:
    $videoTaskId + $videoContext (POSTs to /tasks/{id}/video),
    $videoPendingId (+ optional initial $videoProjectId/$videoDepartmentId,
    POSTs to /pending-task-video), the SAME pendingMediaId as image/audio.

    Document upload target (task #4, document upload + embedding) — same
    shape again: $documentTaskId + $documentContext (POSTs to
    /tasks/{id}/document-uploads), $documentPendingId (+ optional initial
    $documentProjectId/$documentDepartmentId, POSTs to
    /pending-task-document-uploads), the SAME pendingMediaId as
    image/audio/video. Unlike those three, attaching a document is NOT an
    inline preview/embed — it inserts a small clickable file-chip and
    (existing task only; see RichTextDocumentController) attaches a real
    Document to the task via task_documents, the same record the
    Documents page/tab and the "attach an existing document" picker below
    the editor both already use.

    Link-preview target (task #4, Smart Links) — same $taskId/$context
    (POSTs to /tasks/{id}/link-previews) shape as the others, but no
    pending-id: $linkPreviewProjectId alone (+ optional initial
    $linkPreviewDepartmentId, POSTs to /pending-task-link-previews) is
    enough to wire the Add Task page, since nothing here is ever stored
    under a task-scoped path (see LinkPreviewService) the way a pending
    file upload needs one. There's no toolbar button for this one at all
    — see buildLinkPreview() in rich-text-editor.js — it's triggered by
    pasting a bare URL alone on its own line, not a click.
--}}
@props([
    'name' => null,
    'id' => null,
    'value' => null,
    'placeholder' => '',
    'label' => 'Rich text',
    'compact' => false,
    'mentions' => null,
    'imageTaskId' => null,
    'imageContext' => null,
    'imagePendingId' => null,
    'imageProjectId' => null,
    'imageDepartmentId' => null,
    'audioTaskId' => null,
    'audioContext' => null,
    'audioPendingId' => null,
    'audioProjectId' => null,
    'audioDepartmentId' => null,
    'videoTaskId' => null,
    'videoContext' => null,
    'videoPendingId' => null,
    'videoProjectId' => null,
    'videoDepartmentId' => null,
    'documentTaskId' => null,
    'documentContext' => null,
    'documentPendingId' => null,
    'documentProjectId' => null,
    'documentDepartmentId' => null,
    'linkPreviewTaskId' => null,
    'linkPreviewContext' => null,
    'linkPreviewProjectId' => null,
    'linkPreviewDepartmentId' => null,
])

@php $html = \App\Support\RichText::toHtml($value); @endphp

<div
    {{ $attributes->class(['rte', 'rte-loading']) }}
    data-rich-text
    data-content="{{ $html }}"
    data-placeholder="{{ $placeholder }}"
    data-label="{{ $label }}"
    @if ($compact) data-compact @endif
    @if ($mentions) data-mentions="{{ json_encode($mentions) }}" @endif
    @if ($imageTaskId) data-image-task-id="{{ $imageTaskId }}" data-image-context="{{ $imageContext }}" @endif
    @if ($imagePendingId) data-image-pending-id="{{ $imagePendingId }}" data-image-project-id="{{ $imageProjectId }}" data-image-department-id="{{ $imageDepartmentId }}" @endif
    @if ($audioTaskId) data-audio-task-id="{{ $audioTaskId }}" data-audio-context="{{ $audioContext }}" @endif
    @if ($audioPendingId) data-audio-pending-id="{{ $audioPendingId }}" data-audio-project-id="{{ $audioProjectId }}" data-audio-department-id="{{ $audioDepartmentId }}" @endif
    @if ($videoTaskId) data-video-task-id="{{ $videoTaskId }}" data-video-context="{{ $videoContext }}" @endif
    @if ($videoPendingId) data-video-pending-id="{{ $videoPendingId }}" data-video-project-id="{{ $videoProjectId }}" data-video-department-id="{{ $videoDepartmentId }}" @endif
    @if ($documentTaskId) data-document-task-id="{{ $documentTaskId }}" data-document-context="{{ $documentContext }}" @endif
    @if ($documentPendingId) data-document-pending-id="{{ $documentPendingId }}" data-document-project-id="{{ $documentProjectId }}" data-document-department-id="{{ $documentDepartmentId }}" @endif
    @if ($linkPreviewTaskId) data-link-preview-task-id="{{ $linkPreviewTaskId }}" data-link-preview-context="{{ $linkPreviewContext }}" @endif
    @if ($linkPreviewProjectId) data-link-preview-project-id="{{ $linkPreviewProjectId }}" data-link-preview-department-id="{{ $linkPreviewDepartmentId }}" @endif
>
    @if ($name)
        <input type="hidden" name="{{ $name }}" @if ($id) id="{{ $id }}" @endif value="{{ $html }}" data-rte-input>
    @endif
</div>
