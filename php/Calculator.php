<?php


class Calculator
{
    private $stylist;

    public function __construct($stylist)
    {
        $this->stylist = $stylist;
    }

    public function cleanString(string $string): array
    {
        $COMMON_WORDS = ["the", "of", "and", "a", "to", "in", "is", "you", "that", "it", "he", "was", "for", "on", "are", "as", "with", "his", "they", "I", "at", "be", "this", "have", "from", "or", "one", "had", "by", "word", "but", "not", "what", "all", "were", "we", "when", "your", "can", "said", "there", "use", "an", "each", "which", "she", "do", "how", "their", "if", "will", "up", "other", "about", "out", "many", "then", "them", "these", "so", "some", "her", "would", "make", "like", "him", "into", "time", "has", "look", "two", "more", "write", "go", "see", "number", "no", "way", "could", "people", "my", "than", "first", "water", "been", "call", "who", "its", "now", "find", "long", "down", "day", "did", "get", "come", "made", "may", "part", " ", 'sit'];


        $string = preg_replace('/[^A-Za-z0-9\- \n]/', '', $string);
        $string = preg_replace('/(--)/', ' ', $string);
        $string = preg_replace('/\s+/', ' ', $string);
        $stringArr = explode(' ', strtolower($string));
        for ($i = 0; $i < count($stringArr); $i++) {
            if (in_array($stringArr[$i], $COMMON_WORDS)) {
                unset($stringArr[$i]);
                $stringArr = array_values($stringArr);
            }
        }
        return $stringArr;
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

    public function diff(string $old, string $new): array
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
            str_split($modifiedOld),
            str_split($modifiedNew)
        );

        $oldString = '';
        $newString = '';
        foreach ($diff as $item) {

            if (is_array($item)) {
                if ($item['deleted']) {
                    $oldString .= $this->stylist->styleRemovedText(implode('', $item['deleted']));
                }
                if ($item['inserted']) {
                    $newString .= $this->stylist->styleInsertedText(implode('', $item['inserted']));
                }
            } else {
                $oldString .= $item;
                $newString .= $item;
            }
        }

        $oldString = str_replace($placeholder, "<br/>", $oldString) . "<br/>";
        $newString = str_replace($placeholder, "<br/>", $newString) . "<br/>";
        return [
            'old' => $oldString,
            'new' => $newString
        ];
    }

    private function diffArray(array $old, array $new): array
    {
        $newIndices = [];
        foreach ($new as $index => $value) {
            $newIndices[$value][] = $index;
        }

        $matrix = [];
        $maxlen = 0;
        foreach ($old as $oldIndex => $oldValue) {
            if (!isset($newIndices[$oldValue])) {
                continue;
            }

            foreach ($newIndices[$oldValue] as $newIndex) {
                if (isset($matrix[$oldIndex - 1][$newIndex - 1])) {
                    $matrix[$oldIndex][$newIndex] = $matrix[$oldIndex - 1][$newIndex - 1] + 1;
                } else {
                    $matrix[$oldIndex][$newIndex] = 1;
                }

                if ($matrix[$oldIndex][$newIndex] > $maxlen) {
                    $maxlen = $matrix[$oldIndex][$newIndex];
                    $oldMax = $oldIndex + 1 - $maxlen;
                    $newMax = $newIndex + 1 - $maxlen;
                }
            }
        }

        if ($maxlen == 0) {
            return [
                [
                    'deleted' => $old,
                    'inserted' => $new
                ]
            ];
        } else {
            return array_merge(
                $this->diffArray(array_slice($old, 0, $oldMax), array_slice($new, 0, $newMax)),
                array_slice($new, $newMax, $maxlen),
                $this->diffArray(array_slice($old, $oldMax + $maxlen), array_slice($new, $newMax + $maxlen))
            );
        }
    }

}
?>