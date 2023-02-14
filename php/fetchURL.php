<?php



/**
 * Fetches the contents of a URL and returns the text content of the main element or the div elements
 *
 * @param string $url
 * @return string
 */
function getUrlContents(string $url): string
{
  $url = filter_var($url, FILTER_SANITIZE_URL);
  if (!preg_match('/^https?:\/\//', $url)) {
    return 'URL must start with http or https';
  }
  if (!filter_var($url, FILTER_VALIDATE_URL)) {
    return 'Invalid URL';
  }
  if (!preg_match('/(https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|www\.[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9]+\.[^\s]{2,}|www\.[a-zA-Z0-9]+\.[^\s]{2,})/', $url)) {
    return 'Invalid URL';
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
  $allElements = '';
  $headerElement = $dom->getElementsByTagName('header');
  foreach ($headerElement as $element) {
    $allElements .= $element->textContent;
  }
  $divElement = $dom->getElementsByTagName('div');
  foreach ($divElement as $element) {
    $allElements .= $element->textContent;
  }
  return trim(htmlentities($allElements));
}
?>