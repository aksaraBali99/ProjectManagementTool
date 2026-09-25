// task #70 phase 3: viewer-dependent @mention highlighting for RENDERED
// (read-only) comment/reply bodies.
//
// Deliberately separate from rich-text-editor.js's own setupMentions() —
// that's the LIVE EDITOR's autocomplete/tracking, operating on a TipTap
// document while composing. This module only ever touches already-saved,
// read-only markup (anything carrying `data-rich-text-content`), the same
// scope code-highlight.js/video-thumbnail.js/etc. enhance from
// highlightRichText() in app.js. It's also the one enhancement in that
// list whose result genuinely differs per viewer: whether a given mention
// gets the extra "this is you" treatment depends on who's currently
// logged in, not on the saved content itself (see current-user-id meta
// tag, layouts/authenticated.blade.php).
//
// Which names to look for comes from each element's own
// data-mentioned-users (Comment::mentionedUsers(), the durable,
// permission-checked pivot CommentController::syncMentions() writes at
// submit time) — never re-parsed from "@word"-looking text — so this
// never queries anything of its own; the mentioned-user list already
// travelled with the comment.
//
// Never mutates the value fed back into an editor when Edit is clicked —
// tasks/_comments.blade.php always seeds an edit session from
// dataset.rawHtml (the pristine RichText::toHtml() output, captured
// before this runs), never from .innerHTML, so a comment that's
// re-saved untouched can never pick up a stray <span class="mention"> in
// its stored body. Idempotent by construction rather than by a
// processed-marker attribute: a text node already inside a `.mention`
// span is skipped, so calling this again over content that was already
// highlighted (e.g. an ancestor scope covering an already-processed
// child) can't double-wrap it — a fresh call only ever finds fresh,
// unwrapped text, since every call site that changes a body's markup
// (buildBodyElement, restore(), syncComments()'s content-changed branch)
// replaces that element's innerHTML wholesale first.
//
// Known limitation, same one setupMentions()'s own text-based tracking
// already accepts: if a saved mention's "@Full Name" text is itself split
// across two DOM text nodes (e.g. only part of it was bolded), this won't
// find it. Not worth guarding against for plain "@Name" text with no real
// Mention node behind it.
function escapeRegExp(value) {
    return value.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}

export function highlightMentions(scope) {
    const target = scope || document;
    const currentUserId = Number(document.querySelector('meta[name="current-user-id"]')?.getAttribute('content'));

    target.querySelectorAll('[data-rich-text-content][data-mentioned-users]').forEach(function (bodyEl) {
        let mentionedUsers;
        try {
            mentionedUsers = JSON.parse(bodyEl.dataset.mentionedUsers || '[]');
        } catch (error) {
            mentionedUsers = [];
        }
        if (! mentionedUsers.length) return;

        // Longest name first, so mentioning "Jo" can't eat into an
        // unrelated occurrence of "@Joanne" elsewhere in the same body.
        const sorted = mentionedUsers.slice().sort(function (a, b) { return b.name.length - a.name.length; });
        const byName = {};
        sorted.forEach(function (user) { byName[user.name] = user; });
        const pattern = new RegExp('@(' + sorted.map(function (user) { return escapeRegExp(user.name); }).join('|') + ')(?![A-Za-z0-9])', 'g');

        const walker = document.createTreeWalker(bodyEl, NodeFilter.SHOW_TEXT, null);
        const textNodes = [];
        let node;
        while ((node = walker.nextNode())) textNodes.push(node);

        textNodes.forEach(function (textNode) {
            if (textNode.parentElement && textNode.parentElement.closest('.mention')) return;

            const text = textNode.nodeValue;
            pattern.lastIndex = 0;
            if (! pattern.test(text)) return;
            pattern.lastIndex = 0;

            const fragment = document.createDocumentFragment();
            let lastIndex = 0;
            let match;

            while ((match = pattern.exec(text))) {
                if (match.index > lastIndex) fragment.appendChild(document.createTextNode(text.slice(lastIndex, match.index)));

                const user = byName[match[1]];
                const span = document.createElement('span');
                span.className = 'mention' + (user && user.id === currentUserId ? ' mention-you' : '');
                span.textContent = match[0];
                fragment.appendChild(span);

                lastIndex = match.index + match[0].length;
            }

            if (lastIndex < text.length) fragment.appendChild(document.createTextNode(text.slice(lastIndex)));

            textNode.replaceWith(fragment);
        });
    });
}
