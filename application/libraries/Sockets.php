

<?php
defined('BASEPATH') or exit('No direct script access allowed');
require  __DIR__ . '/../../vendor/autoload.php';

use WebSocket\Client;

class Sockets
{

    public function create_connection()
    {
        try {
            $serverAddress = 'ws://erickci.staging-server.online:8080';
            $client = new Client($serverAddress);
            
            
            
            // Convert message data to JSON
            $messageJson = json_encode([ "channel"=> 'notify',]);
            // Send a message to WebSocket server
            $client->send($messageJson);
           
         


            // echo "Message sent to WebSocket server:\n";

        } catch (Exception $e) {
            // echo "Error sending message to WebSocket server: " . $e->getMessage() . "\n";
        }
    }
    public function send_notification() {}
}

?>