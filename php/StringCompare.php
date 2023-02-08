<?php

class StringCompare
{
  public function stringDiff(string $old, string $new): array
  {
    $placeholder = '_NEWLINE_';

    if (str_word_count($old) > 5000 || str_word_count($new) > 5000) {
      return [
        'old' => 'Text is too long please submit 5000 words or less',
        'new' => 'Text is too long please submit 5000 words or less'
      ];
    }
    $modifiedOld = str_replace("\r\n", $placeholder, $old);
    $modifiedNew = str_replace("\r\n", $placeholder, $new);
    $modifiedOld = strip_tags($modifiedOld);
    $modifiedNew = strip_tags($modifiedNew);

    $diff = $this->diffArray(
      preg_split("/[\s]+/", $modifiedOld),
      preg_split("/[\s]+/", $modifiedNew)
    );

    $oldString = '';
    $newString = '';
    foreach ($diff as $item) {
      if (is_array($item)) {
        if ($item['deleted']) {
          $oldString .= "<del>" . (implode(' ', $item['deleted'])) . "</del>";
        }
        if ($item['inserted']) {
          $newString .= "<ins>" . (implode(' ', $item['inserted'])) . "</ins>";
        }
      } else {
        $oldString .= $item . ' ';
        $newString .= $item . ' ';
      }
    }

    $oldString = str_replace($placeholder, "<br/>", $oldString) . "<br/>";
    $newString = str_replace($placeholder, "<br/>", $newString) . "<br/>";
    return [
      'old' => $oldString,
      'new' => $newString
    ];
  }

  public function diffArray($old, $new)
  {
    $matrix = array();
    $maxlen = 0;

    foreach ($old as $oldIndex => $oldValue) {
      $newKeys = array_keys($new, $oldValue);
      foreach ($newKeys as $newIndex) {
        $matrix[$oldIndex][$newIndex] = isset($matrix[$oldIndex - 1][$newIndex - 1]) ? 
          $matrix[$oldIndex - 1][$newIndex - 1] + 1 : 1;
        if ($matrix[$oldIndex][$newIndex] > $maxlen) {
          $maxlen = $matrix[$oldIndex][$newIndex];
          $oldMax = $oldIndex + 1 - $maxlen;
          $newMax = $newIndex + 1 - $maxlen;
        }
      }
    }
    if ($maxlen == 0) {
      return array(array('deleted' => $old, 'inserted' => $new));
    }
    return array_merge(
      $this->diffArray(array_slice($old, 0, $oldMax), array_slice($new, 0, $newMax)),
      array_slice($new, $newMax, $maxlen),
      $this->diffArray(array_slice($old, $oldMax + $maxlen), array_slice($new, $newMax + $maxlen))
    );
  }
}
?>