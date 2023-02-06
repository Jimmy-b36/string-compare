<?php


/**
 * Creates table of keywords from two keyword arrays
 *
 * @param array $kwArrOne First array of keywords
 * @param array $kwArrTwo Second array of keywords
 *
 * @return null
 */
function createKWTable(array $kwArrOne, array $kwArrTwo): void
{
  if (empty($kwArrOne) || empty($kwArrTwo)) {
    echo "No Keywords Found";
    return;
  }
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