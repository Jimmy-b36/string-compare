<?php


// This function gets the contents of a URL and returns it as a string
// The function checks if the <main> element exists, and if it does, it returns the contents of the <main> element
// If the <main> element doesn't exist, it returns the contents of the <body> element
// The function filters out all non-alphanumeric characters, including HTML tags

function getUrlContents(string $url): string
{
  $url = filter_var($url, FILTER_SANITIZE_URL);

  if (!filter_var($url, FILTER_VALIDATE_URL)) {
    return 'Invalid URL';
  }
  if (!preg_match('/^https?:\/\//', $url)) {
    return 'URL must start with http or https';
  }
  $url = strip_tags($url);

  try {
    $page = file_get_contents("$url");
  } catch (Exception $e) {
    return 'Error: ' . $e->getMessage();
  }

  $page = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $page);
  $page = preg_replace('/[^A-Za-z0-9<\/>\-]/', ' ', $page);

  libxml_use_internal_errors(true);
  $dom = new DOMDocument();
  $dom->loadHTML($page);
  libxml_clear_errors();

  $mainElement = $dom->getElementsByTagName('main')->item(0);
  if ($mainElement) {
    return htmlentities($mainElement->textContent);
  }

  $bodyElement = $dom->getElementsByTagName('body')->item(0);
  return htmlentities($bodyElement->textContent);
}

?>