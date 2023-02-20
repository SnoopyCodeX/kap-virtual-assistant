<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/" . constant("ROOT_ASSETS_DIR") . "/twilio/src/Twilio/autoload.php");

use Twilio\Rest\Client;
use Twilio\Exceptions\TwilioException;

class SMSUtil {
    
    /**
     * Sends sms text using Twilio SMS API
     * 
     * @param string $number The number of the recepient
     * @param string $message The sms message to be sent
     * @return string The SID token
     */
    public static function sendSms(string $number, string $message) : ?string {
        $sid    = constant("TWILIO_SID_TOKEN"); 
        $token  = constant("TWILIO_AUTH_TOKEN"); 
        $twilio = new Client($sid, $token); 
        
        try {
            $messageResponse = $twilio->messages->create(
                    $number, 
                    [
                        "messagingServiceSid" => constant("TWILIO_MESSAGING_SID_TOKEN"),      
                        "body" => $message
                    ]
                );
            
            return $messageResponse->sid;
        } catch(TwilioException $e) {
            if(!constant("IS_PRODUCTION_MODE"))
                echo $e->getMessage();

            return null;
        }
    }

}

?>