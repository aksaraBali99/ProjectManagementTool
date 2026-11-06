{{--
    task #124 follow-up: a note's text (App\Support\ReleaseNotes::blocks()):
    "- " lines as one bullet list, other lines as paragraphs with their
    line breaks. Every piece is printed with {{ }}: never HTML.
    Expects $text.
--}}
@foreach (\App\Support\ReleaseNotes::blocks($text) as $block)
    @if ($block['type'] === 'ul')
        <ul class="list-disc space-y-1 pl-5 marker:text-gray-400">
            @foreach ($block['lines'] as $line)
                <li>{{ $line }}</li>
            @endforeach
        </ul>
    @else
        <p>@foreach ($block['lines'] as $line){{ $line }}@if (! $loop->last)<br>@endif @endforeach</p>
    @endif
@endforeach
