<?php

function getUrlContents(string $url)
{
  if ($url == '') {
    return 'No URL selected or Error fetching URL data';
  }

  $site = file_get_contents("$url");
  $site = preg_match("/<body[^>]*>(.*?)<\/body>/is", $site, $matches) ? $matches[1] : $site;

  $dom = new DOMDocument;
  $dom->loadHTML($site);

  $xpath = new DOMXPath($dom);

  $headings = $xpath->query("//h1 | //h2 | //h3 | //h4 | //h5 | //h6");
  $paragraphs = $xpath->query("//p");

  $headings_text = '';
  $paragraphs_text = '';

  foreach ($headings as $heading) {
    $headings_text .= $heading->nodeValue . " ";
  }

  foreach ($paragraphs as $paragraph) {
    $paragraphs_text .= $paragraph->nodeValue . " ";
  }

  return $headings_text . " " . $paragraphs_text;
}

echo getUrlContents('https://onlinetextcompare.com/')
  ?>