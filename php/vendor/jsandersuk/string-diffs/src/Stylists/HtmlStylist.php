<?php
declare(strict_types=1);

namespace JSandersUK\StringDiffs\Stylists;

final class HtmlStylist implements Stylist
{
    private $removedSpanClass;
    private $insertedSpanClass;
    private $wrappingHtmlElement;

    public function __construct(
        string $removedSpanClass = 'removed-text',
        string $insertedSpanClass = 'inserted-text',
        string $wrappingHtmlElement = 'span'
    ) {
        $this->removedSpanClass = $removedSpanClass;
        $this->insertedSpanClass = $insertedSpanClass;
        $this->wrappingHtmlElement = $wrappingHtmlElement;
    }

    public function styleRemovedText(string $text): string
    {
        return $this->styleText($this->removedSpanClass, $text);
    }

    public function styleInsertedText(string $text): string
    {
        return $this->styleText($this->insertedSpanClass, $text);
    }

    private function styleText(string $class, $text): string
    {
        $format = '<%s class="%s">%s</%s>';
        return sprintf($format, $this->wrappingHtmlElement, $class, $text, $this->wrappingHtmlElement);
    }
}