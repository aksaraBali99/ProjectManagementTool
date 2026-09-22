{{--
    Read-only rendering of a stored description/comment body. Accepts legacy
    plain text or editor HTML (RichText::toHtml() sanitizes the latter and
    HTML-escapes the former), so {!! !!} below only ever prints markup that
    already went through the allowlist. Saved code blocks get their syntax
    colors from app.js (data-rich-text-content). $empty is shown, as plain
    text, when there's nothing to render.
--}}
@props(['value' => null, 'empty' => ''])

@php $html = \App\Support\RichText::toHtml($value); @endphp

@if ($html !== '')
    <div {{ $attributes->class(['rich-text']) }} data-rich-text-content>{!! $html !!}</div>
@else
    <p {{ $attributes }}>{{ $empty }}</p>
@endif
