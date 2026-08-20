{{-- Self-contained per inclusion (document.currentScript, not a global id),
     matches the pattern used by tasks/_subtasks.blade.php since this also
     renders once per drilldown row on the task list. --}}
<div class="comment-container" data-task-id="{{ $task->id }}">
    <div class="comment-list space-y-2">
        @forelse ($task->comments as $comment)
            @php $canEditComment = auth()->user()->can('update', $comment); @endphp
            <div class="rounded-[8px] bg-white border border-gray-200 px-3 py-2" data-comment-id="{{ $comment->id }}" data-can-edit="{{ $canEditComment ? '1' : '0' }}">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-medium text-[#1F2937]">{{ $comment->user->name }}</span>
                    <span class="text-[10px] text-gray-400">{{ $comment->created_at->format('M j, Y g:ia') }}</span>
                </div>
                <p class="comment-body-text mt-1 text-[12px] text-gray-700">{{ $comment->body }}</p>
                @if ($canEditComment)
                    <div class="comment-actions mt-1 flex items-center gap-2">
                        <button type="button" class="edit-comment-btn text-[10px] text-[#1D9E75] hover:underline">Edit</button>
                        <button type="button" class="delete-comment-btn text-[10px] text-gray-500 hover:underline">Delete</button>
                    </div>
                @endif
            </div>
        @empty
            <p class="comment-empty text-[11px] text-gray-500">No comments yet.</p>
        @endforelse
    </div>

    <div class="mt-2 flex items-start gap-2">
        <textarea class="new-comment-body flex-1 rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]" rows="2" placeholder="Add a comment…"></textarea>
        <button type="button" class="post-comment-btn rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] font-medium text-gray-700 hover:bg-gray-50">
            Post
        </button>
    </div>
    <p class="comment-error mt-1 text-[11px] text-red-600" style="display: none;"></p>
</div>
<script>
    (function () {
        const container = document.currentScript.previousElementSibling;
        const taskId = container.dataset.taskId;
        const listEl = container.querySelector('.comment-list');
        const bodyInput = container.querySelector('.new-comment-body');
        const postBtn = container.querySelector('.post-comment-btn');
        const errorEl = container.querySelector('.comment-error');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        function escapeHtml(value) {
            const div = document.createElement('div');
            div.textContent = value;
            return div.innerHTML;
        }

        function request(url, method, body) {
            return fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: body ? JSON.stringify(body) : undefined,
            });
        }

        function buildCommentRow(comment) {
            const row = document.createElement('div');
            row.className = 'rounded-[8px] bg-white border border-gray-200 px-3 py-2';
            row.dataset.commentId = comment.id;
            row.dataset.canEdit = comment.can_edit ? '1' : '0';

            const actionsHtml = comment.can_edit
                ? '<div class="comment-actions mt-1 flex items-center gap-2">'
                    + '<button type="button" class="edit-comment-btn text-[10px] text-[#1D9E75] hover:underline">Edit</button>'
                    + '<button type="button" class="delete-comment-btn text-[10px] text-gray-500 hover:underline">Delete</button>'
                    + '</div>'
                : '';

            row.innerHTML = '<div class="flex items-center justify-between">'
                + '<span class="text-[11px] font-medium text-[#1F2937]">' + escapeHtml(comment.user_name) + '</span>'
                + '<span class="text-[10px] text-gray-400">' + escapeHtml(comment.created_at) + '</span>'
                + '</div>'
                + '<p class="comment-body-text mt-1 text-[12px] text-gray-700">' + escapeHtml(comment.body) + '</p>'
                + actionsHtml;

            return row;
        }

        function removeEmptyState() {
            const empty = listEl.querySelector('.comment-empty');
            if (empty) empty.remove();
        }

        function showEmptyStateIfNeeded() {
            if (! listEl.querySelector('[data-comment-id]') && ! listEl.querySelector('.comment-empty')) {
                const empty = document.createElement('p');
                empty.className = 'comment-empty text-[11px] text-gray-500';
                empty.textContent = 'No comments yet.';
                listEl.appendChild(empty);
            }
        }

        function wireComment(row) {
            const editBtn = row.querySelector('.edit-comment-btn');
            const deleteBtn = row.querySelector('.delete-comment-btn');
            const bodyText = row.querySelector('.comment-body-text');
            if (! editBtn && ! deleteBtn) return;

            if (editBtn) {
                editBtn.addEventListener('click', function () {
                    const currentBody = bodyText.textContent;
                    const textarea = document.createElement('textarea');
                    textarea.className = 'comment-edit-textarea mt-1 w-full rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]';
                    textarea.rows = 2;
                    textarea.value = currentBody;
                    bodyText.replaceWith(textarea);
                    row.querySelector('.comment-actions').style.display = 'none';

                    const controls = document.createElement('div');
                    controls.className = 'comment-edit-controls mt-1 flex items-center gap-2';
                    controls.innerHTML = '<button type="button" class="save-comment-btn text-[10px] font-medium text-[#1D9E75] hover:underline">Save</button>'
                        + '<button type="button" class="cancel-comment-btn text-[10px] text-gray-500 hover:underline">Cancel</button>';
                    textarea.insertAdjacentElement('afterend', controls);
                    textarea.focus();

                    function restore(newBody) {
                        const newBodyText = document.createElement('p');
                        newBodyText.className = 'comment-body-text mt-1 text-[12px] text-gray-700';
                        newBodyText.textContent = newBody;
                        textarea.replaceWith(newBodyText);
                        controls.remove();
                        row.querySelector('.comment-actions').style.display = '';
                        wireComment(row);
                    }

                    controls.querySelector('.cancel-comment-btn').addEventListener('click', function () {
                        restore(currentBody);
                    });

                    controls.querySelector('.save-comment-btn').addEventListener('click', function () {
                        const newBody = textarea.value.trim();
                        if (newBody === '') return;

                        request('/comments/' + row.dataset.commentId, 'PUT', { body: newBody })
                            .then(function (response) {
                                if (! response.ok) throw new Error();
                                return response.json();
                            })
                            .then(function (data) {
                                restore(data.comment.body);
                            })
                            .catch(function () {
                                alert('Failed to save comment.');
                            });
                    });
                });
            }

            if (deleteBtn) {
                deleteBtn.addEventListener('click', function () {
                    request('/comments/' + row.dataset.commentId, 'DELETE')
                        .then(function (response) {
                            if (! response.ok) throw new Error();
                            row.remove();
                            showEmptyStateIfNeeded();
                        })
                        .catch(function () {
                            alert('Failed to delete comment.');
                        });
                });
            }
        }

        listEl.querySelectorAll('[data-comment-id]').forEach(wireComment);

        postBtn.addEventListener('click', function () {
            const body = bodyInput.value.trim();
            if (body === '') return;

            errorEl.style.display = 'none';
            postBtn.disabled = true;

            request('/tasks/' + taskId + '/comments', 'POST', { body: body })
                .then(function (response) {
                    if (! response.ok) throw new Error();
                    return response.json();
                })
                .then(function (data) {
                    removeEmptyState();

                    const item = buildCommentRow(Object.assign({}, data.comment, { can_edit: true }));
                    listEl.appendChild(item);
                    wireComment(item);
                    bodyInput.value = '';
                })
                .catch(function () {
                    errorEl.textContent = 'Failed to post comment.';
                    errorEl.style.display = '';
                })
                .finally(function () {
                    postBtn.disabled = false;
                });
        });

        // Scoped to this task's own comment list, not a full-page refetch —
        // and only while the container is actually visible, so collapsed
        // drilldown rows on the Task List page don't poll in the background.
        function syncComments() {
            if (container.offsetParent === null) return;

            fetch('/tasks/' + taskId + '/comments', { headers: { Accept: 'application/json' } })
                .then(function (response) {
                    if (! response.ok) throw new Error();
                    return response.json();
                })
                .then(function (data) {
                    const seenIds = [];

                    data.comments.forEach(function (comment) {
                        seenIds.push(String(comment.id));
                        const existing = listEl.querySelector('[data-comment-id="' + comment.id + '"]');

                        if (! existing) {
                            removeEmptyState();
                            const row = buildCommentRow(comment);
                            listEl.appendChild(row);
                            wireComment(row);
                            return;
                        }

                        if (existing.querySelector('.comment-edit-textarea')) return;

                        const bodyText = existing.querySelector('.comment-body-text');
                        if (bodyText && bodyText.textContent !== comment.body) {
                            bodyText.textContent = comment.body;
                        }
                    });

                    listEl.querySelectorAll('[data-comment-id]').forEach(function (row) {
                        if (seenIds.indexOf(row.dataset.commentId) === -1 && ! row.querySelector('.comment-edit-textarea')) {
                            row.remove();
                        }
                    });

                    showEmptyStateIfNeeded();
                })
                .catch(function () {
                    // Transient polling failure — stay quiet, next tick retries.
                });
        }

        setInterval(syncComments, 7000);
    })();
</script>
