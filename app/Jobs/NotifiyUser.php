<?php

namespace App\Jobs;

use App\Models\Message;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

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
        $api_key = env('SMS_API_KEY');
        $secret_key = env('SMS_SECRET_KEY');
        $message = Message::find($this->messageId);
        if ($message) {
            $postData = array(
                'from' => 'HUKUEVENTS',
                'to' => $message->phone,
                'text' => utf8_encode($message->body),
                'reference' => 'HUKUEVENTS'
            );

            $curl = curl_init();

            curl_setopt_array(
                $curl,
                array(
                    CURLOPT_URL => 'https://messaging-service.co.tz/api/sms/v1/text/single',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => json_encode($postData),
                    CURLOPT_HTTPHEADER => array(
                        'Authorization:Basic ' . base64_encode("$api_key:$secret_key"),
                        'Content-Type: application/json',
                        'Accept: application/json'
                    ),
                )
            );

            $responses = curl_exec($curl);

            curl_close($curl);
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
            }
        }
    }
}
