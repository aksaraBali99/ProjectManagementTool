{{-- Self-contained per inclusion (document.currentScript, not a global id),
     matches the pattern used by tasks/_subtasks.blade.php since this also
     renders once per drilldown row on the task list. --}}
<div class="comment-container" data-task-id="{{ $task->id }}">
    <div class="comment-list space-y-2">
        @forelse ($task->comments as $comment)
            @php $canEditComment = auth()->user()->can('update', $comment); @endphp
            <div class="rounded-md bg-white border border-gray-200 px-3 py-2" data-comment-id="{{ $comment->id }}" data-can-edit="{{ $canEditComment ? '1' : '0' }}">
                <div class="flex items-center justify-between">
                    <span class="inline-flex items-center gap-1.5">
                        <x-avatar :user="$comment->user" size="18px" />
                        <span class="text-[10px] font-medium text-[#1F2937]">{{ $comment->user->name }}</span>
                    </span>
                    <span class="text-[10px] text-gray-400">{{ $comment->created_at->format('M j, Y g:ia') }}</span>
                </div>
                {{-- Legacy plain-text comments and editor HTML both render through
                     RichText::toHtml() (escaped / sanitized respectively). The hash is
                     what the 7s poll compares to tell whether a body changed, since
                     comparing innerHTML against the server string would never match
                     once the browser re-serializes it. --}}
                <x-rich-text :value="$comment->body" class="comment-body-text mt-1 text-[12px] text-gray-700" :data-body-hash="md5($comment->body)" />
                @if ($canEditComment)
                    <div class="comment-actions mt-1 flex items-center gap-2">
                        <button type="button" class="edit-comment-btn text-[10px] text-brand-600 hover:underline">Edit</button>
                        <button type="button" class="delete-comment-btn text-[10px] text-gray-500 hover:underline">Delete</button>
                    </div>
                @endif
            </div>
        @empty
            <p class="comment-empty text-[11px] text-gray-500">No comments yet.</p>
        @endforelse
    </div>

    <div class="mt-2 flex items-start gap-2">
        <x-rich-text-editor class="new-comment-editor min-w-0 flex-1" compact label="New comment" placeholder="Add a comment… type @ to mention someone" :mentions="$mentionableUsers ?? []" />
        <button type="button" class="post-comment-btn rounded-md border border-gray-300 px-3 py-2 text-[12px] font-medium text-gray-700 hover:bg-gray-50">
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
        const editorRoot = container.querySelector('.new-comment-editor');
        const postBtn = container.querySelector('.post-comment-btn');
        const errorEl = container.querySelector('.comment-error');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const mentionableUsers = @json($mentionableUsers ?? []);

        // The rich-text editor (TipTap, shared with the task description) is
        // mounted by app.js, which is a deferred module script and so hasn't
        // run yet when this inline script executes — wait for it, then get the
        // editor controller for a given root element. Mentions, the toolbar and
        // @autocomplete all live in that shared module now, not here.
        function withEditor(root) {
            return new Promise(function (resolve) {
                function go() { window.solavaRichText.mount(root).then(resolve); }
                if (window.solavaRichText) go();
                else document.addEventListener('DOMContentLoaded', go);
            });
        }

        function highlightCode(scope) {
            if (window.solavaRichText) window.solavaRichText.highlight(scope);
        }

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

        // Surfaces the backend's actual validation/authorization message
        // instead of a generic "failed" string, falling back to that
        // generic string only when the response carries no message of its own.
        function requestOrThrow(url, method, body, fallback) {
            return request(url, method, body).then(function (response) {
                if (response.ok) return response;
                return response.json().catch(function () { return null; }).then(function (data) {
                    const fieldErrors = data && data.errors ? Object.values(data.errors)[0] : null;
                    const message = (Array.isArray(fieldErrors) && fieldErrors[0]) || (data && data.message) || fallback;
                    throw new Error(message);
                });
            });
        }

        // comment.body_html is server-sanitized (RichText::toHtml), never the
        // raw stored value — safe to assign as markup.
        function buildBodyElement(html, hash) {
            const body = document.createElement('div');
            body.className = 'comment-body-text rich-text mt-1 text-[12px] text-gray-700';
            body.setAttribute('data-rich-text-content', '');
            body.dataset.bodyHash = hash;
            body.innerHTML = html;

            return body;
        }

        function buildCommentRow(comment) {
            const row = document.createElement('div');
            row.className = 'rounded-md bg-white border border-gray-200 px-3 py-2';
            row.dataset.commentId = comment.id;
            row.dataset.canEdit = comment.can_edit ? '1' : '0';

            const actionsHtml = comment.can_edit
                ? '<div class="comment-actions mt-1 flex items-center gap-2">'
                    + '<button type="button" class="edit-comment-btn text-[10px] text-brand-600 hover:underline">Edit</button>'
                    + '<button type="button" class="delete-comment-btn text-[10px] text-gray-500 hover:underline">Delete</button>'
                    + '</div>'
                : '';

            const avatarStyle = 'background-color: ' + comment.user_avatar_bg + '; color: ' + comment.user_avatar_text
                + '; width: 18px; height: 18px; font-size: calc(18px * 0.43);';

            row.innerHTML = '<div class="flex items-center justify-between">'
                + '<span class="inline-flex items-center gap-1.5">'
                    + '<span class="inline-flex shrink-0 items-center justify-center rounded-full font-medium leading-none" style="' + avatarStyle + '">' + escapeHtml(comment.user_initials) + '</span>'
                    + '<span class="text-[10px] font-medium text-[#1F2937]">' + escapeHtml(comment.user_name) + '</span>'
                + '</span>'
                + '<span class="text-[10px] text-gray-400">' + escapeHtml(comment.created_at) + '</span>'
                + '</div>'
                + actionsHtml;

            const actions = row.querySelector('.comment-actions');
            const body = buildBodyElement(comment.body_html, comment.body_hash);
            if (actions) actions.before(body);
            else row.appendChild(body);

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

        // Wired once per row. The body element is looked up at click time (not
        // captured) because editing swaps it out for an editor and back.
        function wireComment(row) {
            const editBtn = row.querySelector('.edit-comment-btn');
            const deleteBtn = row.querySelector('.delete-comment-btn');
            if (! editBtn && ! deleteBtn) return;

            if (editBtn) {
                editBtn.addEventListener('click', function () {
                    const bodyText = row.querySelector('.comment-body-text');
                    if (! bodyText) return; // already editing

                    const currentHtml = bodyText.innerHTML;
                    const currentHash = bodyText.dataset.bodyHash;

                    const editRoot = document.createElement('div');
                    editRoot.className = 'comment-edit-editor rte-loading mt-1';
                    editRoot.setAttribute('data-rich-text', '');
                    editRoot.setAttribute('data-compact', '');
                    editRoot.dataset.content = currentHtml;
                    editRoot.dataset.label = 'Edit comment';
                    editRoot.dataset.mentions = JSON.stringify(mentionableUsers);
                    bodyText.replaceWith(editRoot);
                    row.querySelector('.comment-actions').style.display = 'none';

                    const controls = document.createElement('div');
                    controls.className = 'comment-edit-controls mt-1 flex items-center gap-2';
                    controls.innerHTML = '<button type="button" class="save-comment-btn text-[10px] font-medium text-brand-600 hover:underline">Save</button>'
                        + '<button type="button" class="cancel-comment-btn text-[10px] text-gray-500 hover:underline">Cancel</button>';
                    editRoot.insertAdjacentElement('afterend', controls);

                    let editor = null;
                    withEditor(editRoot).then(function (created) {
                        editor = created;
                        editor.focus();
                    });

                    function restore(html, hash) {
                        if (editor) editor.destroy();
                        const restored = buildBodyElement(html, hash);
                        editRoot.replaceWith(restored);
                        controls.remove();
                        row.querySelector('.comment-actions').style.display = '';
                        highlightCode(restored);
                    }

                    controls.querySelector('.cancel-comment-btn').addEventListener('click', function () {
                        restore(currentHtml, currentHash);
                    });

                    const saveBtn = controls.querySelector('.save-comment-btn');
                    saveBtn.addEventListener('click', function () {
                        if (! editor || editor.isEmpty()) return;

                        requestOrThrow('/comments/' + row.dataset.commentId, 'PUT', { body: editor.getHTML(), mentioned_user_ids: editor.getMentionedUserIds() }, 'Failed to save comment.')
                            .then(function (response) {
                                return response.json();
                            })
                            .then(function (data) {
                                restore(data.comment.body_html, data.comment.body_hash);
                            })
                            .catch(function (error) {
                                alert(error.message);
                            });
                    });
                    editRoot.addEventListener('rte:submit', function () { saveBtn.click(); });
                });
            }

            if (deleteBtn) {
                deleteBtn.addEventListener('click', function () {
                    requestOrThrow('/comments/' + row.dataset.commentId, 'DELETE', undefined, 'Failed to delete comment.')
                        .then(function () {
                            row.remove();
                            showEmptyStateIfNeeded();
                        })
                        .catch(function (error) {
                            alert(error.message);
                        });
                });
            }
        }

        listEl.querySelectorAll('[data-comment-id]').forEach(wireComment);

        postBtn.addEventListener('click', function () {
            withEditor(editorRoot).then(function (editor) {
                if (editor.isEmpty()) return;

                errorEl.style.display = 'none';
                postBtn.disabled = true;

                requestOrThrow('/tasks/' + taskId + '/comments', 'POST', { body: editor.getHTML(), mentioned_user_ids: editor.getMentionedUserIds() }, 'Failed to post comment.')
                    .then(function (response) {
                        return response.json();
                    })
                    .then(function (data) {
                        removeEmptyState();

                        const item = buildCommentRow(Object.assign({}, data.comment, { can_edit: true }));
                        listEl.appendChild(item);
                        wireComment(item);
                        highlightCode(item);
                        editor.clear();
                    })
                    .catch(function (error) {
                        errorEl.textContent = error.message;
                        errorEl.style.display = '';
                    })
                    .finally(function () {
                        postBtn.disabled = false;
                    });
            });
        });

        // Ctrl/Cmd+Enter inside the editor posts, same as clicking Post.
        editorRoot.addEventListener('rte:submit', function () { postBtn.click(); });

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
                            highlightCode(row);
                            return;
                        }

                        if (existing.querySelector('.comment-edit-editor')) return;

                        const bodyText = existing.querySelector('.comment-body-text');
                        if (bodyText && bodyText.dataset.bodyHash !== comment.body_hash) {
                            bodyText.innerHTML = comment.body_html;
                            bodyText.dataset.bodyHash = comment.body_hash;
                            highlightCode(bodyText);
                        }
                    });

                    listEl.querySelectorAll('[data-comment-id]').forEach(function (row) {
                        if (seenIds.indexOf(row.dataset.commentId) === -1 && ! row.querySelector('.comment-edit-editor')) {
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
