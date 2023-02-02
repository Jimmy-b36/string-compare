# String difference calculator

This package compares two strings and highlights any text that has been removed or inserted.

## Basic usage

To generate a "diff" between two strings you need to create an instance of the Calculator and provide it with a 
stylist - a class that deals with styling the removed and inserted text in order to highlight it to the end-user.

The example below is designed to output diffs to a console/terminal. See [Styling output](#styling-output) for information on how to change the output style. 

```php
$stylist = new JSandersUK\StringDiffs\Stylists\ConsoleStylist();
$calculator = new JSandersUK\StringDiffs\Calculator($stylist);

$styledDifferenceText = $calculator->diff($old, $new);
print $styledDifferenceText;
```

## Styling output

The removed and inserted text in a diff is styled before being returned in order to highlight the differences to the end-user. 

You will need to choose the relevant "stylist" class to match the environment where you will be displaying the diff.

### Outputting to a console/terminal

When printing a diff to a console you can use the _ConsoleStylist_ which uses ANSI escape sequences to colour the removed and inserted text. 

By default removed text is output as white on red and inserted text white on green. Colours can be configured by passing custom values to
the _constructor_ of the class.

#### Basic usage

```php
$stylist = new JSandersUK\StringDiffs\Stylists\ConsoleStylist();
```

#### Usage with custom colours

The package makes use of the _bramus/ansi-php_ package to colour the text and expects its constants to set colours.

**NOTE** the difference between foreground and background constants - COLOR_FG_WHITE vs COLOR_BG_WHITE; "FG" (foreground) and "BG" (background)

```php
$stylist = new JSandersUK\StringDiffs\Stylists\ConsoleStylist(
    Bramus\Ansi\ControlSequences\EscapeSequences\Enums\SGR::COLOR_FG_WHITE,
    Bramus\Ansi\ControlSequences\EscapeSequences\Enums\SGR::COLOR_BG_RED,
    Bramus\Ansi\ControlSequences\EscapeSequences\Enums\SGR::COLOR_FG_WHITE,
    Bramus\Ansi\ControlSequences\EscapeSequences\Enums\SGR::COLOR_BG_GREEN
);
```

### Outputting to HTML

When printing a diff to a browesr you can use the _HtmlStylist_ which wraps removed an inserted text in HTML elements with class applied. 

By default text is wrapped in a SPAN and removed text has a class of _removed-text_ applied and inserted text _inserted-text_. 
The HTML element to wrap text and the two classes can be configured by passing custom values to the _constructor_ of the class.

#### Basic usage

```php
$stylist = new JSandersUK\StringDiffs\Stylists\HtmlStylist();;
```

#### Usage with HTML classes and element

```php
$stylist = new JSandersUK\StringDiffs\Stylists\HtmlStylist(
    'old-text',
    'new-text',
    'div'
);
```
