<?php
namespace App\Helper;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Http\Response;

class Notification{

    public static function send(){
        $data = [
            "notification" => [
                "body"  => "Laravel push notification",
                "title" => "Firebase Notification",
                "image" => ""
            ],
            "priority" =>  "high",
            "data" => [
                "click_action"  =>  "WEB_NOTIFICATION_CLICK",
                "id"            =>  "1",
                "status"        =>  "done",
                "info"          =>  [
                    "title"  => "Firebase Notification",
                    "link"   => "",
                    "image"  => ""
                ]
            ],
            "to" => "dC6rIQcX0YJv6ucOo9wGk1:APA91bFgG_wmiqy7DGSqc1roEV_uEQ6CAa4jnlUmgYDB7xcghq-aQXKAFAZBr2lK9tbfT_9VthPGf8cIjdxioE2h2WDLDeVhR0ofSPMb0iFOAA3b62L5_n83PPLra3D1UoMIy8S1QfNl"
        ];
        $ch = curl_init();
        $url = 'https://fcm.googleapis.com/fcm/send';
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: key=AAAAS5fmpX4:APA91bGWPsdApIRw8Ku6lwA33UhiqEDRXjbJKiWuU5XOSFE0l5wNxR0htMjDlvooNu3S2IKauapB2Co4UrG70oRkj8BtdCBJAA6mwA0EwO219QZ5N8559YcXd5CmTfv5ackrsT5uWcBk';

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);        
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $result = curl_exec($ch);
        //curl_close ($ch);
        
        return $result;
    }

    public static function subscribeToTopic($topicName, $registrationToken)
    {
        $url = $registrationToken . "/rel/topics/" . $topicName;

        $headers = [
            'Authorization: key='.config('app.SERVER_API_KEY'),
            'Content-Type: application/json',
            'project_id: '.config('app.PROJECT_ID') 
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://iid.googleapis.com/iid/v1/' . $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        return $response;
    }

    public static function sendNotification($data, $topicName = null)
    {
        $url = 'https://fcm.googleapis.com/fcm/send';
        $data = [
            'to' => '/topics/' . $topicName,
            'notification' => [
                'body' => $data['body'] ?? 'Something',
                'title' => $data['title'] ?? 'Something',
                'image' => $data['image'] ?? null,
            ],
            'data' => [
                'url' => $data['url'] ?? null,
                'redirect_to' => $data['redirect_to'] ?? null,
            ],
            'apns' => [
                'payload' => [
                    'aps' => [
                        'mutable-content' => 1,
                    ],
                ],
                'fcm_options' => [
                    'image' => $data['image'] ?? null,
                ], 
            ],
        ];

        self::execute($url, $data);
    }

    private function execute($url, $dataPost = [], $method = 'POST')
    {
        $result = false;
        try {
            $client = new Client();
            $result = $client->request($method, $url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'key=' . env('SERVER_API_KEY'),
                ],
                'json' => $dataPost,
                'timeout' => 300,
            ]);

            $result = $result->getStatusCode() == Response::HTTP_OK;
        } catch (Exception $e) {
            Log::debug($e);
        }

        return $result;
    }
}