{{-- Self-contained per inclusion (document.currentScript, not a global id),
     matches the pattern used by tasks/_subtasks.blade.php since this also
     renders once per drilldown row on the task list. --}}
<div class="comment-container" data-task-id="{{ $task->id }}">
    <div class="comment-list space-y-2">
        @forelse ($task->comments as $comment)
            <div class="rounded-[8px] bg-white border border-gray-200 px-3 py-2">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-medium text-[#1F2937]">{{ $comment->user->name }}</span>
                    <span class="text-[10px] text-gray-400">{{ $comment->created_at->format('M j, Y g:ia') }}</span>
                </div>
                <p class="mt-1 text-[12px] text-gray-700">{{ $comment->body }}</p>
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

        postBtn.addEventListener('click', function () {
            const body = bodyInput.value.trim();
            if (body === '') return;

            errorEl.style.display = 'none';
            postBtn.disabled = true;

            fetch('/tasks/' + taskId + '/comments', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({ body: body }),
            })
                .then(function (response) {
                    if (! response.ok) throw new Error();
                    return response.json();
                })
                .then(function (data) {
                    const empty = listEl.querySelector('.comment-empty');
                    if (empty) empty.remove();

                    const item = document.createElement('div');
                    item.className = 'rounded-[8px] bg-white border border-gray-200 px-3 py-2';
                    item.innerHTML = '<div class="flex items-center justify-between">'
                        + '<span class="text-[11px] font-medium text-[#1F2937]">' + escapeHtml(data.comment.user_name) + '</span>'
                        + '<span class="text-[10px] text-gray-400">' + escapeHtml(data.comment.created_at) + '</span>'
                        + '</div>'
                        + '<p class="mt-1 text-[12px] text-gray-700">' + escapeHtml(data.comment.body) + '</p>';
                    listEl.appendChild(item);
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
    })();
</script>
