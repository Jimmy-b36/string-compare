<?php

function getUrlContents(string $url)
{
  if ($url == '') {
    return 'No URL selected or Error fetching URL data';
  }

  $site = file_get_contents("$url");
  $site = preg_replace('/[^A-Za-z0-9<\/>\-]/', ' ', $site);

  // Check if <main> element exists
  if (strpos($site, '<main') !== false) {
    preg_match("/<main[^>]*>(.*?)<\/main>/is", $site, $matches);
    echo preg_replace('/\s+/', ' ', strip_tags($matches[1]));
  } else {
    preg_match("/<body[^>]*>(.*?)<\/body>/is", $site, $matches);
    // If <main> element doesn't exist, return the entire page
    return preg_replace('/\s+/', ' ', strip_tags($matches[1]));
  }
}

?>