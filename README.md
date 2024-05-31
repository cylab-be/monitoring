# Monitoring

[![pipeline status](https://gitlab.cylab.be/cylab/monitoring/badges/master/pipeline.svg)](https://gitlab.cylab.be/cylab/monitoring/-/commits/master)
[![coverage report](https://gitlab.cylab.be/cylab/monitoring/badges/master/coverage.svg)](https://gitlab.cylab.be/cylab/monitoring/-/commits/master)

A simple monitoring tool where monitored servers "push" their state to the monitoring interface.

![](./monitoring.png)

## Contributing

The easiest way to run the development environment is using docker compose.

Once docker is installed, you can start the dev environment with

```bash
cp env.dev .env

# modify .env if needed
# (specially UID and GID)
nano .env

docker compose up
```

After a few seconds, the monitoring interface will be available at ```http://127.0.0.1:8080/```

The dev stack also includes mailhog, so you can inspect sent emails at ```http://127.0.0.1:8025```

More info: https://cylab.be/blog/336/use-docker-compose-to-create-a-dev-environment-for-laravel
