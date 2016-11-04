<?php

namespace Pusher;

use Ratchet\ConnectionInterface;
use Ratchet\Wamp\WampServerInterface;
use Swarrot\Broker\Message;
use Swarrot\Broker\MessageProvider\MessageProviderInterface;

class Pusher implements WampServerInterface
{
    protected $subscribedChannels = array();
    protected $clients;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
    }

    public function onSubscribe(ConnectionInterface $conn, $channel) {
        $this->subscribedChannels[$channel->getId()] = $channel;
    }

    public function onPush(Message $message, MessageProviderInterface $messageProvider) {
        $push = json_decode($message->getBody(), true);

        echo "Received push on channel ".$push['channel']."\n";

        // If the lookup topic object isn't set there is no one to publish to
        if (array_key_exists($push['channel'], $this->subscribedChannels)) {

            $channel = $this->subscribedChannels[$push['channel']];

            // re-send the data to all the clients subscribed to that category
            $channel->broadcast($push);
            echo "Broadcast push\n";
        } else {
            echo "No channel for this push\n";
        }

        $messageProvider->ack($message);
    }

    public function onOpen(ConnectionInterface $conn)
    {
        // Store the new connection to send messages to later
        $this->clients->attach($conn);

        echo "New connection! ({$conn->resourceId})\n";
    }
    public function onUnSubscribe(ConnectionInterface $conn, $topic) {
    }
    public function onClose(ConnectionInterface $conn) {
    }
    public function onCall(ConnectionInterface $conn, $id, $topic, array $params) {
        // In this application if clients send data it's because the user hacked around in console
        $conn->callError($id, $topic, 'You are not allowed to make calls')->close();
    }
    public function onPublish(ConnectionInterface $conn, $topic, $event, array $exclude, array $eligible) {
        // In this application if clients send data it's because the user hacked around in console
        $conn->close();
    }
    public function onError(ConnectionInterface $conn, \Exception $e) {
    }
}
