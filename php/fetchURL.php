<?php



/**
 * Fetches the contents of a URL and returns the text content of the main-content element, the "main" element or the div elements
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
  $page = preg_replace('/[^A-Za-z0-9<\/>\-]/', ' ', $page);
  libxml_use_internal_errors(true);
  $dom = new DOMDocument();
  $dom->loadHTML($page);
  libxml_clear_errors();
  // remove all <script> elements from the DOM
  $scripts = $dom->getElementsByTagName('script');
  while ($scripts && $scripts->length > 0)
    $scripts->item(0)->parentNode->removeChild($scripts->item(0));
  // remove all <style> elements from the DOM
  $styles = $dom->getElementsByTagName('style');
  while ($styles && $styles->length > 0)
    $styles->item(0)->parentNode->removeChild($styles->item(0));

  // if the "main-content" element exists, get the text content of all its descendants
  $mainElement = $dom->getElementById('main-content');
  if ($mainElement) {
    $allElements = '';
    $descendants = $mainElement->getElementsByTagName('*');
    foreach ($descendants as $element) {
      $allElements .= $element->textContent . "\r\n";
    }
    return trim(htmlentities($allElements));
    // if the "main-content" element does not exist, get the text content of all the main elements
  } else {
    $mainTag = $dom->getElementsByTagName('main')->item(0);
    if ($mainTag) {
      $allElements = '';
      foreach ($mainTag->childNodes as $child) {
        $allElements .= $child->textContent . "\r\n";
      }
      return trim(htmlentities($allElements));
      // if the "main" element does not exist, get the text content of all the div elements
    } else {
      $divs = $dom->getElementsByTagName('div');
      $allElements = '';
      foreach ($divs as $div) {
        $allElements .= $div->textContent . "\r\n";
      }
      return trim(htmlentities($allElements));
    }
  }

}
?>