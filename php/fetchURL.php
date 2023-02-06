<?php


// This function gets the contents of a URL and returns it as a string
// The function checks if the <main> element exists, and if it does, it returns the contents of the <main> element
// If the <main> element doesn't exist, it returns the contents of the <body> element
// The function filters out all non-alphanumeric characters, including HTML tags

function getUrlContents(string $url): string
{
  $page = file_get_contents("$url");
  $page = preg_replace('/[^A-Za-z0-9<\/>\-]/', ' ', $page);

  // Check if <main> element exists
  if (strpos($page, '<main') !== false) {
    preg_match("/<main[^>]*>(.*?)<\/main>/is", $page, $matches);
    return preg_replace('/\s+/', ' ', strip_tags($matches[1]));
  }

  preg_match("/<body[^>]*>(.*?)<\/body>/is", $page, $matches);
  // If <main> element doesn't exist, return the entire page
  return preg_replace('/\s+/', ' ', strip_tags($matches[1]));
}


?>