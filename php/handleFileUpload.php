<?php
include 'vendor/autoload.php';
function handleFileUpload($file)
{
  $MIME_TYPES = ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/msword', 'text/plain', 'application/vnd.oasis.opendocument.text', 'application/msword', 'application/pdf'];
  $parser = new \Smalot\PdfParser\Parser();
  if (!isset($file['tmp_name'])) {
    return;
  }
  if ($file['error'] !== 0) {
    return 'No file selected or Error uploading file';
  }

  if (!in_array($file['type'], $MIME_TYPES)) {
    return 'File type not supported';
  }
  if ($file['size'] > 100000000) {
    return 'File size too large';
  }
  if ($file['type'] === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
    $text = read_file_docx($file['tmp_name']);
    return htmlspecialchars($text);
  }
  if ($file['type'] === 'application/msword') {
    $text = read_file_docx($file['tmp_name']);
    return htmlspecialchars($text);
  }
  if ($file['type'] === 'application/vnd.oasis.opendocument.text') {
    $text = read_file_odt($file['tmp_name']);
    $text = preg_replace('/<(.*?)>/', ' ', $text);
    $text = preg_replace('/\t+/', '', $text);
    return htmlspecialchars($text);
  }
  if ($file['type'] === 'text/plain') {
    $data = file_get_contents($file['tmp_name']);
    return htmlspecialchars($data);
  }
  if ($file['type'] === 'application/pdf') {
    $pdf = $parser->parseFile($file['tmp_name']);
    $text = $pdf->getText();
    $text = preg_replace('/\t+/', '', $text);
    return htmlspecialchars($text);
  }
  $data = file_get_contents($file['tmp_name']);
  return htmlspecialchars($data);
}


function read_file_docx($filename)
{
  $zip = new ZipArchive;
  $striped_content = '';
  $content = '';
  if (!$filename || !file_exists($filename))
    return false;
  if ($zip->open($filename) === TRUE) {
    if ($zip_entry = $zip->getStream("word/document.xml")) {
      $content = stream_get_contents($zip_entry);
      fclose($zip_entry);
    }
    $zip->close();
  } else {
    return false;
  }
  $content = str_replace('</w:r></w:p></w:tc><w:tc>', " ", $content);
  $content = str_replace('</w:r></w:p>', "\r\n", $content);
  $striped_content = strip_tags($content);
  return $striped_content;
}
function read_file_odt($filename)
{
  $dataFile = "content.xml";
  //Create a new ZIP archive object
  $zip = new ZipArchive;

  // Open the archive file
  if (true === $zip->open($filename)) {
    // If successful, search for the data file in the archive
    if (($index = $zip->locateName($dataFile)) !== false) {
      // Index found! Now read it to a string
      $text = $zip->getFromIndex($index);
      // Load XML from a string
      // Ignore errors and warnings
      $xml = new DOMDocument;
      $xml->loadXML($text, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
      // Return XML
      return $xml->saveXML();
    }
    //Close the archive file
    $zip->close();
  }
  // In case of failure return a message
  return "File no`enter code here`t found";
}
?>