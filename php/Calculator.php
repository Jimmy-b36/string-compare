<?php
$WORD_COUNT_OLD = 0;
$WORD_COUNT_NEW = 0;

class Calculator
{
    private $stylist;

    public function __construct($stylist)
    {
        $this->stylist = $stylist;
    }

    public function cleanString(string $string): array
    {
        $string = preg_replace('/[^A-Za-z0-9\- \n]/', '', $string);
        $string = preg_replace('/(--)/', ' ', $string);
        $string = preg_replace('/\s+/', ' ', $string);
        $stringArr = explode(' ', strtolower($string));
        for ($i = 0; $i < count($stringArr); $i++) {
            if (in_array($stringArr[$i], $GLOBALS['COMMON_WORDS'])) {
                unset($stringArr[$i]);
                $stringArr = array_values($stringArr);
            }
        }
        return $stringArr;
    }

    public function counter(string $old, string $new)
    {
        $GLOBALS['WORD_COUNT_OLD'] = str_word_count($old);
        $GLOBALS['WORD_COUNT_NEW'] = str_word_count($new);

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
        $countDoubleOld = array_count_values($chunkedOldArr);
        $countDoubleNew = array_count_values($chunkedNewArr);
        $countSingleOld = array_count_values($old);
        $countSingleNew = array_count_values($new);
        return array("KwSingleOriginal" => $countSingleOld, "KwSingleNew" => $countSingleNew, "KwDoubleOriginal" => $countDoubleOld, "KwDoubleNew" => $countDoubleNew);
    }

    public function diff(string $old, string $new): array
    {
        if (str_word_count($old) > 5000) {
            return [
                'old' => 'Original text is too long please submit 5000 words or less',
                'new' => $new
            ];
        }
        if (str_word_count($new) > 5000) {
            return [
                'old' => $old,
                'new' => 'New text is too long please submit 5000 words or less'
            ];
        }
        $diff = $this->diffArray(
            str_split($old),
            str_split($new)
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