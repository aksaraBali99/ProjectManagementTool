<?php

use App\Support\RichText;

test('editor output is recognised as HTML and ordinary prose is not', function (string $value, bool $expected) {
    expect(RichText::isHtml($value))->toBe($expected);
})->with([
    'paragraph' => ['<p>hello</p>', true],
    'empty editor' => ['<p></p>', true],
    'multiple blocks' => ['<h2>T</h2><p>a</p><ul><li><p>b</p></li></ul>', true],
    'code block' => ['<pre><code class="language-js">x</code></pre>', true],
    'surrounding whitespace' => ["  <p>hi</p>\n", true],
    'plain sentence' => ['just some words', false],
    'prose mentioning a tag' => ['use the <b> tag for bold', false],
    'html-ish but text before' => ['see <p>this</p>', false],
    'html-ish but text after' => ['<p>this</p> and more', false],
    'inline element only' => ['<strong>hi</strong>', false],
    'empty string' => ['', false],
    'multiline plain text' => ["line one\nline two", false],
]);

test('plain text becomes escaped paragraphs with line breaks preserved', function () {
    expect(RichText::fromPlainText("one\ntwo"))->toBe('<p>one<br>two</p>');
    expect(RichText::fromPlainText("one\n\ntwo"))->toBe('<p>one</p><p>two</p>');
    expect(RichText::fromPlainText("a\r\nb\r\n\r\nc"))->toBe('<p>a<br>b</p><p>c</p>');
    expect(RichText::fromPlainText('5 < 6 & <b>x</b>'))->toBe('<p>5 &lt; 6 &amp; &lt;b&gt;x&lt;/b&gt;</p>');
    expect(RichText::fromPlainText('   '))->toBe('');
});

test('toHtml handles every stored shape: null, blank, legacy text and editor HTML', function () {
    expect(RichText::toHtml(null))->toBe('');
    expect(RichText::toHtml(''))->toBe('');
    expect(RichText::toHtml("  \n "))->toBe('');
    expect(RichText::toHtml('Legacy text'))->toBe('<p>Legacy text</p>');
    expect(RichText::toHtml('<p>Editor <strong>html</strong></p>'))->toBe('<p>Editor <strong>html</strong></p>');
});

test('normalize nulls blanks, keeps plain text as-is, and sanitizes HTML', function () {
    expect(RichText::normalize(null))->toBeNull();
    expect(RichText::normalize(''))->toBeNull();
    expect(RichText::normalize('  '))->toBeNull();
    expect(RichText::normalize('<p></p>'))->toBeNull();
    expect(RichText::normalize('<p> </p><p></p>'))->toBeNull();
    expect(RichText::normalize('plain < text'))->toBe('plain < text');
    expect(RichText::normalize('<p onclick="x()">hi</p>'))->toBe('<p>hi</p>');
});

test('the sanitizer keeps exactly what the editor can produce', function () {
    $editorOutput = '<h1>A</h1><h2>B</h2><h3>C</h3>'
        .'<p><strong>b</strong><em>i</em><u>u</u>line<br>break</p>'
        .'<ul><li><p>x</p></li></ul><ol><li><p>y</p></li></ol>'
        .'<pre><code class="language-python">print(1)</code></pre>';

    $clean = RichText::sanitize($editorOutput);

    foreach (['<h1>A</h1>', '<h2>B</h2>', '<h3>C</h3>', '<strong>b</strong>', '<em>i</em>', '<u>u</u>', '<br', '<ul><li><p>x</p></li></ul>', '<ol><li><p>y</p></li></ol>', 'class="language-python"'] as $expected) {
        expect($clean)->toContain($expected);
    }
});

test('the sanitizer removes anything the editor cannot produce', function (string $dirty, string $mustNotContain) {
    expect(RichText::sanitize($dirty))->not->toContain($mustNotContain);
})->with([
    'script element' => ['<p>a</p><script>alert(1)</script>', '<script'],
    'inline handler' => ['<p onclick="x()">a</p>', 'onclick'],
    'javascript: href' => ['<p><a href="javascript:alert(1)">a</a></p>', 'javascript:'],
    'data: href' => ['<p><a href="data:text/html;base64,PHNjcmlwdD4=">a</a></p>', 'data:'],
    'relative href' => ['<p><a href="/admin/delete">a</a></p>', '/admin/delete'],
    'image' => ['<p><img src="https://x/y.png"></p>', '<img'],
    'iframe' => ['<p>a</p><iframe src="https://evil"></iframe>', '<iframe'],
    'style attribute' => ['<p style="position:fixed">a</p>', 'style='],
    'arbitrary code class' => ['<pre><code class="fixed inset-0">a</code></pre>', 'fixed'],
    'class on paragraph' => ['<p class="fixed inset-0">a</p>', 'class='],
]);

test('external links are forced to open safely', function () {
    $clean = RichText::sanitize('<p><a href="https://example.com">x</a></p>');

    expect($clean)->toContain('rel="noopener noreferrer nofollow"')->toContain('target="_blank"');
});

test('plainText extracts visible words with block boundaries as newlines', function () {
    expect(RichText::plainText('<p>Hello <strong>world</strong></p><p>Second &amp; last</p>'))->toBe("Hello world\nSecond & last");
    expect(RichText::plainText('<ul><li><p>one</p></li><li><p>two</p></li></ul>'))->toContain('one')->toContain('two');
    expect(RichText::plainText('<p></p>'))->toBe('');
    expect(RichText::plainText('legacy <b>text</b>'))->toBe('legacy <b>text</b>');
    expect(RichText::plainText(null))->toBe('');
});
