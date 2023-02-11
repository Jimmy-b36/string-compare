<?php

class StringCompare
{
  /**
   * Returns both old and new text highlighting the differences between them.
   *
   * @param string $old
   * @param string $new
   *
   * @return array
   */
  public function stringDiff(string $old, string $new): array
  {
    $placeholder = '_NEWLINE_';

    // Check if text is too long
    if (str_word_count($old) > 10000 || str_word_count($new) > 10000) {
      return [
        'old' => 'Text is too long please submit 10000 words or less',
        'new' => 'Text is too long please submit 10000 words or less'
      ];
    }
    // Replace all line breaks with a placeholder
    $modifiedOld = str_replace("\r\n", $placeholder, $old);
    $modifiedNew = str_replace("\r\n", $placeholder, $new);

    // Remove all HTML tags
    $modifiedOld = strip_tags($modifiedOld);
    $modifiedNew = strip_tags($modifiedNew);

    // Split the strings into arrays, separating them by spaces
    $diff = $this->diffArray(
      preg_split("/[\s]+/", $modifiedOld),
      preg_split("/[\s]+/", $modifiedNew)
    );

    $oldString = '';
    $newString = '';
    // Loop through each array item and highlight the changes
    foreach ($diff as $item) {
      if (is_array($item)) {
        if ($item['deleted']) {
          $oldString .= "<del>" . (implode(' ', $item['deleted'])) . "</del> ";
        }
        if ($item['inserted']) {
          $newString .= "<ins>" . (implode(' ', $item['inserted'])) . "</ins> ";
        }
      } else {
        $oldString .= $item . ' ';
        $newString .= $item . ' ';
      }
    }

    // Replace the placeholder with line breaks
    $oldString = str_replace($placeholder, "<br/>", $oldString) . "<br/>";
    $newString = str_replace($placeholder, "<br/>", $newString) . "<br/>";
    return [
      'old' => $oldString,
      'new' => $newString
    ];
  }

  /**
   * Calculates the difference between two arrays using the "longest common
   * subsequence" algorithm.
   *
   * @param array $old
   *   Array of strings from the old text.
   * @param array $new
   *   Array of strings from the new text.
   *
   * @return array
   *   Array of strings containing the differences between the two arrays.
   */
  public function diffArray(array $old, array $new): array
  {

    // This is the matrix we will work with. It has the size of $old times $new.
    $matrix = array();

    // We need to know the maximum length of the differences in the matrix.
    $maxlen = 0;

    // Loop through the old array.
    foreach ($old as $oldIndex => $oldValue) {
      // Look for the same value in the new array.
      $newKeys = array_keys($new, $oldValue);
      foreach ($newKeys as $newIndex) {
        // If we have a match, check the diagonal cell. If it exists, add 1 to the
        // value. If it doesn't exist, it's 1.
        $matrix[$oldIndex][$newIndex] = isset($matrix[$oldIndex - 1][$newIndex - 1]) ? 
          $matrix[$oldIndex - 1][$newIndex - 1] + 1 : 1;

        // If the length is greater than the maximum length, remember it.
        if ($matrix[$oldIndex][$newIndex] > $maxlen) {
          $maxlen = $matrix[$oldIndex][$newIndex];
          $oldMax = $oldIndex + 1 - $maxlen;
          $newMax = $newIndex + 1 - $maxlen;
        }
      }
    }

    // If there is no match, return an array with the old and new texts.
    if ($maxlen == 0) {
      return array(array('deleted' => $old, 'inserted' => $new));
    }

    // If there is a match, return the differences.
    return array_merge(
      $this->diffArray(array_slice($old, 0, $oldMax), array_slice($new, 0, $newMax)),
      array_slice($new, $newMax, $maxlen),
      $this->diffArray(array_slice($old, $oldMax + $maxlen), array_slice($new, $newMax + $maxlen))
    );
  }
}
?>