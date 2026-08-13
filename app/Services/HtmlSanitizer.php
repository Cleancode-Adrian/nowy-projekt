<?php

namespace App\Services;

use DOMAttr;
use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXPath;

/**
 * Czyści HTML pochodzący z edytora WYSIWYG oraz z generatora AI, zanim trafi
 * do bazy i zostanie wyrenderowany przez {!! !!}.
 *
 * Działa na allowliście: cokolwiek nie jest jawnie dozwolone, jest usuwane.
 * Korzysta wyłącznie z rozszerzenia ext-dom (standard w PHP), więc nie wymaga
 * dodatkowych zależności composera.
 */
class HtmlSanitizer
{
    /** Tagi dozwolone w treści. */
    private const ALLOWED_TAGS = [
        'p', 'br', 'hr', 'span', 'div',
        'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
        'strong', 'b', 'em', 'i', 'u', 's', 'del', 'ins', 'mark', 'small', 'sub', 'sup',
        'ul', 'ol', 'li', 'dl', 'dt', 'dd',
        'blockquote', 'pre', 'code', 'figure', 'figcaption',
        'a', 'img',
        'table', 'thead', 'tbody', 'tfoot', 'tr', 'th', 'td', 'caption', 'colgroup', 'col',
    ];

    /** Atrybuty dozwolone per tag. '*' obowiązuje dla wszystkich tagów. */
    private const ALLOWED_ATTRIBUTES = [
        '*' => ['class', 'id', 'title', 'dir', 'lang'],
        'a' => ['href', 'target', 'rel'],
        'img' => ['src', 'alt', 'width', 'height', 'loading', 'decoding'],
        'td' => ['colspan', 'rowspan', 'headers'],
        'th' => ['colspan', 'rowspan', 'scope', 'headers'],
        'col' => ['span'],
        'colgroup' => ['span'],
        'ol' => ['start', 'reversed', 'type'],
        'blockquote' => ['cite'],
    ];

    /**
     * Elementy usuwane razem z zawartością - nie wystarczy zdjąć samego taga.
     */
    private const STRIPPED_WITH_CONTENT = [
        'script', 'style', 'iframe', 'frame', 'frameset', 'object', 'embed',
        'applet', 'form', 'input', 'button', 'select', 'textarea', 'option',
        'link', 'meta', 'base', 'noscript', 'svg', 'math', 'template',
    ];

    private const ALLOWED_URL_SCHEMES = ['http', 'https', 'mailto', 'tel'];

    public function clean(?string $html): string
    {
        $html = (string) $html;

        if (trim($html) === '') {
            return '';
        }

        $document = new DOMDocument('1.0', 'UTF-8');

        $previous = libxml_use_internal_errors(true);

        // Wrapper + meta charset: bez tego DOMDocument psuje polskie znaki.
        $loaded = $document->loadHTML(
            '<?xml encoding="UTF-8"><div id="__sanitizer_root__">' . $html . '</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NONET
        );

        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        if (!$loaded) {
            // Nie udało się sparsować - bezpieczniej zwrócić czysty tekst.
            return e(strip_tags($html));
        }

        $xpath = new DOMXPath($document);

        // getElementById() po loadHTML() bywa zawodne (zależy od DTD),
        // więc szukamy wrappera przez XPath.
        $root = $xpath->query('//div[@id="__sanitizer_root__"]')?->item(0);

        if (!$root instanceof DOMElement) {
            return e(strip_tags($html));
        }

        $this->removeComments($xpath, $root);
        $this->removeForbiddenElements($xpath, $root);
        $this->unwrapUnknownElements($root);
        $this->cleanAttributes($xpath, $root);

        return trim($this->innerHtml($document, $root));
    }

    /**
     * Wersja tekstowa - do zajawek, meta description i wyszukiwarki.
     */
    public function toPlainText(?string $html, int $limit = 0): string
    {
        $text = trim(html_entity_decode(
            strip_tags($this->clean($html)),
            ENT_QUOTES | ENT_HTML5,
            'UTF-8'
        ));

        $text = (string) preg_replace('/\s+/u', ' ', $text);

        return $limit > 0 ? \Illuminate\Support\Str::limit($text, $limit) : $text;
    }

