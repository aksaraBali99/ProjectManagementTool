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
                <p class="comment-body-text mt-1 text-[12px] text-gray-700">{{ $comment->body }}</p>
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

    <div class="mt-2 flex items-start gap-2" style="position: relative;">
        <textarea class="new-comment-body flex-1 rounded-md border border-gray-300 px-3 py-2 text-[12px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600" rows="2" placeholder="Add a comment… type @ to mention someone"></textarea>
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
        const bodyInput = container.querySelector('.new-comment-body');
        const postBtn = container.querySelector('.post-comment-btn');
        const errorEl = container.querySelector('.comment-error');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const mentionableUsers = @json($mentionableUsers ?? []);

        // Plain-textarea @mention autocomplete: no caret-pixel tracking (the
        // dropdown just anchors below the textarea — precise enough for a
        // comment box this small), and no contenteditable/rich-text markup —
        // a picked name is inserted as literal "@Full Name " text. Mentioned
        // IDs are tracked per textarea instance as they're picked, but a
        // literal-text-still-present check at read time (getMentionedUserIds)
        // is what keeps the list correct after edits — deleting the "@Name"
        // text drops it from what's submitted, without needing to re-parse
        // the whole body. Reused for both the "new comment" box and any
        // "edit comment" box.
        function setupMentionAutocomplete(textarea) {
            const mentioned = new Map(); // user id -> the literal "@Name" text inserted for it

            // Edit mode starts from an existing body with no picker
            // history — seed from whichever mentionable names already
            // appear in the text, longest name first so "Jo" can't shadow
            // a "Jo Ann" match starting at the same position.
            mentionableUsers.slice().sort(function (a, b) {
                return b.name.length - a.name.length;
            }).forEach(function (user) {
                if (textarea.value.indexOf('@' + user.name) !== -1) {
                    mentioned.set(user.id, '@' + user.name);
                }
            });

            let dropdown = null;

            function closeDropdown() {
                if (dropdown) {
                    dropdown.remove();
                    dropdown = null;
                }
            }

            function activeQuery() {
                const caret = textarea.selectionStart;
                const value = textarea.value;
                const uptoCaret = value.slice(0, caret);
                const at = uptoCaret.lastIndexOf('@');
                if (at === -1) return null;

                const between = uptoCaret.slice(at + 1);
                if (/\s/.test(between)) return null;

                const before = at === 0 ? '' : value[at - 1];
                if (before && ! /\s/.test(before)) return null;

                return { at: at, query: between };
            }

            function selectMention(user, at) {
                const caret = textarea.selectionStart;
                const value = textarea.value;
                const display = '@' + user.name + ' ';

                textarea.value = value.slice(0, at) + display + value.slice(caret);
                const newCaret = at + display.length;
                textarea.focus();
                textarea.setSelectionRange(newCaret, newCaret);
                mentioned.set(user.id, '@' + user.name);
                closeDropdown();
            }

            function highlightOption(options, index) {
                options.forEach(function (option, i) {
                    option.classList.toggle('bg-gray-50', i === index);
                });
            }

            function renderDropdown(matches, at) {
                closeDropdown();
                if (matches.length === 0) return;

                dropdown = document.createElement('div');
                dropdown.className = 'mention-dropdown absolute z-20 w-56 overflow-y-auto rounded-md border border-gray-200 bg-white text-[12px] shadow-lg';

                matches.forEach(function (user) {
                    const item = document.createElement('button');
                    item.type = 'button';
                    item.className = 'mention-option block w-full px-3 py-1.5 text-left hover:bg-gray-50';
                    item.textContent = user.name;
                    // mousedown, not click: fires before the textarea blurs,
                    // so selectionStart/selectionEnd still point where the
                    // user left them when selectMention reads them.
                    item.addEventListener('mousedown', function (event) {
                        event.preventDefault();
                        selectMention(user, at);
                    });
                    dropdown.appendChild(item);
                });

                highlightOption(Array.prototype.slice.call(dropdown.querySelectorAll('.mention-option')), 0);

                const parent = textarea.parentElement;
                if (getComputedStyle(parent).position === 'static') {
                    parent.style.position = 'relative';
                }
                parent.appendChild(dropdown);

                // Flip above the textarea when there isn't enough room below
                // to show the list on screen (a comment box near the bottom
                // of the page, or a scrolled-down task-list drilldown row)
                // — otherwise the dropdown would render partly or fully off
                // the bottom of the viewport with no way to reach the rest
                // of it. dropdown.scrollHeight here is its natural height,
                // read before any max-height constraint is applied.
                const margin = 4;
                const edgePadding = 8;
                const textareaRect = textarea.getBoundingClientRect();
                const spaceBelow = window.innerHeight - textareaRect.bottom - margin - edgePadding;
                const spaceAbove = textareaRect.top - margin - edgePadding;
                const placeAbove = spaceBelow < dropdown.scrollHeight && spaceAbove > spaceBelow;

                dropdown.style.maxHeight = Math.max(80, placeAbove ? spaceAbove : spaceBelow) + 'px';
                dropdown.style.left = textarea.offsetLeft + 'px';

                if (placeAbove) {
                    dropdown.style.top = (textarea.offsetTop - margin - dropdown.offsetHeight) + 'px';
                } else {
                    dropdown.style.top = (textarea.offsetTop + textarea.offsetHeight + margin) + 'px';
                }
            }

            textarea.addEventListener('input', function () {
                const q = activeQuery();
                if (! q) {
                    closeDropdown();
                    return;
                }

                const query = q.query.toLowerCase();
                const matches = mentionableUsers.filter(function (user) {
                    return user.name.toLowerCase().indexOf(query) !== -1;
                }).slice(0, 6);

                renderDropdown(matches, q.at);
            });

            textarea.addEventListener('keydown', function (event) {
                if (! dropdown) return;

                const options = Array.prototype.slice.call(dropdown.querySelectorAll('.mention-option'));
                if (options.length === 0) return;

                let activeIndex = options.findIndex(function (option) {
                    return option.classList.contains('bg-gray-50');
                });
                if (activeIndex === -1) activeIndex = 0;

                if (event.key === 'ArrowDown') {
                    event.preventDefault();
                    highlightOption(options, (activeIndex + 1) % options.length);
                } else if (event.key === 'ArrowUp') {
                    event.preventDefault();
                    highlightOption(options, (activeIndex - 1 + options.length) % options.length);
                } else if (event.key === 'Enter' || event.key === 'Tab') {
                    event.preventDefault();
                    options[activeIndex].dispatchEvent(new MouseEvent('mousedown', { bubbles: true, cancelable: true }));
                } else if (event.key === 'Escape') {
                    closeDropdown();
                }
            });

            // Delayed so a mousedown-selected option's own handler still
            // runs (and can call preventDefault/read the caret) before the
            // dropdown that owns it gets torn down.
            textarea.addEventListener('blur', function () {
                setTimeout(closeDropdown, 150);
            });

            return {
                getMentionedUserIds: function () {
                    const ids = [];
                    mentioned.forEach(function (display, id) {
                        if (textarea.value.indexOf(display) !== -1) ids.push(id);
                    });
                    return ids;
                },
                reset: function () {
                    mentioned.clear();
                },
            };
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
                    textarea.className = 'comment-edit-textarea mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-[12px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600';
                    textarea.rows = 2;
                    textarea.value = currentBody;
                    bodyText.replaceWith(textarea);
                    row.querySelector('.comment-actions').style.display = 'none';
                    const editMentions = setupMentionAutocomplete(textarea);

                    const controls = document.createElement('div');
                    controls.className = 'comment-edit-controls mt-1 flex items-center gap-2';
                    controls.innerHTML = '<button type="button" class="save-comment-btn text-[10px] font-medium text-brand-600 hover:underline">Save</button>'
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

                        requestOrThrow('/comments/' + row.dataset.commentId, 'PUT', { body: newBody, mentioned_user_ids: editMentions.getMentionedUserIds() }, 'Failed to save comment.')
                            .then(function (response) {
                                return response.json();
                            })
                            .then(function (data) {
                                restore(data.comment.body);
                            })
                            .catch(function (error) {
                                alert(error.message);
                            });
                    });
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

        const newCommentMentions = setupMentionAutocomplete(bodyInput);

        postBtn.addEventListener('click', function () {
            const body = bodyInput.value.trim();
            if (body === '') return;

            errorEl.style.display = 'none';
            postBtn.disabled = true;

            requestOrThrow('/tasks/' + taskId + '/comments', 'POST', { body: body, mentioned_user_ids: newCommentMentions.getMentionedUserIds() }, 'Failed to post comment.')
                .then(function (response) {
                    return response.json();
                })
                .then(function (data) {
                    removeEmptyState();

                    const item = buildCommentRow(Object.assign({}, data.comment, { can_edit: true }));
                    listEl.appendChild(item);
                    wireComment(item);
                    bodyInput.value = '';
                    newCommentMentions.reset();
                })
                .catch(function (error) {
                    errorEl.textContent = error.message;
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
