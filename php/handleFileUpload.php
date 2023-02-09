<?php
include 'vendor/autoload.php';

/**
 * This function takes in a file extracts the text content and returns it as a string.
 * @param mixed $file
 * @return string
 */
function handleFileUpload($file)
{
  $MIME_TYPES = ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/msword', 'text/plain', 'application/vnd.oasis.opendocument.text', 'application/msword', 'application/pdf'];
  $parser = new \Smalot\PdfParser\Parser();
  if (!isset($file['tmp_name']) || $file['error'] !== 0) {
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
    return htmlspecialchars($text, ENT_HTML5, 'UTF-8');
  }
  if ($file['type'] === 'application/msword') {
    $text = read_file_docx($file['tmp_name']);
    return htmlspecialchars($text, ENT_HTML5, 'UTF-8');
  }
  if ($file['type'] === 'application/vnd.oasis.opendocument.text') {
    $text = read_file_odt($file['tmp_name']);
    $text = preg_replace('/<(.*?)>/', ' ', $text);
    $text = preg_replace('/\t+/', '', $text);
    return htmlspecialchars($text, ENT_HTML5, 'UTF-8');
  }
  if ($file['type'] === 'text/plain') {
    try {
      $text = file_get_contents($file['tmp_name']);
      return htmlspecialchars($text, ENT_HTML5, 'UTF-8');
    } catch (Exception $e) {
      return 'Error reading file';
    }
  }
  if ($file['type'] === 'application/pdf') {
    $pdf = $parser->parseFile($file['tmp_name']);
    $text = $pdf->getText();
    $text = preg_replace('/\t+/', '', $text);
    return htmlspecialchars($text, ENT_HTML5, 'UTF-8');
  }
  try {
    $text = file_get_contents($file['tmp_name']);
    return htmlspecialchars($text, ENT_HTML5, 'UTF-8');
  } catch (Exception $e) {
    return 'Error reading file';
  }

}

/**
 * This function takes in a docx file and returns the text content as a string.
 * @param mixed $filename
 * @return bool|string
 */
function read_file_docx($filename)
{
  // Create new ZIP archive
  $zip = new ZipArchive;
  // Variable to store the contents of the document.xml file
  $striped_content = '';
  // Variable to store the contents of the entire document
  $content = '';
  // Check if the file exists
  if (!$filename || !file_exists($filename))
    return false;
  // Open the document as a ZIP file
  if ($zip->open($filename) === TRUE) {
    // Get the contents of the document.xml file
    if ($zip_entry = $zip->getStream("word/document.xml")) {
      // Store the contents of the document.xml file in the $content variable
      $content = stream_get_contents($zip_entry);
      // Close the file
      fclose($zip_entry);
    }
    // Close the ZIP file
    $zip->close();
  } else {
    return false;
  }
  // Replace any instances of </w:r></w:p></w:tc><w:tc> in the document with a space
  $content = str_replace('</w:r></w:p></w:tc><w:tc>', " ", $content);
  // Replace any instances of </w:r></w:p> in the document with a new line
  $content = str_replace('</w:r></w:p>', "\r\n", $content);
  // Strip out any HTML tags
  $striped_content = strip_tags($content);
  // Return the stripped content
  return $striped_content;
}

/**
 * This function takes in a odt file and returns the text content as a string.
 * @param mixed $filename
 * @return bool|string
 */
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
  return "Failed to open the file";
}
?>