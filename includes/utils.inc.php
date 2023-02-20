<?php  

/**
 * A class that contains string utilities
 */
class StringUtils {
    private function __construct()
    {}

    /**
     * Checks if the haystack contains the needle
     * 
     * @param string $haystack The string where to search the needle
     * @param string $needle The string to find in the haystack
     * @return bool
     * @see https://stackoverflow.com/a/7112596
     */
    public static function contains(string $haystack, string $needle) : bool {
        return (strpos($haystack, $needle) !== false);
    }
    
    /**
     * Checks if the haystack starts with the needle
     * 
     * @param string $haystack The string where to search the needle
     * @param string $needle The string to find in the haystack
     * @return bool
     * @see https://stackoverflow.com/a/2790912
     */
    public static function startsWith(string $haystack, string $needle) : bool {
        return (strpos($haystack, $needle) === 0);
    }
}

?>