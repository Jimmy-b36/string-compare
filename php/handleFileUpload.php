<?php
include 'vendor/autoload.php';
function handleFileUpload($file)
{
  $parser = new \Smalot\PdfParser\Parser();
  if (!isset($file['tmp_name'])) {
    return;
  }
  if ($file['error'] !== 0) {
    return 'No file selected or Error uploading file';
  } elseif (!in_array($file['type'], $GLOBALS['MIME_TYPES'])) {
    $output .= 'Invalid file type';
  } elseif ($file['size'] > 100000) {
    $output .= 'File size too large';
  } elseif ($file['type'] === 'application/pdf') {
    $pdf = $parser->parseFile($file['tmp_name']);
    $text = $pdf->getText();
    preg_replace('/\s+/', '', $text);
    return htmlspecialchars($text);
  } else {
    $data = file_get_contents($file['tmp_name']);
    return htmlspecialchars($data);
  }

}
?>