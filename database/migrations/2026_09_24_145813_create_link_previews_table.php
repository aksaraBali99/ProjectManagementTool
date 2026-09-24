<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Smart Links (task #4) — a global, app-wide cache keyed by URL, not by
 * task or organization: the same https://example.com/article pasted into
 * two completely unrelated tasks by two different users is the exact same
 * row, fetched once. This is deliberately NOT tenant-scoped data (no
 * organization_id, no BelongsToOrganization) — it's a fetch cache, not
 * content anyone owns, closer in spirit to a DNS cache than to a Document.
 *
 * Read at PASTE time only (LinkPreviewService::resolve()), never at
 * render/view time — the fetched title/image/domain a paste actually used
 * gets baked into that specific description/comment's own saved HTML
 * (link-preview-extension.js's plain attributes), so a later refresh of
 * this cache row never silently changes what an already-saved chip shows.
 * This table's only job is avoiding a redundant external fetch (or Google
 * Drive API call) the next time the SAME url is pasted anywhere.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('link_previews', function (Blueprint $table) {
            $table->id();
            // sha256 of the normalized URL — the URL itself can be
            // arbitrarily long (some Google Docs URLs run past 200
            // chars), so it's kept in its own column rather than indexed
            // directly.
            $table->string('url_hash', 64)->unique();
            $table->text('url');
            $table->string('title')->nullable();
            $table->text('image_url')->nullable();
            $table->string('domain')->nullable();
            $table->timestamp('fetched_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('link_previews');
    }
};
