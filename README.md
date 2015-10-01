# wntt-docker-vagrant
Full We Need To Talk project environment.

## Setting up environment

### Vagrant based setup

Go to project directory and start with command:

```sh bin/vagrant-up.sh```

After starting log into webserver container and clear cache with command:

```sh bin/clear-cache.sh``` 

### Docker-compose based setup

Go to project directory and start with command:

```docker-compose up -d```

After starting log into webserver container and clear cache with command:

```sh bin/clear-cache.sh```

## Stopping environment

### Vagrant based setup

Go to project directory and stop with command:

```sh bin/vagrant-down.sh```

### Docker-compose based setup

Go to project directory and stop with command:

```docker-compose stop```

## Useful workflow commands and tools

### On docker machine

Listing all docker containers:

```docker ps -a```

Logging on container (e.g. webserver container):

```docker exec -ti wnttdockervagrant_web_1 bash```

### When logged to docker webserver container

Clearing cache of dev environment:

```sh bin/clear-cache.sh``` 

Clearing cache of other environments (e.g. behat):

```sh bin/clear-cache.sh behat```

Loading data fixtures:

```sh bin/load-fixtures.sh```

Running behat tests:

```bin/behat ```

Running behat tests of single suite:

```bin/behat --suite=api_features```

```bin/behat --suite=admin_features```

Running behat on single feature (e.g. api user feature):

```bin/behat features/api/user.feature```

Running phpspec tests:

```bin/phpspec run```

Changing permissions to cache and logs directories:

```chmod 777 -R /tmp/sf2```
