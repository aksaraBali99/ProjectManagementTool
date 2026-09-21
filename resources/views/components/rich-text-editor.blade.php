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
--}}
@props(['name' => null, 'id' => null, 'value' => null, 'placeholder' => '', 'label' => 'Rich text', 'compact' => false, 'mentions' => null])

@php $html = \App\Support\RichText::toHtml($value); @endphp

<div
    {{ $attributes->class(['rte', 'rte-loading']) }}
    data-rich-text
    data-content="{{ $html }}"
    data-placeholder="{{ $placeholder }}"
    data-label="{{ $label }}"
    @if ($compact) data-compact @endif
    @if ($mentions) data-mentions="{{ json_encode($mentions) }}" @endif
>
    @if ($name)
        <input type="hidden" name="{{ $name }}" @if ($id) id="{{ $id }}" @endif value="{{ $html }}" data-rte-input>
    @endif
</div>
