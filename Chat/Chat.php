<?php

namespace Chat;

require (__DIR__) . '/../vendor/autoload.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Chat implements MessageComponentInterface
    {
        protected $clients;

        public function __construct()
        {
            $this->clients = new \SplObjectStorage;
            echo "Server started\n";
        }

        public function onOpen(ConnectionInterface $conn)
        {
            $this->clients->attach($conn);
            $id = uniqid();
            echo "New connection! ({$conn->resourceId})\n";
        }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $data = json_decode($msg, true);
        // var_dump($data);
        $receiverId = array_key_exists('id',$data) ?$data['id']:null;
        $sender_auth_id = array_key_exists('auth_id',$data)?$data['auth_id']:null;
        $message = $data['msg'];
        $channel = $data['channel'];
        foreach ($this->clients as $client) {
            $socketAttachedId = $this->clients[$client];
            // Check if the client's ID matches the target ID
            if ($from !== $client) {
                var_dump( $channel);
                if ($channel == 'msg') {
                   
                    if ($socketAttachedId['id'] == $receiverId) {
                        $client->send(json_encode($message) );
                        echo "Sending message \"{$message['content']}\" to {$receiverId} send from " . $sender_auth_id."\n";
                    }
                    if ($socketAttachedId['id'] == $sender_auth_id) {
                        $client->send(json_encode($message) );
                        echo "Sending message \"{$message['content']}\" to {$receiverId} send from " . $sender_auth_id."\n";
                    }
                }
                if($channel == "notify"){
                    $client->send(json_encode(["notify" =>true]) );
                }
                
            } 
            
          
            else {
                if ($channel == 'authAttachment') {
                    $this->associatingConnectionWithUserId($from, $sender_auth_id);
                }
                if ($channel == 'msg') {
                    var_dump( $channel);
                        $client->send( json_encode($message) );
                        echo "You send message \"{$message['content']}\" to {$receiverId} \n";
                }
            }
        }
    }
    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} has disconnected\n";
    }
    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }
    public function associatingConnectionWithUserId(ConnectionInterface $conn, $msg)
    {
        $this->clients->attach($conn, ['id' => $msg]);
        echo "Attaching {$conn->resourceId} Connection with Auth Id " . $msg."\n";
    }
}
