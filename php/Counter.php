<?php
class Counter
{
  public function cleanString(string $string): array
  {
    $COMMON_WORDS = ["the", "of", "and", "a", "to", "in", "is", "you", "that", "it", "he", "was", "for", "on", "are", "as", "with", "his", "they", "i", "at", "be", "this", "have", "from", "or", "one", "had", "by", "word", "but", "not", "what", "all", "were", "we", "when", "your", "can", "said", "there", "use", "an", "each", "which", "she", "do", "how", "their", "if", "will", "up", "other", "about", "out", "many", "then", "them", "these", "so", "some", "her", "would", "make", "like", "him", "into", "time", "has", "look", "two", "more", "write", "go", "see", "number", "no", "way", "could", "people", "my", "than", "first", "water", "been", "call", "who", "its", "now", "find", "long", "down", "day", "did", "get", "come", "made", "may", "part", " ", 'sit', "b", "c", "d", "e", "f", "g", "h", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z"];


    $string = preg_replace('/[^A-Za-z0-9\- \n]/', '', $string);
    $string = preg_replace('/(--)/', ' ', $string);
    $string = preg_replace('/\s+/', ' ', $string);
    $stringArr = explode(' ', strtolower($string));
    $newStrArr = array();
    for ($i = 0; $i < count($stringArr); $i++) {
      if (!in_array($stringArr[$i], $COMMON_WORDS)) {
        if (!is_numeric($stringArr[$i])) {
          array_push($newStrArr, $stringArr[$i]);
        }
      }
    }
    return $newStrArr;
  }





  public function counter(string $old, string $new): array
  {
    $old = $this->cleanString($old);
    $new = $this->cleanString($new);
    $chunkedOldArr = array();
    $chunkedNewArr = array();
    for ($i = 0; $i < count($old); $i++) {
      if (isset($old[$i + 1])) { // if next element exists
        array_push($chunkedOldArr, "{$old[$i]} {$old[$i + 1]}");
      }
    }
    for ($i = 0; $i < count($new); $i++) {
      if (isset($new[$i + 1])) { // if next element exists
        array_push($chunkedNewArr, "{$new[$i]} {$new[$i + 1]}");
      }
    }
    $countDoubleOld = array_reduce($chunkedOldArr, function ($carry, $item) {
      if (!isset($carry[$item])) {
        $carry[$item] = 0;
      }
      $carry[$item]++;
      return $carry;
    }, []);
    $countDoubleNew = array_reduce($chunkedNewArr, function ($carry, $item) {
      if (!isset($carry[$item])) {
        $carry[$item] = 0;
      }
      $carry[$item]++;
      return $carry;
    }, []);
    $countSingleOld = array_reduce($old, function ($carry, $item) {
      if (!isset($carry[$item])) {
        $carry[$item] = 0;
      }
      $carry[$item]++;
      return $carry;
    }, []);
    $countSingleNew = array_reduce($new, function ($carry, $item) {
      if (!isset($carry[$item])) {
        $carry[$item] = 0;
      }
      $carry[$item]++;
      return $carry;
    }, []);

    return array("KwSingleOriginal" => $countSingleOld, "KwSingleNew" => $countSingleNew, "KwDoubleOriginal" => $countDoubleOld, "KwDoubleNew" => $countDoubleNew);
  }
}
?>