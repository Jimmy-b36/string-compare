<?php
require_once('StringCompare.php');
it("should return both old and new text with no changes if they're the same", function () {
  $stringCompare = new StringCompare();
  $old = 'This is a test';
  $new = 'This is a test';
  $result = $stringCompare->stringDiff($old, $new);
  expect($result)->toEqual([
    'old' => 'This is a test <br/>',
    'new' => 'This is a test <br/>'
  ]);
});

it("should return the two strings with the differences highlighted", function () {
  $stringCompare = new StringCompare();
  $old = 'This is a test';
  $new = 'This is a test with changes';
  $result = $stringCompare->stringDiff($old, $new);
  expect($result)->toEqual([
    'old' => 'This is a test <br/>',
    'new' => 'This is a test <ins>with changes</ins> <br/>'
  ]);
});

it("should return text is too long if the 'old' text is over 10000 words", function () {
  $stringCompare = new StringCompare();
  $old = 'This';
  for ($i = 0; $i < 10000; $i++) {
    $old .= ' is a test';
  }
  $new = 'This is a test with changes';
  $result = $stringCompare->stringDiff($old, $new);
  expect($result)->toEqual([
    'old' => 'Text is too long please submit 10000 words or less',
    'new' => 'Text is too long please submit 10000 words or less'
  ]);
});

it("should return text is too long if the 'new' text is over 10000 words", function () {
  $stringCompare = new StringCompare();
  $old = 'This';
  $new = 'This';
  for ($i = 0; $i < 10000; $i++) {
    $new .= ' is a test';
  }
  $result = $stringCompare->stringDiff($old, $new);
  expect($result)->toEqual([
    'old' => 'Text is too long please submit 10000 words or less',
    'new' => 'Text is too long please submit 10000 words or less'
  ]);
});

it("should return text with line breaks and no changes", function () {
  $stringCompare = new StringCompare();
  $old = "This is a test \r\n This is a test";
  $new = "This is a test";
  $result = $stringCompare->stringDiff($old, $new);
  expect($result)->toEqual([
    'old' => "This is a test <del><br/> This is a test</del> <br/>",
    'new' => 'This is a test <br/>'
  ]);
});
?>