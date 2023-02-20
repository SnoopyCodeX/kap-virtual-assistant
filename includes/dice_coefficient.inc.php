<?php  

class DiceCoefficient {

    /**
     * Computes similarities between 2
     * strings
     * 
     * @param string $string1 The first string
     * @param string $string2 The second string
     * @return float
     */
    public static function computeSimilarity(string $string1, string $string2) : float {
        if(empty($string1) || empty($string2))
            return 0;

        if($string1 === $string2)
            return 1;

        $strlen1 = strlen($string1);
        $strlen2 = strlen($string2);

        if($strlen1 < 2 || $strlen2 < 2)
            return 0;

        $matches = 0;
        $i = $j = 0;

        while($i < ($strlen1 - 1) && $j < ($strlen2 - 1)) {
            $a = substr($string1, $i, 2);
            $b = substr($string2, $j, 2);

            if(strcasecmp($a, $b) == 0)
                $matches += 2;

            ++$i;
            ++$j;
        }

        return $matches / (($strlen1 - 1) + ($strlen2 - 1));
    }

    /**
     * Computes the similarity of a string
     * from an array of strings
     * 
     * @param string $string1 The string to compare
     * @param array $strings2 The array of strings to compare to
     * @return array
     */
    public static function computeSimilarity2(string $string1, array $strings2) : array {
        $scores = array();

        foreach($strings2 as $string2)
            array_push($scores, [
                'content' => $string2, 
                'score' => self::computeSimilarity($string1, $string2)
            ]);

        return $scores;
    }

}

?>