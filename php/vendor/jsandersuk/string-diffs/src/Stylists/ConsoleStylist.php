<?php
declare(strict_types=1);

namespace JSandersUK\StringDiffs\Stylists;

use Bramus\Ansi\Ansi;
use Bramus\Ansi\ControlSequences\EscapeSequences\Enums\SGR;
use Bramus\Ansi\Writers\BufferWriter;

final class ConsoleStylist implements Stylist
{
    private $removedTextColour;
    private $removedBackgroundColour;
    private $insertedTextColour;
    private $insertedBackgroundColour;
    private $ansi;

    public function __construct(
        string $removedTextColour = SGR::COLOR_FG_WHITE,
        string $removedBackgroundColour = SGR::COLOR_BG_RED,
        string $insertedTextColour = SGR::COLOR_FG_WHITE,
        string $insertedBackgroundColour = SGR::COLOR_BG_GREEN
    ) {
        $this->removedTextColour = $removedTextColour;
        $this->removedBackgroundColour = $removedBackgroundColour;
        $this->insertedTextColour = $insertedTextColour;
        $this->insertedBackgroundColour = $insertedBackgroundColour;
        $this->ansi = new Ansi(new BufferWriter());
    }

    public function styleRemovedText(string $text): string
    {
        return $this->styleText($this->removedTextColour, $this->removedBackgroundColour, $text);
    }

    public function styleInsertedText(string $text): string
    {
        return $this->styleText($this->insertedTextColour, $this->insertedBackgroundColour, $text);
    }

    private function styleText(string $textColour, string $backgroundColour, $text): string
    {
        $ansi = $this->ansi;
        $ansi->color([$textColour, $backgroundColour]);
        $ansi->text($text);
        $ansi->nostyle();
        return $ansi->flush();
    }
}