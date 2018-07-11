# Logging
Shopsys Framework is Symfony based application that uses [Monolog](https://github.com/Seldaek/monolog)
with [symfony/monolog-bundle](https://github.com/symfony/monolog-bundle) as logging tool.
Now it stores cron, slow and other logs into `project-base/var/logs/` directory in which the log files are rotated based
on date and count of each log file.

Shopsys Framework runs in environment that is built of [docker containers](https://www.docker.com/what-container)
for php, database, web server, mailing server, testing server and cache server.

## Logging Using Streams
Based on [The Twelve-Factor App](https://12factor.net/logs) principle we decided to use output streams for logging
rather than files so debugging and monitoring can be done better and faster.

There are 2 output streams for logging
- `STDERR` stream that should be used for error logs
- `STDOUT` should be used for logs that don't belong to error group logs

For monolog we just need to change config for logging type to `stream` and path to `php://stdout` or `php://stderr`
in config file `https://github.com/shopsys/shopsys/blob/master/project-base/app/config/config.yml`.
Many docker containers like php-fpm or postgres are preconfigured to use `STDERR` and `STDOUT` as a default path for
logging so there is no need for additional configuration to change it into these streams.
Php-fpm docker container sends logs into `STDERR` stream, `STDOUT` stream for access log is not used
because of the [php-fpm issue](https://github.com/docker-library/php/issues/358#issuecomment-271033464).

## Check Logs Via Docker
Since we have logging configured for using streams, now we can start debugging the logs from container like `php-fpm`
by command
```
docker logs -f shopsys-framework-php-fpm
```
`shopsys-framework-php-fpm` is name of php-fpm container from [docker-compose.yml](https://github.com/shopsys/shopsys/blob/master/docker/conf/docker-compose.yml.dist)
We will see that log message that was sent via `STDOUT` contains string `said into stdout` and the one via `STDERR` contains  `said into stderr`.

## Check Logs Via Docker-Compose
If we want to see logs from all the containers, we can use command
```
docker-compose logs
```
The output will be
```
shopsys-framework-php-fpm | [16-Jul-2018 13:48:47] WARNING: [pool www] child 10 said into stderr: "[2018-07-16 13:48:47] event.DEBUG: Notified event "kernel.terminate" to listener "Symfony\Bundle\SwiftmailerBundle\EventListener\EmailSenderListener::onTerminate". {"event":"kernel.terminate","listener":"Symfony\\Bundle\\SwiftmailerBundle\\EventListener\\EmailSenderListener::onTerminate"} []"
```
as we can see, each line contains name of the container on the start followed by the log message.

## Conclusion
Logging into streams is one of the prerequisites for scalable application, with implementation of orchestration tool like
[Kubernetes](https://kubernetes.io/docs/) we will be able to store logs in a centralized way and be able to find problems faster and also monitor
production environment.