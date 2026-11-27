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
// (redirect()->intended()). The page then finds the drafts and sends them.
//
// Generic on purpose, so other parts (subtasks, documents, reactions) can
// use it later. Only comments save drafts for now.
//
// Drafts live in localStorage, one entry each, keyed by user and task
// (solava:pending-comment:<user>:<task>:<id>), so two tabs saving at once
// don't overwrite each other. Every storage call is guarded: private
// windows and full storage throw, and then saving reports false so the
// caller can tell the user instead of redirecting.

const PREFIX = 'solava:pending-comment:';
const MAX_AGE_MS = 24 * 60 * 60 * 1000;

export function isSessionExpired(response) {
    return !! response && (response.status === 401 || response.status === 419);
}

function currentUserId() {
    const meta = document.querySelector('meta[name="current-user-id"]');
    return meta ? meta.getAttribute('content') : '';
}

function taskPrefix(taskId) {
    return PREFIX + currentUserId() + ':' + taskId + ':';
}

// Every storage key starting with `prefix`. Throws if storage is unavailable.
function keysStartingWith(prefix) {
    const storage = window.localStorage;
    const keys = [];
    for (let i = 0; i < storage.length; i++) {
        const name = storage.key(i);
        if (name && name.indexOf(prefix) === 0) keys.push(name);
    }
    return keys;
}

// A stored draft, or null if it can't be read, isn't ours, or is too old.
function readDraft(raw) {
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

/**
 * Saves the comment being sent when the session ran out. payload:
 * { action: 'create' | 'reply' | 'edit', body, mentioned_user_ids,
 *   parent_comment_id, comment_id }. Returns false if it couldn't be
 * stored (no user id, private window, storage full).
 */
export function savePendingComment(taskId, payload) {
    if (! currentUserId()) return false;
    const savedAt = Date.now();
    const id = savedAt + '-' + Math.random().toString(36).slice(2, 8);
    try {
        window.localStorage.setItem(taskPrefix(taskId) + id, JSON.stringify(Object.assign({}, payload, { v: 1, saved_at: savedAt })));
        return true;
    } catch (error) {
        return false;
    }
}

/**
 * This user's saved drafts for this task, oldest first. Reading them
 * removes them, so a reload can never send one twice. Drafts older than
 * 24 hours, or that can't be read, are dropped.
 */
export function takePendingComments(taskId) {
    if (! currentUserId()) return [];
    const drafts = [];
    try {
        keysStartingWith(taskPrefix(taskId)).forEach(function (name) {
            const draft = readDraft(window.localStorage.getItem(name));
            window.localStorage.removeItem(name);
            if (draft) drafts.push(draft);
        });
    } catch (error) {
        return [];
    }
    return drafts.sort(function (a, b) { return a.saved_at - b.saved_at; });
}

/**
 * Removes drafts older than 24 hours (or unreadable), whoever saved them,
 * so a comment whose author never signs back in doesn't stay on a shared
 * computer. Run on every page load.
 */
export function purgeExpiredPending() {
    try {
        keysStartingWith(PREFIX).forEach(function (name) {
            if (! readDraft(window.localStorage.getItem(name))) window.localStorage.removeItem(name);
        });
    } catch (error) {
        // Storage unavailable: nothing stored to purge.
    }
}

/** Removes every saved comment draft on this browser (on logout). */
export function clearAllPending() {
    try {
        keysStartingWith(PREFIX).forEach(function (name) { window.localStorage.removeItem(name); });
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
