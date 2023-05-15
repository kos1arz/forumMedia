<?php
namespace App\Websocket;
 
use Exception;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use SplObjectStorage;

class Chat implements MessageComponentInterface
{
    protected $clients;
 
    public function __construct()
    {
        $this->clients = new SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
    }
 
    public function onMessage(ConnectionInterface $from, $msg)
    {
        foreach($this->clients as $connection)
        {
            if($connection === $from)
            {
                continue;
            }
            $connection->send($msg);
        }
    }
 
    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
    }
 
    public function onError(ConnectionInterface $conn, Exception $e)
    {
        $this->clients->detach($conn);
        $conn->close();
    }
}