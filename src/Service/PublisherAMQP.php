<?php

namespace App\Service;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class PublisherAMQP
{
    private $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    private function getAMQPConnection()
    {
        $host = $this->params->get('app.amqp_host');
        $port = $this->params->get('app.amqp_port');
        $user = $this->params->get('app.amqp_user');
        $password = $this->params->get('app.amqp_password');
        $vhost = $user;

        return new AMQPStreamConnection($host, $port, $user, $password, $vhost);
    }

    public function publishMessage(string $messageContent)
    {
        $connection = $this->getAMQPConnection();
        $exchange = $this->params->get('app.amqp_exchange');
        $queue = $this->params->get('app.amqp_queue');

        $channel = $connection->channel();
        $channel->queue_declare($queue, false, true, false, false);
        $channel->exchange_declare($exchange, AMQPExchangeType::DIRECT, false, true, false);
        $channel->queue_bind($queue, $exchange);

        $messageBody = $messageContent;
        $message = new AMQPMessage($messageBody, [
            'content_type' => 'application/json',
            'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT
        ]);
        $channel->basic_publish($message, $exchange);
        $channel->close();
        $connection->close();
    }
}
