// SOL-0022: what a page does when the login session has expired under it.
//
// An AJAX request from a page whose session has ended gets 401
// ("Unauthenticated.", the auth middleware answering JSON) or 419 ("CSRF
// token mismatch.", the session was replaced, e.g. by logging out in
// another tab, so the page's token no longer matches). Both mean: sign in
// again. Rather than a sign-in popup (Google sign-in is a full-page OAuth
// redirect, and a JSON login endpoint would be one more thing to secure
// and rate-limit), the page saves what the user was sending, then reloads
// itself: signed out, the auth middleware sends it to /login and
// remembers the page, and both sign-in methods return there
// (redirect()->intended()). The page then finds the draft and sends it.
//
// Generic on purpose, so other parts (subtasks, documents, reactions) can
// use it later. Only comments save a draft for now.
//
// Drafts live in localStorage, one per user and task. Every storage call
// is guarded: private windows and full storage throw, and then saving
// reports false so the caller can tell the user instead of redirecting.

const PREFIX = 'solava:pending-comment:';
const MAX_AGE_MS = 24 * 60 * 60 * 1000;

export function isSessionExpired(response) {
    return !! response && (response.status === 401 || response.status === 419);
}

function currentUserId() {
    const meta = document.querySelector('meta[name="current-user-id"]');
    return meta ? meta.getAttribute('content') : '';
}

function key(taskId) {
    return PREFIX + currentUserId() + ':' + taskId;
}

/**
 * Saves the comment being sent when the session ran out. payload:
 * { action: 'create' | 'reply' | 'edit', body, mentioned_user_ids,
 *   parent_comment_id, comment_id }. Returns false if it couldn't be
 * stored (no user id, private window, storage full).
 */
export function savePendingComment(taskId, payload) {
    if (! currentUserId()) return false;
    try {
        window.localStorage.setItem(key(taskId), JSON.stringify(Object.assign({}, payload, { v: 1, saved_at: Date.now() })));
        return true;
    } catch (error) {
        return false;
    }
}

/**
 * The saved draft for this user and task, or null. Reading it removes it,
 * so a reload can never send it twice. Drafts older than 24 hours, or that
 * can't be read, are dropped.
 */
export function takePendingComment(taskId) {
    if (! currentUserId()) return null;
    let raw = null;
    try {
        raw = window.localStorage.getItem(key(taskId));
        if (raw === null) return null;
        window.localStorage.removeItem(key(taskId));
    } catch (error) {
        return null;
    }

    let draft = null;
    try {
        draft = JSON.parse(raw);
    } catch (error) {
        return null;
    }
    if (! draft || draft.v !== 1 || typeof draft.body !== 'string') return null;
    if (! draft.saved_at || Date.now() - draft.saved_at > MAX_AGE_MS) return null;

    return draft;
}

/** Removes every saved comment draft on this browser (on logout). */
export function clearAllPending() {
    try {
        const storage = window.localStorage;
        const keys = [];
        for (let i = 0; i < storage.length; i++) {
            const name = storage.key(i);
            if (name && name.indexOf(PREFIX) === 0) keys.push(name);
        }
        keys.forEach(function (name) { storage.removeItem(name); });
    } catch (error) {
        // Nothing stored, or storage unavailable: nothing to clear.
    }
}

/**
 * Reloads this page. Signed out, the server sends it to /login and
 * remembers this URL as the one to return to after signing in. Linking to
 * /login directly would skip that.
 */
export function redirectToSignIn() {
    window.location.assign(window.location.href);
}
