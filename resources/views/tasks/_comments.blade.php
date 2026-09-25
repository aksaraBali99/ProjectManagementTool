{{-- Self-contained per inclusion (document.currentScript, not a global id),
     matches the pattern used by tasks/_subtasks.blade.php since this also
     renders once per drilldown row on the task list.

     Threading (task #70, phase 2) matches Jira Cloud's real behavior:
     EVERY comment — top-level or itself a reply — shows a "Reply"
     action, but storage stays genuinely flat/one-level regardless of
     which one was clicked (CommentController::resolveParentCommentId()
     always re-parents to the top-level ancestor). Replies render as ONE
     flat, chronologically-ordered list under their top-level comment,
     with no deeper visual nesting — the mention pre-filled into the
     reply editor (see openReplyCompose() below) is what conveys who a
     specific reply was actually addressing, not indentation. Both live
     in the same comments table/query — see Comment::replies()/
     parentComment() — grouped here into topLevelComments +
     repliesByParent for the initial render; the 7s poll (syncComments()
     below) groups the same shape from a single flat fetch client-side,
     using the exact same rule (parent_comment_id null = top-level).

     Every mention (whether typed manually or pre-filled by clicking
     Reply) is plain "@Full Name" text tracked client-side, not a special
     rich-text node — see rich-text-editor.js's setupMentions(), the same
     mechanism the main new-comment editor already uses. --}}
@php
    $topLevelComments = $task->comments->whereNull('parent_comment_id')->sortBy('created_at')->values();
    $repliesByParent = $task->comments->whereNotNull('parent_comment_id')->groupBy('parent_comment_id');

    // The org/permission part of CommentPolicy::update()'s rule is the
    // SAME for every comment/reply on this page (they all belong to this
    // one task's organization) — computed once here rather than via
    // auth()->user()->can('update', $comment) per comment/reply below,
    // which re-ran isSuperAdmin()/isOwner()/hasPermission() (each its own
    // query) once per row. See CommentPolicy::canEditOwnComments()'s own
    // docblock for the full story (task #70 phase 4's query-count
    // regression test surfaced this on the JSON polling endpoint;
    // this initial render had the identical N+1).
    $canEditOwnComments = app(\App\Policies\CommentPolicy::class)->canEditOwnComments(auth()->user(), $task->organization_id);
    $isSuperAdminOrOwner = auth()->user()->isSuperAdmin() || auth()->user()->isOwner();

    // task #70 phase 4 follow-up: a smiley-plus icon instead of a plain
    // "React" text link, matching the stroke-based icon style
    // rich-text-editor.js's own ICON_ATTRS/ICONS.emoji already use
    // (14x14 rendered, 16x16 viewBox, currentColor stroke) — no existing
    // "add reaction" glyph in this codebase to copy, so this combines
    // that same smiley with a small "+" the way Slack/Discord's own
    // add-reaction icon does. buildCommentCard() in the script below
    // renders the identical markup for a card built client-side.
    $reactIconAttrs = 'width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"';
    $reactIcon = '<svg '.$reactIconAttrs.'><circle cx="6.3" cy="6.3" r="4.8"/><circle cx="4.7" cy="5.2" r=".6" fill="currentColor" stroke="none"/><circle cx="7.9" cy="5.2" r=".6" fill="currentColor" stroke="none"/><path d="M4.1 7.6a3.1 3.1 0 0 0 4.4 0"/><line x1="11.5" y1="10" x2="11.5" y2="14"/><line x1="9.5" y1="12" x2="13.5" y2="12"/></svg>';
@endphp
<div class="comment-container" data-task-id="{{ $task->id }}">
    <div class="comment-list space-y-3">
        @forelse ($topLevelComments as $comment)
            @php $canEditComment = $canEditOwnComments && ($isSuperAdminOrOwner || $comment->user_id === auth()->id()); @endphp
            <div class="comment-thread" data-comment-id="{{ $comment->id }}">
                <div class="comment-card rounded-md bg-white border border-gray-200 px-3 py-2" data-comment-id="{{ $comment->id }}" data-can-edit="{{ $canEditComment ? '1' : '0' }}" data-author-id="{{ $comment->user_id }}" data-author-name="{{ $comment->user->name }}">
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
                    <x-rich-text :value="$comment->body" class="comment-body-text mt-1 text-[12px] text-gray-700" :data-body-hash="md5($comment->body)" :data-mentioned-users="$comment->mentionedUsers->map(fn ($user) => ['id' => $user->id, 'name' => $user->name])->values()->toJson()" />
                    {{-- task #70 phase 4: reaction pills, one per distinct emoji
                         reacted with on this comment — Comment::reactionSummary()
                         does the aggregation in memory (comments.reactions.user
                         is eager-loaded at the task level, see
                         TaskManagementController), so rendering this whole list
                         costs zero extra queries beyond that one relation load,
                         however many comments/reactions the task has. --}}
                    <div class="reactions-list mt-1 flex flex-wrap items-center gap-1" data-reactions-hash="{{ $comment->reactionsHash(auth()->id()) }}">
                        @foreach ($comment->reactionSummary(auth()->id()) as $reaction)
                            <button type="button" class="reaction-pill{{ $reaction['reacted_by_me'] ? ' is-mine' : '' }}" data-emoji="{{ $reaction['emoji'] }}" title="{{ implode(', ', $reaction['user_names']) }}">{{ $reaction['emoji'] }} <span class="reaction-count">{{ $reaction['count'] }}</span></button>
                        @endforeach
                    </div>
                    <div class="comment-actions mt-1 flex items-center gap-2">
                        @if ($canEditComment)
                            <button type="button" class="edit-comment-btn text-[10px] text-brand-600 hover:underline">Edit</button>
                            <button type="button" class="delete-comment-btn text-[10px] text-gray-500 hover:underline">Delete</button>
                        @endif
                        {{-- Replying follows the same rule as posting a top-level
                             comment (anyone who can view the task) — not gated by
                             canEditComment, which only governs editing/deleting
                             THIS PARTICULAR comment. Shown on every comment,
                             including replies (see the reply cards below). --}}
                        <button type="button" class="reply-comment-btn text-[10px] text-brand-600 hover:underline">Reply</button>
                        {{-- Same permission rule as Reply — anyone who can view
                             the task can react, not gated by canEditComment. --}}
                        <button type="button" class="react-comment-btn inline-flex items-center text-brand-600 hover:text-brand-700" title="Add reaction" aria-label="Add reaction">{!! $reactIcon !!}</button>
                    </div>
                </div>
                <div class="replies-list mt-2 ml-6 space-y-2">
                    @foreach ($repliesByParent->get($comment->id, collect())->sortBy('created_at') as $reply)
                        @php $canEditReply = $canEditOwnComments && ($isSuperAdminOrOwner || $reply->user_id === auth()->id()); @endphp
                        <div class="comment-card rounded-md bg-white border border-gray-200 px-3 py-2" data-comment-id="{{ $reply->id }}" data-can-edit="{{ $canEditReply ? '1' : '0' }}" data-author-id="{{ $reply->user_id }}" data-author-name="{{ $reply->user->name }}">
                            <div class="flex items-center justify-between">
                                <span class="inline-flex items-center gap-1.5">
                                    <x-avatar :user="$reply->user" size="18px" />
                                    <span class="text-[10px] font-medium text-[#1F2937]">{{ $reply->user->name }}</span>
                                </span>
                                <span class="text-[10px] text-gray-400">{{ $reply->created_at->format('M j, Y g:ia') }}</span>
                            </div>
                            <x-rich-text :value="$reply->body" class="comment-body-text mt-1 text-[12px] text-gray-700" :data-body-hash="md5($reply->body)" :data-mentioned-users="$reply->mentionedUsers->map(fn ($user) => ['id' => $user->id, 'name' => $user->name])->values()->toJson()" />
                            <div class="reactions-list mt-1 flex flex-wrap items-center gap-1" data-reactions-hash="{{ $reply->reactionsHash(auth()->id()) }}">
                                @foreach ($reply->reactionSummary(auth()->id()) as $reaction)
                                    <button type="button" class="reaction-pill{{ $reaction['reacted_by_me'] ? ' is-mine' : '' }}" data-emoji="{{ $reaction['emoji'] }}" title="{{ implode(', ', $reaction['user_names']) }}">{{ $reaction['emoji'] }} <span class="reaction-count">{{ $reaction['count'] }}</span></button>
                                @endforeach
                            </div>
                            <div class="comment-actions mt-1 flex items-center gap-2">
                                @if ($canEditReply)
                                    <button type="button" class="edit-comment-btn text-[10px] text-brand-600 hover:underline">Edit</button>
                                    <button type="button" class="delete-comment-btn text-[10px] text-gray-500 hover:underline">Delete</button>
                                @endif
                                <button type="button" class="reply-comment-btn text-[10px] text-brand-600 hover:underline">Reply</button>
                                <button type="button" class="react-comment-btn inline-flex items-center text-brand-600 hover:text-brand-700" title="Add reaction" aria-label="Add reaction">{!! $reactIcon !!}</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <p class="comment-empty text-[11px] text-gray-500">No comments yet.</p>
        @endforelse
    </div>

    <div class="mt-2 flex items-start gap-2">
        <x-rich-text-editor class="new-comment-editor min-w-0 flex-1" compact label="New comment" placeholder="Add a comment… type @ to mention someone" :mentions="$mentionableUsers ?? []" :image-task-id="$task->id" image-context="comment" :audio-task-id="$task->id" audio-context="comment" :video-task-id="$task->id" video-context="comment" :document-task-id="$task->id" document-context="comment" :link-preview-task-id="$task->id" link-preview-context="comment" />
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

        // task #70 phase 3: captures each server-rendered comment body's
        // PRISTINE html (before mention-highlight.js wraps any "@Name"
        // text in a <span>) into its own dataset, since this inline
        // script — synchronous, runs during initial HTML parsing — always
        // executes before app.js's module code (deferred by default) can
        // call highlightRichText() and mutate these bodies. Editing a
        // comment always reads from this raw copy, never from the live
        // (possibly already-highlighted) .innerHTML — see the edit-button
        // handler further down.
        container.querySelectorAll('.comment-body-text[data-rich-text-content]').forEach(function (bodyText) {
            bodyText.dataset.rawHtml = bodyText.innerHTML;
        });

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
        // raw stored value — safe to assign as markup. dataset.rawHtml keeps
        // that exact string around (task #70 phase 3) so a later Edit click
        // always re-seeds the editor from it, not from .innerHTML, which
        // mention-highlight.js may since have wrapped in <span class="mention">.
        function buildBodyElement(html, hash, mentionedUsers) {
            const body = document.createElement('div');
            body.className = 'comment-body-text rich-text mt-1 text-[12px] text-gray-700';
            body.setAttribute('data-rich-text-content', '');
            body.dataset.bodyHash = hash;
            body.dataset.rawHtml = html;
            body.dataset.mentionedUsers = JSON.stringify(mentionedUsers || []);
            body.innerHTML = html;

            return body;
        }

        // task #70 phase 4: (re)builds a comment/reply card's reactions-list
        // from the {emoji, count, user_names, reacted_by_me} shape both
        // CommentController and CommentReactionController return (the exact
        // same shape Comment::reactionSummary() produces, and the initial
        // Blade render already used) — one function, reused for a freshly
        // posted comment (empty), a reaction just added/replaced/removed
        // (postReaction()'s own response) and a reaction discovered via the
        // 7s poll (syncComments()) alike, so all three can never render this
        // differently from one another. Rebuilds the whole list rather than
        // diffing individual pills — reaction counts are small enough that
        // this is simpler and in practice no more work than a real diff.
        function renderReactions(card, reactions) {
            const list = card.querySelector('.reactions-list');
            if (! list) return;

            list.innerHTML = '';
            reactions.forEach(function (reaction) {
                const pill = document.createElement('button');
                pill.type = 'button';
                pill.className = 'reaction-pill' + (reaction.reacted_by_me ? ' is-mine' : '');
                pill.dataset.emoji = reaction.emoji;
                pill.title = reaction.user_names.join(', ');
                pill.appendChild(document.createTextNode(reaction.emoji + ' '));
                const count = document.createElement('span');
                count.className = 'reaction-count';
                count.textContent = String(reaction.count);
                pill.appendChild(count);
                list.appendChild(pill);
            });
        }

        // One reaction picker for the whole comment section — reused across
        // every comment/reply's own React button rather than building one
        // per card, the same singleton-popover approach the toolbar's own
        // :emoji: button already uses (buildEmojiPicker() closes any
        // previously open picker before opening another). reactionTarget
        // records which comment the currently open (or most recently
        // opened) picker is FOR, read by onPick() at the moment a choice is
        // actually made — the picker itself has no notion of "which
        // comment", only this wrapper does.
        let reactionTarget = null;
        let reactionPickerPromise = null;

        function getReactionPicker() {
            if (! reactionPickerPromise) {
                reactionPickerPromise = window.solavaRichText.buildEmojiPicker({
                    onPick: function (item) {
                        if (reactionTarget !== null) postReaction(reactionTarget, item.emoji);
                    },
                });
            }

            return reactionPickerPromise;
        }

        function openReactionPicker(commentId, button) {
            reactionTarget = commentId;
            getReactionPicker().then(function (picker) { picker.toggle(button); });
        }

        // The single toggle endpoint for add/replace/remove alike
        // (CommentReactionController::store() decides which, based on
        // whatever this user's existing reaction on this comment already
        // is) — used both when picking an emoji from the picker and when
        // clicking an existing pill directly (a pill click always targets
        // the SAME emoji already shown on it, so clicking your own pill
        // un-reacts, and clicking someone else's pill adds/switches your
        // own reaction to that emoji — one code path either way).
        function postReaction(commentId, emoji) {
            requestOrThrow('/comments/' + commentId + '/reactions', 'POST', { emoji: emoji }, 'Failed to react.')
                .then(function (response) { return response.json(); })
                .then(function (data) {
                    const card = findCard(commentId);
                    if (! card) return;
                    renderReactions(card, data.reactions);
                    card.querySelector('.reactions-list').dataset.reactionsHash = data.reactions_hash;
                })
                .catch(function (error) {
                    alert(error.message);
                });
        }

        // The visual card shared by a top-level comment and a reply alike
        // (avatar/name/timestamp/body/actions) — every comment gets a
        // Reply action, top-level or itself a reply (matches Jira: who
        // ends up pre-filled as a mention is resolved from whichever
        // comment was actually clicked, not from a fixed "only top-level"
        // rule — see openReplyCompose()). authorId/authorName carry
        // through to dataset so a reply posted THIS session (no page
        // reload) can itself be replied to correctly, same as one loaded
        // from the initial Blade render.
        function buildCommentCard(comment) {
            const card = document.createElement('div');
            card.className = 'comment-card rounded-md bg-white border border-gray-200 px-3 py-2';
            card.dataset.commentId = comment.id;
            card.dataset.canEdit = comment.can_edit ? '1' : '0';
            card.dataset.authorId = comment.user_id;
            card.dataset.authorName = comment.user_name;

            const editDeleteHtml = comment.can_edit
                ? '<button type="button" class="edit-comment-btn text-[10px] text-brand-600 hover:underline">Edit</button>'
                    + '<button type="button" class="delete-comment-btn text-[10px] text-gray-500 hover:underline">Delete</button>'
                : '';
            const replyHtml = '<button type="button" class="reply-comment-btn text-[10px] text-brand-600 hover:underline">Reply</button>';
            // Identical markup to the server-rendered button above (same
            // smiley-plus icon, same title/aria-label) — see this file's
            // top @php block for why this glyph was chosen.
            const reactHtml = '<button type="button" class="react-comment-btn inline-flex items-center text-brand-600 hover:text-brand-700" title="Add reaction" aria-label="Add reaction">'
                + '<svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'
                + '<circle cx="6.3" cy="6.3" r="4.8"/><circle cx="4.7" cy="5.2" r=".6" fill="currentColor" stroke="none"/><circle cx="7.9" cy="5.2" r=".6" fill="currentColor" stroke="none"/>'
                + '<path d="M4.1 7.6a3.1 3.1 0 0 0 4.4 0"/><line x1="11.5" y1="10" x2="11.5" y2="14"/><line x1="9.5" y1="12" x2="13.5" y2="12"/></svg>'
                + '</button>';
            const actionsHtml = '<div class="comment-actions mt-1 flex items-center gap-2">' + editDeleteHtml + replyHtml + reactHtml + '</div>';
            // Always empty at build time — a comment JS builds here is either
            // brand new (nobody could have reacted yet) or freshly discovered
            // by the poll (which, if it already has reactions, rebuilds this
            // via renderReactions() right after — see both call sites below).
            const reactionsHtml = '<div class="reactions-list mt-1 flex flex-wrap items-center gap-1" data-reactions-hash=""></div>';

            const avatarStyle = 'background-color: ' + comment.user_avatar_bg + '; color: ' + comment.user_avatar_text
                + '; width: 18px; height: 18px; font-size: calc(18px * 0.43);';

            card.innerHTML = '<div class="flex items-center justify-between">'
                + '<span class="inline-flex items-center gap-1.5">'
                    + '<span class="inline-flex shrink-0 items-center justify-center rounded-full font-medium leading-none" style="' + avatarStyle + '">' + escapeHtml(comment.user_initials) + '</span>'
                    + '<span class="text-[10px] font-medium text-[#1F2937]">' + escapeHtml(comment.user_name) + '</span>'
                + '</span>'
                + '<span class="text-[10px] text-gray-400">' + escapeHtml(comment.created_at) + '</span>'
                + '</div>'
                + reactionsHtml
                + actionsHtml;

            const actions = card.querySelector('.comment-actions');
            const body = buildBodyElement(comment.body_html, comment.body_hash, comment.mentioned_users);
            if (actions) actions.before(body);
            else card.appendChild(body);

            renderReactions(card, comment.reactions || []);
            card.querySelector('.reactions-list').dataset.reactionsHash = comment.reactions_hash || '';

            return card;
        }

        // A brand-new top-level comment's full thread — its own card plus
        // an (initially empty) replies-list for later replies to land in.
        // Never called for a reply, which is just its buildCommentCard()
        // appended straight into its parent thread's .replies-list.
        function buildCommentThread(comment) {
            const thread = document.createElement('div');
            thread.className = 'comment-thread';
            thread.dataset.commentId = comment.id;
            thread.appendChild(buildCommentCard(comment));

            const repliesList = document.createElement('div');
            repliesList.className = 'replies-list mt-2 ml-6 space-y-2';
            thread.appendChild(repliesList);

            return thread;
        }

        function findCard(commentId) {
            return container.querySelector('.comment-card[data-comment-id="' + commentId + '"]');
        }

        function findThread(commentId) {
            return container.querySelector('.comment-thread[data-comment-id="' + commentId + '"]');
        }

        // Inserts a new element (a .comment-thread into .comment-list, or
        // a reply's .comment-card into a thread's .replies-list — same
        // helper either way, since both containers hold direct children
        // keyed by data-comment-id) at its correct CHRONOLOGICAL position
        // rather than always at the end. Comment ids are auto-increment
        // and therefore already order-equivalent to created_at, so a
        // numeric id comparison is simpler and more reliable than parsing
        // the display-formatted created_at string back into a sortable
        // value. Kept generic (container + element + id, no assumption
        // about what kind of element it is) so Phase 4's reactions can
        // reuse it rather than needing a second insertion strategy.
        function insertInOrder(container, element, commentId) {
            const existing = Array.prototype.filter.call(container.children, function (child) {
                return child.dataset && child.dataset.commentId !== undefined;
            });

            for (let i = 0; i < existing.length; i++) {
                if (Number(existing[i].dataset.commentId) > commentId) {
                    container.insertBefore(element, existing[i]);
                    return;
                }
            }

            container.appendChild(element);
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

        // Builds and opens a reply-compose box under clickedCard's own
        // thread — clickedCard can be a top-level comment's card OR a
        // reply's card, either way .closest('.comment-thread') finds the
        // right ancestor (a reply's card already lives nested inside its
        // thread's own subtree). Created on demand (same lazy
        // create/destroy pattern as the Edit-in-place editor below), not
        // pre-rendered by Blade, so a page with many comments doesn't
        // mount a hidden TipTap instance per thread up front. Appended as
        // a sibling AFTER .replies-list, never inside it, so a new reply
        // arriving via syncComments() while this is open only ever
        // touches .replies-list and can't disturb an in-progress draft
        // here. The resulting reply is always parented to clickedCard's
        // OWN id (top-level or reply) — the server
        // (resolveParentCommentId()) re-parents it to the top-level
        // ancestor for storage.
        //
        // Pre-fills the editor with a REAL, removable mention of
        // clickedCard's author — not a server-side guarantee (see
        // CommentController::store()'s own docblock for why that was
        // deliberately dropped): setupMentions()'s seedFromText() (rich-
        // text-editor.js) scans this initial content the same way it
        // would scan any pre-loaded content, so if the user deletes the
        // "@Name" text before posting, getMentionedUserIds() correctly
        // stops reporting it — real freedom to remove it, not a cosmetic
        // placeholder the server overrides anyway. Only pre-filled when
        // the author is actually in mentionableUsers: that list already
        // excludes yourself (no self-mention, matching the manual-mention
        // rule), and naturally excludes anyone who's since lost view
        // access to this task (Task::viewableUsers(), the Phase 1 fix) -
        // skipping the pre-fill for either case is the safe default
        // rather than mentioning someone the eligibility check would
        // reject anyway.
        function openReplyCompose(clickedCard) {
            const thread = clickedCard.closest('.comment-thread');
            if (thread.querySelector('.reply-compose')) return; // already open

            const authorId = Number(clickedCard.dataset.authorId);
            const authorName = clickedCard.dataset.authorName;
            const authorIsMentionable = mentionableUsers.some(function (user) { return user.id === authorId; });

            const compose = document.createElement('div');
            compose.className = 'reply-compose mt-2 ml-6';

            const editRoot = document.createElement('div');
            editRoot.className = 'reply-editor rte-loading';
            editRoot.setAttribute('data-rich-text', '');
            editRoot.setAttribute('data-compact', '');
            if (authorIsMentionable) {
                editRoot.dataset.content = '<p>@' + escapeHtml(authorName) + ' </p>';
            }
            editRoot.dataset.label = 'Reply';
            editRoot.dataset.mentions = JSON.stringify(mentionableUsers);
            editRoot.dataset.imageTaskId = taskId;
            editRoot.dataset.imageContext = 'comment';
            editRoot.dataset.audioTaskId = taskId;
            editRoot.dataset.audioContext = 'comment';
            editRoot.dataset.videoTaskId = taskId;
            editRoot.dataset.videoContext = 'comment';
            editRoot.dataset.documentTaskId = taskId;
            editRoot.dataset.documentContext = 'comment';
            editRoot.dataset.linkPreviewTaskId = taskId;
            editRoot.dataset.linkPreviewContext = 'comment';

            const postWrap = document.createElement('div');
            postWrap.className = 'flex items-center gap-2 pt-1';
            postWrap.innerHTML = '<button type="button" class="post-reply-btn rounded-md border border-gray-300 px-3 py-2 text-[12px] font-medium text-gray-700 hover:bg-gray-50">Reply</button>'
                + '<button type="button" class="cancel-reply-btn text-[11px] text-gray-500 hover:underline">Cancel</button>';

            const errorEl = document.createElement('p');
            errorEl.className = 'reply-error mt-1 text-[11px] text-red-600';
            errorEl.style.display = 'none';

            compose.appendChild(editRoot);
            compose.appendChild(postWrap);
            compose.appendChild(errorEl);
            thread.appendChild(compose);

            let editor = null;
            withEditor(editRoot).then(function (created) {
                editor = created;
                editor.focus();
            });

            function closeCompose() {
                if (editor) editor.destroy();
                compose.remove();
            }

            postWrap.querySelector('.cancel-reply-btn').addEventListener('click', closeCompose);

            postWrap.querySelector('.post-reply-btn').addEventListener('click', function () {
                if (! editor || editor.isEmpty()) return;

                errorEl.style.display = 'none';

                requestOrThrow('/tasks/' + taskId + '/comments', 'POST', {
                    body: editor.getHTML(),
                    mentioned_user_ids: editor.getMentionedUserIds(),
                    parent_comment_id: Number(clickedCard.dataset.commentId),
                }, 'Failed to post reply.')
                    .then(function (response) {
                        return response.json();
                    })
                    .then(function (data) {
                        const repliesList = thread.querySelector('.replies-list');
                        const card = buildCommentCard(Object.assign({}, data.comment, { can_edit: true }));
                        insertInOrder(repliesList, card, data.comment.id);
                        wireCard(card);
                        highlightCode(card);
                        closeCompose();
                    })
                    .catch(function (error) {
                        errorEl.textContent = error.message;
                        errorEl.style.display = '';
                    });
            });

            editRoot.addEventListener('rte:submit', function () { postWrap.querySelector('.post-reply-btn').click(); });
        }

        // Wired once per comment CARD (top-level or reply alike) for
        // Edit/Delete (when permitted) and Reply (always). The card is
        // the identity element for editing state — a currently-open
        // Edit-in-place editor or reply-compose box is found relative to
        // it, never assumed from the wider thread.
        function wireCard(card) {
            const editBtn = card.querySelector('.edit-comment-btn');
            const deleteBtn = card.querySelector('.delete-comment-btn');
            const replyBtn = card.querySelector('.reply-comment-btn');

            if (replyBtn) {
                replyBtn.addEventListener('click', function () {
                    openReplyCompose(card);
                });
            }

            if (! editBtn && ! deleteBtn) return;

            if (editBtn) {
                editBtn.addEventListener('click', function () {
                    const bodyText = card.querySelector('.comment-body-text');
                    if (! bodyText) return; // already editing

                    // task #70 phase 3: dataset.rawHtml, not .innerHTML — the
                    // latter may already carry mention-highlight.js's
                    // <span class="mention"> wrappers, which must never leak
                    // into what gets edited/re-saved.
                    const currentHtml = bodyText.dataset.rawHtml;
                    const currentHash = bodyText.dataset.bodyHash;
                    const currentMentionedUsers = JSON.parse(bodyText.dataset.mentionedUsers || '[]');

                    const editRoot = document.createElement('div');
                    editRoot.className = 'comment-edit-editor rte-loading mt-1';
                    editRoot.setAttribute('data-rich-text', '');
                    editRoot.setAttribute('data-compact', '');
                    editRoot.dataset.content = currentHtml;
                    editRoot.dataset.label = 'Edit comment';
                    editRoot.dataset.mentions = JSON.stringify(mentionableUsers);
                    editRoot.dataset.imageTaskId = taskId;
                    editRoot.dataset.imageContext = 'comment';
                    editRoot.dataset.audioTaskId = taskId;
                    editRoot.dataset.audioContext = 'comment';
                    editRoot.dataset.videoTaskId = taskId;
                    editRoot.dataset.videoContext = 'comment';
                    editRoot.dataset.documentTaskId = taskId;
                    editRoot.dataset.documentContext = 'comment';
                    editRoot.dataset.linkPreviewTaskId = taskId;
                    editRoot.dataset.linkPreviewContext = 'comment';
                    bodyText.replaceWith(editRoot);
                    card.querySelector('.comment-actions').style.display = 'none';

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

                    function restore(html, hash, mentionedUsers) {
                        if (editor) editor.destroy();
                        const restored = buildBodyElement(html, hash, mentionedUsers);
                        editRoot.replaceWith(restored);
                        controls.remove();
                        card.querySelector('.comment-actions').style.display = '';
                        highlightCode(restored);
                    }

                    controls.querySelector('.cancel-comment-btn').addEventListener('click', function () {
                        restore(currentHtml, currentHash, currentMentionedUsers);
                    });

                    const saveBtn = controls.querySelector('.save-comment-btn');
                    saveBtn.addEventListener('click', function () {
                        if (! editor || editor.isEmpty()) return;

                        requestOrThrow('/comments/' + card.dataset.commentId, 'PUT', { body: editor.getHTML(), mentioned_user_ids: editor.getMentionedUserIds() }, 'Failed to save comment.')
                            .then(function (response) {
                                return response.json();
                            })
                            .then(function (data) {
                                restore(data.comment.body_html, data.comment.body_hash, data.comment.mentioned_users);
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
                    if (! confirm('Delete this comment? This cannot be undone.')) return;

                    requestOrThrow('/comments/' + card.dataset.commentId, 'DELETE', undefined, 'Failed to delete comment.')
                        .then(function () {
                            // Deleting a top-level comment cascades to its
                            // replies server-side — remove the whole
                            // thread, not just this card, so no orphaned
                            // empty thread/replies-list is left behind.
                            const thread = card.closest('.replies-list') ? null : card.closest('.comment-thread');
                            (thread || card).remove();
                            showEmptyStateIfNeeded();
                        })
                        .catch(function (error) {
                            alert(error.message);
                        });
                });
            }
        }

        container.querySelectorAll('.comment-card').forEach(wireCard);

        // Delegated (not per-card) — covers every React button and reaction
        // pill uniformly, whether server-rendered on initial load or built
        // later by buildCommentCard()/renderReactions(), with no separate
        // wiring step needed when a new card/pill is added.
        container.addEventListener('click', function (event) {
            const reactBtn = event.target.closest('.react-comment-btn');
            if (reactBtn) {
                const card = reactBtn.closest('.comment-card');
                if (card) openReactionPicker(card.dataset.commentId, reactBtn);
                return;
            }

            const pill = event.target.closest('.reaction-pill');
            if (pill) {
                const card = pill.closest('.comment-card');
                if (card) postReaction(card.dataset.commentId, pill.dataset.emoji);
            }
        });

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

                        const thread = buildCommentThread(Object.assign({}, data.comment, { can_edit: true }));
                        insertInOrder(listEl, thread, data.comment.id);
                        wireCard(thread.querySelector('.comment-card'));
                        highlightCode(thread);
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
        //
        // A single flat fetch (top-level comments and replies together,
        // see CommentController::index()), grouped client-side by
        // parent_comment_id — mirrors the same rule the initial Blade
        // render groups server-side. Processing order matches the
        // server's created_at ascending order, so a reply's parent thread
        // is always already in the DOM (either from initial render, or
        // built earlier in this same pass) by the time the reply's own
        // turn comes — replies can never arrive before the comment they're
        // replying to. Every insertion targets a specific container
        // (.comment-list for a new thread, a specific thread's
        // .replies-list for a new reply) via insertInOrder() rather than
        // appending blindly or rebuilding anything wholesale — a new
        // reply lands at its correct chronological position even if it
        // wasn't the very latest thing across the whole task (e.g. a
        // reply to an older thread arriving after a newer top-level
        // comment already has). This targeted-container approach is also
        // what keeps an open Edit-in-place editor, an open reply-compose
        // draft, and the page's scroll position all untouched by a
        // routine poll tick.
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
                        const existing = findCard(comment.id);

                        if (! existing) {
                            removeEmptyState();

                            if (! comment.parent_comment_id) {
                                const thread = buildCommentThread(comment);
                                insertInOrder(listEl, thread, comment.id);
                                wireCard(thread.querySelector('.comment-card'));
                                highlightCode(thread);
                                return;
                            }

                            // Parent is guaranteed already present — see
                            // the ordering note above.
                            const parentThread = findThread(comment.parent_comment_id);
                            if (! parentThread) return;

                            const card = buildCommentCard(comment);
                            insertInOrder(parentThread.querySelector('.replies-list'), card, comment.id);
                            wireCard(card);
                            highlightCode(card);
                            return;
                        }

                        if (existing.querySelector('.comment-edit-editor')) return;

                        const bodyText = existing.querySelector('.comment-body-text');
                        if (bodyText && bodyText.dataset.bodyHash !== comment.body_hash) {
                            bodyText.innerHTML = comment.body_html;
                            bodyText.dataset.bodyHash = comment.body_hash;
                            bodyText.dataset.rawHtml = comment.body_html;
                            bodyText.dataset.mentionedUsers = JSON.stringify(comment.mentioned_users || []);
                            highlightCode(bodyText);
                        }

                        // task #70 phase 4: independent of the body_hash check
                        // above — a reaction from someone else can arrive
                        // with the comment's own body completely unchanged,
                        // and vice versa. Same extensible-diffing idiom
                        // Phase 2/3 already used, just its own hash.
                        const reactionsList = existing.querySelector('.reactions-list');
                        if (reactionsList && reactionsList.dataset.reactionsHash !== comment.reactions_hash) {
                            renderReactions(existing, comment.reactions);
                            reactionsList.dataset.reactionsHash = comment.reactions_hash;
                        }
                    });

                    container.querySelectorAll('.comment-card').forEach(function (card) {
                        if (seenIds.indexOf(card.dataset.commentId) !== -1) return;
                        if (card.querySelector('.comment-edit-editor')) return;

                        const inReplies = card.closest('.replies-list');
                        (inReplies ? card : card.closest('.comment-thread') || card).remove();
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
