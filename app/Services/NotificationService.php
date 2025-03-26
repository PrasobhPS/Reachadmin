<?php

namespace App\Services;

use App\Models\Notification;
use Illuminate\Http\Request;
use Google\Client as GoogleClient;
use App\Models\FcmNotification;
use Illuminate\Support\Facades\Storage;

class NotificationService
{
    /**
     * Create and save a notification.
     *
     * @param string $message
     * @param int|null $employeeId
     * @param int|null $employerId
     * @param int|null $jobId
     * @param bool $isRead
     * @return Notification
     */
    public function createNotification(
        string $message,
        ?int $employeeId = null,
        ?int $jobId = null,
        ?int $notifiedBy = null,
        ?int $notifiedTo = null,
        ?string $url_keyword = null,
        $jobRole = null

    ): Notification {

        return Notification::create([
            'employee_id' => $employeeId,
            'job_id' => $jobId,
            'notified_by' => $notifiedBy,
            'notified_to' => $notifiedTo,
            'message' => $message,
            'url_keyword' => $url_keyword,
        ]);
    }

    /**
     * Notify when a chat message is sent.
     *
     * @param int $senderId
     * @param int $receiverId
     * @param string $message
     * @return Notification
     */

    public function new_notification(int $employeeId, int $jobId, int $notifiedBy, int $notifiedTo, $message, $url_keyword, $jobRole = null): Notification
    {
        // Create a job liked notification
        $notificationMessage = $message;

        $notification = $this->createNotification($notificationMessage, $employeeId, $jobId, $notifiedBy, $notifiedTo, $url_keyword);
        $this->sendFcmNotification($notifiedTo, $notificationMessage, $url_keyword, $jobId, $employeeId, $jobRole);
        return $notification;
    }
    /**
     * Send an FCM push notification.
     *
     * @param int $userId
     * @param string $message
     */
    private function sendFcmNotification(int $userId, string $message, string $url_keyword, string $jobId, string $employeeId, string $jobRole = null): void
    {


        $users = FcmNotification::where('member_id', $userId)
            ->where('is_login', 1)
            ->get();


        foreach ($users as $fcmNotifications) {
            $fcmToken = $fcmNotifications->token;

            // $fcmToken = 'do1QRCrEQUU1kT1ADp2knE:APA91bHCmX5imeu6YCBO7HO7ad78L-tgu32HSmjKD9vn34AA1MzB7lKfSnjb6O16hStGsihcyMFUe3EOyTfX-0nKGtbGx2omNfI9sHLLQhtDwnPJgvm3ta4';
            if (!$fcmToken) {

                // Exit if user does not have an FCM token
                return;
            }

            if ($url_keyword == 'Member' || $url_keyword == 'Specialist') {
                $title = 'Book A Call';
            } elseif ($url_keyword == 'Job' || $url_keyword == 'Employee') {
                $title = 'New Like';
            } else {
                $title = 'Interview';
            }
            $title = $title . ' Notification';
            $projectId = config('services.fcm.project_id');
            $credentialsFilePath = storage_path('app/json/reach-492da-firebase-adminsdk-51l6z-83a87c88f4.json');

            $client = new GoogleClient();
            $client->setAuthConfig($credentialsFilePath);
            $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
            $client->refreshTokenWithAssertion();
            $token = $client->getAccessToken();
            $access_token = $token['access_token'];

            $headers = [
                "Authorization: Bearer $access_token",
                'Content-Type: application/json',
            ];
            // $message = 'Captain Miller1 has booked an appointment with you on  at 1:00 PM';
            $data = [
                "message" => [
                    "token" => $fcmToken,
                    "notification" => [
                        "title" => $title,
                        "body" => $message,
                    ],
                    "data" => [
                        "url_keyword" => $url_keyword,
                        "job_id" => $jobId,
                        "employee_id" => $employeeId,
                        "job_role" => $jobRole,
                    ]
                ],
            ];

            $payload = json_encode($data);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send");
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            $response = curl_exec($ch);
            $err = curl_error($ch);

            curl_close($ch);

            if ($err) {
                echo json_encode([
                    'message' => "Curl Error: " . $err
                ]);
            } else {

                $responseData = json_decode($response, true);
                $responseData = json_decode($response, true);
                // Log success or handle response if needed
                json_encode([
                    'message' => "Success: " . json_encode($responseData)
                ]);
            }
            //return;
        }
    }


    public function sendAlertNotification(int $userId, string $message, string $url_keyword, string $jobId, string $employeeId, string $jobRole = null, $title = null): void
    {


        $users = FcmNotification::where('member_id', $userId)
            ->where('is_login', 1)
            ->get();


        foreach ($users as $fcmNotifications) {
            $fcmToken = $fcmNotifications->token;

            // $fcmToken = 'do1QRCrEQUU1kT1ADp2knE:APA91bHCmX5imeu6YCBO7HO7ad78L-tgu32HSmjKD9vn34AA1MzB7lKfSnjb6O16hStGsihcyMFUe3EOyTfX-0nKGtbGx2omNfI9sHLLQhtDwnPJgvm3ta4';
            if (!$fcmToken) {

                // Exit if user does not have an FCM token
                return;
            }

            $title = $title ?? 'New Update Available!';

            $projectId = config('services.fcm.project_id');
            $credentialsFilePath = storage_path('app/json/reach-492da-firebase-adminsdk-51l6z-83a87c88f4.json');

            $client = new GoogleClient();
            $client->setAuthConfig($credentialsFilePath);
            $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
            $client->refreshTokenWithAssertion();
            $token = $client->getAccessToken();
            $access_token = $token['access_token'];

            $headers = [
                "Authorization: Bearer $access_token",
                'Content-Type: application/json',
            ];
            // $message = 'Captain Miller1 has booked an appointment with you on  at 1:00 PM';
            $data = [
                "message" => [
                    "token" => $fcmToken,
                    "notification" => [
                        "title" => $title,
                        "body" => $message,
                    ],
                    "data" => [
                        "url_keyword" => $url_keyword,
                        "title" => $title,
                        "message" => $message,
                        "member_id" => (string)$userId,

                    ]
                ],
            ];

            $payload = json_encode($data);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send");
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            $response = curl_exec($ch);
            $err = curl_error($ch);

            curl_close($ch);

            if ($err) {
                echo json_encode([
                    'message' => "Curl Error: " . $err
                ]);
            } else {


                $responseData = json_decode($response, true);
                $logMessage = [
                    'message' => "Success: " . json_encode($responseData)
                ];
            }
            //return;
        }
    }
}
