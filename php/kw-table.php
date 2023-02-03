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