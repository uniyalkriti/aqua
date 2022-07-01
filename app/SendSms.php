<?php

namespace App;

use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\Model;

class SendSms extends Model
{
    public static function send_sms($custom_numbers,$custom_messages)
    {
    	// Account details
		$apiKey = urlencode('64hne6Ar9t4-k6TmqMJLL6mRI5R04RaFH6Nn5vKi0g');
		
		// Message details
		$numbers = array($custom_numbers);
		$sender = urlencode('mSELLG');
		$message = rawurlencode($custom_messages);
	 
		$numbers = implode(',', $numbers);
	 
		// Prepare data for POST request
		$data = array('apikey' => $apiKey, 'numbers' => $numbers, "sender" => $sender, "message" => $message);
	 
		// Send the POST request with cURL
		$ch = curl_init('https://api.textlocal.in/send/');
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		curl_close($ch);
		$response_decode = json_decode($response);
		// Process your response here

		return $response_decode;
		// echo $response;
		}
		

		public static function sendEMAIL($msg, $mailId,$subject)
    {
        $subject = !empty($subject)?$subject:'mSELL';
        $send=Mail::raw($msg, function ($message) use($mailId,$subject)
        {
          $message->to($mailId,$mailId)
            ->subject($subject);
        });
           
        return true;
    }
}
