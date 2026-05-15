<?php

namespace App\Support;

use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

class RichTextSanitizer
{
    public function sanitize(?string $html): string
    {
        if (! is_string($html) || trim($html) === '') {
            return '';
        }

        return $this->sanitizer()->sanitize($html);
    }

    private function sanitizer(): HtmlSanitizer
    {
        $config = (new HtmlSanitizerConfig())
            ->allowSafeElements()
            ->allowRelativeLinks()
            ->allowRelativeMedias()
            ->allowElement('rich-text-attachment', [
                'sgid',
                'content-type',
                'url',
                'href',
                'filename',
                'filesize',
                'width',
                'height',
                'previewable',
                'presentation',
                'caption',
            ])
            ->allowElement('figure', ['class'])
            ->allowElement('figcaption')
            ->allowElement('iframe', ['src', 'width', 'height', 'frameborder', 'allowfullscreen', 'allow', 'style', 'scrolling', 'title', 'loading'])
            ->allowAttribute('class', ['div', 'span', 'figure', 'rich-text-attachment'])
            ->allowAttribute('dir', ['div', 'p'])
            ->allowAttribute('lang', ['div', 'p', 'span'])
            ->withMaxInputLength(-1);

        return new HtmlSanitizer($config);
    }
}