    private function removeComments(DOMXPath $xpath, DOMElement $root): void
    {
        foreach (iterator_to_array($xpath->query('.//comment()', $root) ?: []) as $comment) {
            $comment->parentNode?->removeChild($comment);
        }
    }

    private function removeForbiddenElements(DOMXPath $xpath, DOMElement $root): void
    {
        $query = './/' . implode('|.//', self::STRIPPED_WITH_CONTENT);

        foreach (iterator_to_array($xpath->query($query, $root) ?: []) as $node) {
            $node->parentNode?->removeChild($node);
        }
    }

    /**
     * Tagi spoza allowlisty tracą znacznik, ale zachowują tekst w środku -
     * dzięki temu nietypowe formatowanie nie kasuje treści wpisu.
     */
    private function unwrapUnknownElements(DOMElement $root): void
    {
        $changed = true;

        while ($changed) {
            $changed = false;

            foreach (iterator_to_array($root->getElementsByTagName('*')) as $element) {
                if (!$element instanceof DOMElement) {
                    continue;
                }

                if (in_array(strtolower($element->nodeName), self::ALLOWED_TAGS, true)) {
                    continue;
                }

                $parent = $element->parentNode;

                if (!$parent instanceof DOMNode) {
                    continue;
                }

                while ($element->firstChild) {
                    $parent->insertBefore($element->firstChild, $element);
                }

                $parent->removeChild($element);
                $changed = true;
            }
        }
    }

    private function cleanAttributes(DOMXPath $xpath, DOMElement $root): void
    {
        foreach (iterator_to_array($xpath->query('.//*', $root) ?: []) as $element) {
            if (!$element instanceof DOMElement) {
                continue;
            }

            $tag = strtolower($element->nodeName);
            $allowed = array_merge(
                self::ALLOWED_ATTRIBUTES['*'],
                self::ALLOWED_ATTRIBUTES[$tag] ?? []
            );

            foreach (iterator_to_array($element->attributes ?? []) as $attribute) {
                if (!$attribute instanceof DOMAttr) {
                    continue;
                }

                $name = strtolower($attribute->nodeName);

                // Zdejmuje onclick, onerror, onload i całą resztę handlerów.
                if (!in_array($name, $allowed, true) || str_starts_with($name, 'on')) {
                    $element->removeAttribute($attribute->nodeName);
                    continue;
                }

                if (in_array($name, ['href', 'src', 'cite'], true)
                    && !$this->isSafeUrl($attribute->nodeValue)) {
                    $element->removeAttribute($attribute->nodeName);
                }
            }

            // Linki otwierane w nowej karcie bez rel="noopener" dają dostęp do window.opener.
            if ($tag === 'a' && $element->getAttribute('target') === '_blank') {
                $element->setAttribute('rel', 'noopener noreferrer');
            }
        }
    }

    private function isSafeUrl(?string $url): bool
    {
        $url = trim((string) $url);

        if ($url === '') {
            return false;
        }

        // Ścieżki względne i kotwice są bezpieczne.
        if (str_starts_with($url, '/') || str_starts_with($url, '#')) {
            return true;
        }

        // "java\nscript:" i podobne obejścia - normalizujemy przed sprawdzeniem.
        $normalized = strtolower((string) preg_replace('/[\s\x00-\x1F]+/', '', $url));

        if (str_starts_with($normalized, 'data:image/')) {
            return true;
        }

        foreach (self::ALLOWED_URL_SCHEMES as $scheme) {
            if (str_starts_with($normalized, $scheme . ':')) {
                return true;
            }
        }

        // Brak schematu = URL względny (np. "blog/wpis").
        return !str_contains($normalized, ':');
    }

    private function innerHtml(DOMDocument $document, DOMElement $root): string
    {
        $html = '';

        foreach ($root->childNodes as $child) {
            $html .= $document->saveHTML($child);
        }

        return $html;
    }
}
