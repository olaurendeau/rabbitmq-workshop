# rabbitmq-workshop

## Installation

1. [Install docker](https://www.docker.com/products/overview#/install_the_platform)
2. Clone this repository somewhere `git clone git@github.com:olaurendeau/rabbitmq-workshop.git && cd rabbitmq-workshop`
3. Build containers `docker-compose build`
4. Run containers `docker-compose up`
5. Check if :
  * app is properly working at [http://localhost:4444/](http://localhost:4444/)
  * RabbitMQ management interface is available at [http://guest:guest@localhost:15672/#/queues](http://guest:guest@localhost:15672/#/queues)
