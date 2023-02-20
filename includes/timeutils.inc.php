<?php  

class TimeUtils {

    private static $SECOND = 1;
    private static $MINUTE = 60;
    private static $HOUR = 60 * 60;
    private static $DAY = 60 * 60 * 24;
    private static $MONTH = 60 * 60 * 24 * 30;
    private static $YEAR = 60 * 60 * 24 * 30 * 12;

    /**
     * Converts human readable
     * time unit to seconds.
     * 
     * Time units:
     * [s -> seconds]
     * [m -> minutes]
     * [h -> hours]
     * [d -> days]
     * [mo -> months]
     * [y -> years]
     * 
     * @param string $time The time unit to convert to seconds
     * @return int
     */
    public static function toSeconds(string $time) : int {
        $matches = [];

        if(preg_match("/(\\d+)(s|m|h|d|mo|y)/", $time, $matches) == 1) {
            $number = intval($matches[1]);
            $unit = $matches[2];
            $seconds = 0;

            switch($unit) {
                case "s":
                    $seconds = $number * TimeUtils::$SECOND;
                break;

                case "m":
                    $seconds = $number * TimeUtils::$MINUTE;
                break;

                case "h":
                    $seconds = $number * TimeUtils::$HOUR;
                break;

                case "d":
                    $seconds = $number * TimeUtils::$DAY;
                break;

                case "mo":
                    $seconds = $number * TimeUtils::$MONTH;
                break;

                case "y":
                    $seconds = $number * TimeUtils::$YEAR;
                break;
            }

            return $seconds;
        }

        return -1;
    }

}

?>