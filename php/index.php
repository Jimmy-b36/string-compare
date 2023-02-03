<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Document</title>
  <link rel="stylesheet" href="stringCompare.css" />
</head>

<body>
  <?php
  ini_set('memory_limit', '-1');
  $MIME_TYPES = ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'text/plain', 'application/vnd.oasis.opendocument.text', 'application/msword', 'application/pdf'];
  function debug_to_console($str, $data)
  {
    $output = $data;
    if (is_array($output))
      $output = implode(',', $output);

    echo "<script>console.log('Debug Objects: " . $str . " " . $output . "' );</script>";
  } ?>

  <div class="header">
    <h1>Text Compare - Plagiarism Check Between Two Documents</h1>
  </div>

  <div class="container"><button id="toggle-button" class="upload-button">Upload file</button></div>


  <div id="URL-form">
    <form action="index.php" method="post">
      <div class="upload-forms">
        <div class="URL-input-container--left">
          <label for="original-text-url">Original text url:</label>
          <input type="text" name="original-text-url" id="original-text-url" class="input-box">
        </div>
        <input type="submit" value="Submit URL" class="upload-button" />
        <div class="URL-input-container--right">
          <label for="new-text-url">New text url:</label>
          <input type="text" name="new-text-url" id="new-text-url" class="input-box">
        </div>
      </div>
      <div class="container">

      </div>
    </form>
  </div>
  <div id="file-upload-form" class="hidden">
    <form action="index.php" method="post" enctype="multipart/form-data">
      <div class="upload-forms">
        <div class="file-input-container">
          <label for="originalFileToUpload">Select original file to upload:</label>
          <input type="file" name="originalFileToUpload" id="fileToUpload">
        </div>
        <input type="submit" value="Upload Text" name="submit" class="upload-button">
        <div class="file-input-container">
          <label for="newFileToUpload">Select new file to upload:</label>
          <input type="file" name="newFileToUpload" id="fileToUpload">
        </div>

      </div>
    </form>
  </div>

  <script>
    const toggleButton = document.getElementById('toggle-button');
    const URLForm = document.getElementById('URL-form');
    const fileUploadForm = document.getElementById('file-upload-form');
    toggleButton.addEventListener('click', () => {
      URLForm.classList.toggle('hidden');
      URLForm.classList.toggle('upload-forms')
      fileUploadForm.classList.toggle('hidden');
      fileUploadForm.classList.toggle('upload-forms')
      toggleButton.innerHTML = URLForm.classList.contains('hidden') ? 'Enter URL' : 'Upload file';
    });
  </script>

  <?php
  require_once 'fetchURL.php';
  include 'vendor/autoload.php';
  $parser = new \Smalot\PdfParser\Parser();
  $originalTextarea = '<textarea name="original-text" class="input-box" id="original-text" cols="75" rows="30">';
  $newTextarea = '<textarea name="new-text" class="input-box" id="new-text" cols="75" rows="30">';
  // <!-- URL upload logic -->
  if (isset($_POST["original-text-url"])) {
    if ($_POST["original-text-url"] == '') {
      $originalTextarea .= 'No URL selected or Error fetching URL data';
    } else {
      $originalTextarea .= getUrlContents($_POST["original-text-url"]);
    }
  }
  if (isset($_POST["new-text-url"])) {
    if ($_POST["new-text-url"] == '') {
      $newTextarea .= 'No URL selected or Error fetching URL data';
    } else {
      $newTextarea .= getUrlContents($_POST["new-text-url"]);
    }
  }

  // <!-- Original file upload logic -->
  if (isset($_FILES['originalFileToUpload']['tmp_name'])) {
    if ($_FILES['originalFileToUpload']['error'] !== 0) {
      $originalTextarea .= 'No file selected or Error uploading file';
    } elseif (!in_array($_FILES['originalFileToUpload']['type'], $MIME_TYPES)) {
      $originalTextarea .= 'Invalid file type';
    } elseif ($_FILES['originalFileToUpload']['size'] > 1000000) {
      $originalTextarea .= 'File size too large';
    } elseif ($_FILES['originalFileToUpload']['type'] === 'application/pdf') {
      $pdf = $parser->parseFile($_FILES['originalFileToUpload']['tmp_name']);
      $text = $pdf->getText();
      $originalTextarea .= htmlspecialchars($text);
    } else {
      $data = file_get_contents($_FILES['originalFileToUpload']['tmp_name']);
      $originalTextarea .= htmlspecialchars($data);
    }
  }
  // <!-- New file upload logic -->
  if (isset($_FILES['newFileToUpload']['tmp_name'])) {
    if ($_FILES['newFileToUpload']['error'] !== 0) {
      $newTextarea .= 'No file selected or Error uploading file';
    } elseif (!in_array($_FILES['newFileToUpload']['type'], $MIME_TYPES)) {
      $newTextarea .= 'Invalid file type';

    } elseif ($_FILES['newFileToUpload']['size'] > 1000000) {
      $newTextarea .= 'File size too large';
    } elseif ($_FILES['newFileToUpload']['type'] === 'application/pdf') {
      $pdf = $parser->parseFile($_FILES['newFileToUpload']['tmp_name']);
      $text = $pdf->getText();
      preg_replace('/\s+/', '', $text);
      $newTextarea .= htmlspecialchars($text);
    } else {
      $data = file_get_contents($_FILES['newFileToUpload']['tmp_name']);
      $newTextarea .= htmlspecialchars($data);
    }
  }

  // Keep original text in textarea after submit
  if (isset($_POST["original-text"])) {
    $originalTextarea .= $_POST["original-text"];
  }
  if (isset($_POST["new-text"])) {
    $newTextarea .= $_POST["new-text"];
  }
  $newTextarea .= '</textarea>';
  $originalTextarea .= '</textarea>';
  ?>


  <form action="index.php" method="POST" id="string-new-form" class="input-container">
    <div class="outer-input-container">
      <div class='input-container'>
        <label for="original-text">Please enter original text:</label>
        <?= $originalTextarea ?>
      </div>
      <div class="input-container">
        <label for="new-text">Please enter new text:</label>
        <?= $newTextarea ?>
      </div>
    </div>
    <input type="submit" value="Compare texts" class="submit-button" />
  </form>


  <div class="container">
    <?php
    require_once 'Calculator.php';
    require_once 'HtmlStylist.php';
    // String difference calculator
    $stylist = new HtmlStylist();
    $calculator = new Calculator($stylist);
    if (isset($_POST["original-text"])) {
      $original_text = $_POST["original-text"];
      $new_text = $_POST["new-text"];
      if ($original_text == '' || $new_text == '') {
        $result = [
          'old' => 'No text entered',
          'new' => 'No text entered'
        ];
      } else {
        $result = $calculator->diff($original_text, $new_text);
      }
    }
    ?>
    <div class="str-results">
      <?php if (isset($result["old"])) {
        echo $result["old"];
      } ?>
    </div>
    <div class="str-results">
      <?php if (isset($result["new"])) {
        echo $result["new"];
      } ?>
    </div>
  </div>

  <div class="kw-container--outer">
    <div class="kw-container--inner">
      <!-- KW density calculators -->
      <div class="kw-container">
        KW Density original-text:
        <table border='1'>
          <?php
          require 'create-kw-table.php';
          if (isset($_POST["original-text"])) {
            $original_text = $_POST["original-text"];
            $new_text = $_POST["new-text"];
            $kw = $calculator->counter($original_text, $new_text);
            createKWTable($kw["KwSingleOriginal"], $kw["KwSingleNew"]);
          }
          ?>
        </table>
      </div>
      <div class="kw-container kw-container--hidden">
        KW Density original-text:
        <table border='1'>
          <?php
          if (isset($_POST["original-text"])) {
            $original_text = $_POST["original-text"];
            $new_text = $_POST["new-text"];
            $kw = $calculator->counter($original_text, $new_text);
            createKWTable($kw["KwDoubleOriginal"], $kw["KwDoubleNew"]);
          }
          ?>
        </table>
      </div>


      <button id="kw-changer" class="upload-button">Double</button>

      <div class="kw-container">
        KW Density new-text:
        <table border="1">
          <?php
          if (isset($_POST["original-text"])) {
            $original_text = $_POST["original-text"];
            $new_text = $_POST["new-text"];
            $kw = $calculator->counter($original_text, $new_text);
            createKWTable($kw["KwSingleNew"], $kw["KwSingleOriginal"]);
          }
          ?>
        </table>
      </div>
      <div class="kw-container kw-container--hidden">
        KW Density new-text:
        <table border="1">
          <?php
          if (isset($_POST["original-text"])) {
            $original_text = $_POST["original-text"];
            $new_text = $_POST["new-text"];
            $kw = $calculator->counter($original_text, $new_text);
            createKWTable($kw["KwDoubleNew"], $kw["KwDoubleOriginal"]);
          }
          ?>
        </table>
      </div>
    </div>
    <div class="wc-container">
      <p>
        Original text word count:
        <?php if (isset($GLOBALS['WORD_COUNT_OLD'])) {
          echo $GLOBALS['WORD_COUNT_OLD'];
        } ?>
      </p>
      <p>
        New text word count:
        <?php if (isset($GLOBALS['WORD_COUNT_NEW'])) {
          echo $GLOBALS['WORD_COUNT_NEW'];
        } ?>
      </p>
    </div>
    <script>
      const kwButton = document.getElementById('kw-changer');
      kwButton.addEventListener('click', function () {
        let kwTables = document.getElementsByClassName('kw-container');
        kwButton.innerHTML = kwButton.innerHTML === 'Single' ? 'Double' : 'Single';
        for (var i = 0; i < kwTables.length; i++) {
          kwTables[i].classList.toggle('kw-container--hidden');
        }
      });
    </script>
  </div>
</body>


</html>