<?php

namespace App\Libraries;

use ElephantIO\Client;
use Exception;
use Illuminate\Support\Facades\Log;

class SocketIO
{
    /**
     * Send data to Socket.IO server
     *
     * @param string $event Event name to emit
     * @param array $data Data to send
     * @param string $socketUrl Socket.IO server URL
     * @param string $namespace Socket.IO namespace
     * @return bool Success status
     */
    public static function emit(string $event, array $data, string $socketUrl = null, string $namespace = '/'): bool
    {
        $socketUrl = $socketUrl ?? env('SOCKET_IO_URL', 'http://192.168.1.194:4000');

        try {
            // Initialize client
            $options = ['client' => Client::CLIENT_4X];
            $client = Client::create($socketUrl, $options);

            // Connect to server
            $client->connect();

            // Connect to namespace if not default
            if ($namespace !== '/') {
                $client->of($namespace);
            }

            // Emit event
            $client->emit($event, $data);

            // Disconnect
            $client->disconnect();

            return true;
        } catch (Exception $e) {
            // Log error but don't throw exception
            Log::error('Socket.IO error', [
                'event' => $event,
                'url' => $socketUrl,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Send member type update to Socket.IO server
     *
     * @param int $memberId Member ID
     * @param string $memberType Member type
     * @return bool Success status
     */
    public static function sendMemberTypeUpdate(int $memberId, string $memberType, string $isSpecialist): bool
    {
        $data = [
            'memberId' => $memberId,
            'memberType' => $memberType,
            'isSpecialist' => $isSpecialist
        ];

        return self::emit('changeMembership', $data);
    }
    public static function sendExpertUpdate(int $memberId, string $isSpecialist): bool
    {

        $data = [
            'memberId' => $memberId,
            'isSpecialist' => $isSpecialist
        ];

        return self::emit('updateSpecialist', $data);
    }
}
