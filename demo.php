<?php
function diffArray(array $old, array $new): array
{
  // Create a hash table to store the indices of elements in the new array
  $newIndices = [];
  foreach ($new as $index => $value) {
    $newIndices[$value][] = $index;
  }

  // Initialize a matrix to store the lengths of the longest common subsequences
  $matrix = [];
  $maxlen = 0;

  // Iterate over each element in the old array
  foreach ($old as $oldIndex => $oldValue) {
    // If the current element is not in the new array, skip it
    if (!isset($newIndices[$oldValue])) {
      continue;
    }

    // Iterate over each index of the current element in the new array
    foreach ($newIndices[$oldValue] as $newIndex) {
      // If the length of the LCS between the previous elements is known, update the current length
      if (isset($matrix[$oldIndex - 1][$newIndex - 1])) {
        $matrix[$oldIndex][$newIndex] = $matrix[$oldIndex - 1][$newIndex - 1] + 1;
      } else {
        $matrix[$oldIndex][$newIndex] = 1;
      }

      // If the current length is greater than the maximum length, update the maximum length
      if ($matrix[$oldIndex][$newIndex] > $maxlen) {
        $maxlen = $matrix[$oldIndex][$newIndex];
        $oldMax = $oldIndex + 1 - $maxlen;
        $newMax = $newIndex + 1 - $maxlen;
      }
    }
  }

  // If there is no LCS, return the arrays with the "deleted" and "inserted" labels
  if ($maxlen == 0) {
    return [
      [
        'deleted' => $old,
        'inserted' => $new
      ]
    ];
  } else {
    // Otherwise, recursively call the function with the subarrays before and after the LCS
    return array_merge(
      $this->diffArray(array_slice($old, 0, $oldMax), array_slice($new, 0, $newMax)),
      array_slice($new, $newMax, $maxlen),
      $this->diffArray(array_slice($old, $oldMax + $maxlen), array_slice($new, $newMax + $maxlen))
    );
  }
}
?>