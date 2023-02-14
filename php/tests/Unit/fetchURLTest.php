<?php
require_once('fetchURL.php');
$EXAMPLE_CONTENTS = 'Example Domain     This domain is for use in illustrative examples in documents  You may use this     domain in literature without prior coordination or asking for permission      More information';
it("should return the contents of a valid URL", function () {
  $url = 'https://www.example.com';
  $result = getUrlContents($url);
  expect($result)->toEqual($GLOBALS['EXAMPLE_CONTENTS']);
});

it("should return 'Invalid URL' if the URL is invalid", function () {
  $url = 'https://example';
  $result = getUrlContents($url);
  expect($result)->toEqual('Invalid URL');
});

it("should return 'URL must start with http or https' if the URL doesn't start with http or https", function () {
  $url = 'www.example.com';
  $result = getUrlContents($url);
  expect($result)->toEqual('URL must start with http or https');
});

it("should return 'Error: error message' if the URL doesn't exist", function () {
  $url = 'https://www.example.com/404';
  $result = getUrlContents($url);
  expect($result)->toContain('Error: ');
});



?>