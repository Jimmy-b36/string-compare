<?php
declare(strict_types=1);

namespace JSandersUK\StringDiffs\Stylists;

interface Stylist
{
    public function styleRemovedText(string $text): string;
    public function styleInsertedText(string $text): string;
}