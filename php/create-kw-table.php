<?php
$COMMON_WORDS = ["the", "of", "and", "a", "to", "in", "is", "you", "that", "it", "he", "was", "for", "on", "are", "as", "with", "his", "they", "I", "at", "be", "this", "have", "from", "or", "one", "had", "by", "word", "but", "not", "what", "all", "were", "we", "when", "your", "can", "said", "there", "use", "an", "each", "which", "she", "do", "how", "their", "if", "will", "up", "other", "about", "out", "many", "then", "them", "these", "so", "some", "her", "would", "make", "like", "him", "into", "time", "has", "look", "two", "more", "write", "go", "see", "number", "no", "way", "could", "people", "my", "than", "first", "water", "been", "call", "who", "its", "now", "find", "long", "down", "day", "did", "get", "come", "made", "may", "part", " ", "ishydrated", 'sit'];
function createKWTable(array $kwArrOne, array $kwArrTwo)
{


  arsort($kwArrOne);
  $index = 0;
  echo "<tr>";
  echo "<th>Keyword</th>";
  echo "<th>Count</th>";
  echo "<th>Percentage</th>";
  echo "</tr>";
  foreach ($kwArrOne as $key => $value) {
    echo "<tr>";
    if ($index > 10) {
      break;
    }

    if (in_array($key, $GLOBALS['COMMON_WORDS'])) {
      continue;
    }
    if (!array_key_exists($key, $kwArrTwo)) {
      echo "<td> $key </td>";
      echo "<td> $value </td>";
      echo "<td> " . round(($value / array_sum($kwArrOne)) * 100, 2) . "% </td>";
    } elseif ($kwArrTwo[$key] < $value) {
      echo "<td style='background-color: #aeffae;'> $key </td>";
      echo "<td style='background-color: #aeffae;'> $value </td>";
      echo "<td style='background-color: #aeffae;'> " . round(($value / array_sum($kwArrOne)) * 100, 2) . "% </td>";
    } elseif ($kwArrTwo[$key] > $value) {
      echo "<td style='background-color: #ff9b9b;'> $key </td>";
      echo "<td style='background-color: #ff9b9b;'> $value </td>";
      echo "<td style='background-color: #ff9b9b;'> " . round(($value / array_sum($kwArrOne)) * 100, 2) . "% </td>";
    } else {
      echo "<td> $key </td>";
      echo "<td> $value </td>";
      echo "<td> " . round(($value / array_sum($kwArrOne)) * 100, 2) . "% </td>";
    }
    $index++;
    echo "</tr>";
  }
  return;
}
?>