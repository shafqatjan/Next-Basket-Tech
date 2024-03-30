# docker-compose.yml
version: '3'
services:
 users:
    build: ./users
    ports:
      - "8080:80"
 notifications:
    build: ./notifications
    ports:
      - "8081:80"
 rabbitmq:
    image: "rabbitmq:3-management"
    ports:
      - "5672:5672"
      - "15672:15672"
