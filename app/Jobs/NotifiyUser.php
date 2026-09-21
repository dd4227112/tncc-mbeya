<?php

namespace App\Jobs;

use App\Models\Message;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class NotifiyUser implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public  int $messageId
    ) {}
    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $sender_name = config('message.sender_name');
        $api_key = config('message.api_key');
        $sms_url = config('message.sms_url');
        $message = Message::find($this->messageId);
        if(!$message) {
            Log::error("Message with ID {$this->messageId} not found.");
            return;
        }
        if (!$sender_name || !$api_key || !$sms_url) {
            Log::error("Missing SMS configuration. Please check your .env file.");
            return;
        }
        if ($message) {
            $postData = array(
                'from' => $sender_name,
                'to' => $message->phone,
                'text' => utf8_encode($message->body),
                'flash' => 0,
                'reference' => $message->reference
            );

            $curl = curl_init();

            curl_setopt_array(
                $curl,
                array(
                    CURLOPT_URL => $sms_url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => json_encode($postData),
                    CURLOPT_HTTPHEADER => array(
                        'Authorization:Bearer ' . $api_key,
                        'Content-Type: application/json',
                        'Accept: application/json'
                    ),
                )
            );

            $responses = curl_exec($curl);

            if ($responses) {

                $response = json_decode($responses, true);

                if (isset($response['success']) && $response['success'] == false) {
                    $status = 'sent';
                    // Message::where('phone', $phone_number)->where('otp', $otp)->update(['status' => "2"]);
                    // $message->update(['status' => 'sent', 'messageId' => $response['messageId']]);
                } else {
                    if ($response['messages'][0]['status']['groupName'] == 'PENDING') {
                        $status = 'pending';
                        // $message->update(['status' => 'pending', 'messageId' => $response['messages'][0]['messageId'], 'smsCount' => $response['messages'][0]['smsCount']]);
                    } else {
                        $status = 'failed';
                        // $message->update(['status' => 'failed', 'messageId' => $response['messages'][0]['sendReference'], 'smsCount' => $response['messages'][0]['smsCount']]);
                    }
                }
                $message->update(['status' => $status, 'response' => $responses]);
            }else{
                $message->update(['status' => 'failed', 'response' => curl_error($curl)]);
            }
        }
    }
}
