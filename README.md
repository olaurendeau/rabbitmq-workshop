# rabbitmq-workshop

## Installation

1. [Install docker](https://www.docker.com/products/overview#/install_the_platform) & [Install docker-compose](https://docs.docker.com/compose/install/)
2. Clone this repository somewhere `git clone git@github.com:olaurendeau/rabbitmq-workshop.git && cd rabbitmq-workshop`
3. Build containers `docker-compose build`
4. Run containers in background `docker-compose up -d`
5. Check if :
  * app is properly working at [http://localhost:4446/](http://localhost:4446/)
  * RabbitMQ management interface is available at [http://guest:guest@localhost:15672/#/queues](http://guest:guest@localhost:15672/#/queues)

## Workshop

### 1 - Asynchronize request

Create a queue `queue.document` and bind it to `amq.direct` exchange without routing_key

In `backend/api.php` instead of generating the invoice, publish the request as a json string on `amq.direct` exchange. Change result in response to `pending`. Using `backend/src/RabbitMQ/RabbitMQWrapper::publish` method should help.

Move InvoiceGenerator code to `backend/worker.php`.

Change `web/app.js` success message to something more "pending"

Run `docker-compose restart worker` to restart worker

### 2 - Fill the gap

Create a `backend/script.php` file generating 100 messages to `amq.direct` exchange.

Run `docker-compose run command php script.php` to produce 100 messages

Play with the mailer stoping and starting it `docker-compose stop mailer` / `docker-compose up -d mailer`

Scale workers `docker-compose scale worker=5`

### 3 - Handle failures

In `backend/worker.php` randomly fail the processor by sending an exception

Swarrot library propose a [retry processor](https://github.com/swarrot/swarrot/tree/master/src/Swarrot/Processor/Retry), replace the ExceptionCatcherProcessor with the RetryProcessor

Some queue & bindings definitions should be done

Run `docker-compose restart worker` to restart worker

### 4 - Dispatch events

In `backend/src/Logger/Logger.php` publish messages like `{"id": "1234", "message": "[18:21:26] afe4da94-55bf-4dd0-1a3c-e38e03dbb8ac [worker] Message processed"}` on exchange `amq.topic` with routing key `log.{application}`

Copy `backend/worker.php` to a `backend/log-catcher.php`, build a LogProcessor echoing the message, define a temporary queue with a unique name and a customizable binding with `backend/src/RabbitMQ/RabbitMQWrapper::createTemporaryQueue` and consume from that queue.

Run `docker-compose restart worker` to restart worker

Run `docker-compose run command bash` to ssh on command container

Play with `php log-catcher.php "#"`

### 5 - Push

In `backend/src/Logger/Logger.php` add `request_id` and `channel` in json payload

Have a look at `backend/pusher.php` shouldn't have much to do.

Run `docker-compose restart worker pusher` to restart worker & pusher

### 6 - Configuration

Have a look at https://github.com/olaurendeau/rabbit-mq-admin-toolkit-cli readme and define a `.rabbit-mq-admin-toolkit.yml` file defining our rabbitmq objects configuration

Run `docker-compose run command php rabbit-mq-admin-toolkit.phar define`
