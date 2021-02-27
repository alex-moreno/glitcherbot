
# Visual regression testing made easy

Automating the boring stuff. 

Managing a website can be difficult. You can end up with hundreds of pages, and making sure all of them are functional after an event, say a deployment, peaks of traffic, editorial changes, ads or javascripts behaving badly, ... Creating software and maintaining websites and apps is hard... regression testing should not.

Before you start, Clone / fork this repository and go into the folder.
There are two ways of using this tool: With [Vagrant](#Vagrant) or [Docker](#Docker).


## Vagrant
Download and install [Vagrant](https://www.vagrantup.com/downloads).

### Requirements
- PHP 7.0
- Sqlite
- A (maybe big) list of urls to crawl. Robots.txt and Sitemaps detected automatically

### Installation

0. Download and unzip the package:
```
curl -L -# -C - -O "https://github.com/alex-moreno/glitcherbot/archive/main.zip"
unzip main.zip
cd glitcherbot-main
```

1. Run:
```composer install```

1. Make a copy of your config.php

```cp config.sample.php config.php```

### Usage

Create a .csv which contains a list of urls to iterate over (see example.csv).

If using Acquia Site Factory, a command is supplied to generate a list of sites from a sites.json file. You'll need to:

1. Download the sites.json in your Acquia Cloud subscription

```scp  [subscription][ENV].[ENV]@[subscription][ENV].ssh.enterprise-g1.acquia-sites.com:/mnt/files/[subscription][ENV]/files-private/sites.json ./sites-dev.json```

1. Vagrant up if you want to use the crawler inside the virtual machine (recommended).


1. Run the crawl against that json

```php bin/visual_regression_bot.php acquia:acsf-crawl-sites sites.json```


You can see all available commands by running:

```php bin/visual_regression_bot.php```

For help with a specific command use:

```php bin/visual_regression_bot.php help <command>```

Whilst debugging, increase verbosity by adding a number of `-v` flags.

`-v` : verbose
`-vv` : very verbose
`-vvv` : debug

### Configuration

There are some settings that you can configure, like the headers that you'll send to the site or the concurrency that you want to use.

Move your config.sample.php into config.php and adapt to your needs. For example:

```
<?php

return [
  'headers' => [
    'User-Agent'   => 'GlitcherBotScrapper/0.1',
    'Accept'       => 'application/json',
  ],
  'http_errors' => false,
  'connect_timeout' => 0, // wait forever
  'read_timeout' => 0,
  'timeout' => 0, // wait forever
  'concurrency' => 60,
];

```

Note: The higher the concurrency is configured, the more sites it will run on each step, but be careful, php is fast (contrary to popular belief), it could send high load to a site and put it in trouble. Big power means bigger responsibility.

## Stand Alone Tool

To run the regression tool as a stand along interface you need to point your webserver at the html/ directory in the repo.

### PHP Web Server

A composer script has been included to aid with testing of the tool. To run this use the command.

```composer start```

Then navigate to the following address in your browser.

```http://0.0.0.0:8000/```

## Docker

A docker setup has been included to aid with the running or the tool.

Download and install [Docker](https://www.docker.com/)

### Starting the Docker container
This command will start the containers

`make up`

### Building the tool (only need to run once)
This command will check if the config file exists and create one if needed. Then it will install all Composer 
dependencies.

`make build`

### Crawling
This command will use sample-sites.csv as source of urls to Crawl by default.

`make crawl`

To run the command with a different file, use the syntax

`make crawl SITES_CSV=path_to_sites_csv`

or for Acquia json files:

`make crawl-acquia SITES_JSON=sitesd8-prod.json`

Keep in mind that the crawl runs within the container, so `path_to_sites_csv` needs to be relative to the container.

### Web interface
Opens the tool on the browser.

`make open`

### Stopping the container

`make stop`

### Chained commands.
You can run all commands at once, for example the following command will start the containers, build, craw and open the browser.

`make up build crawl open`


### Parameters

--include_sitemaps=yes

This will include all sitemaps in the website, if they are referenced from the robots.txt

--force_sitemaps=yes

Using makefile and Docker:

`make crawl SITES_CSV=sample-sites.csv INCLUDE_SITEMAPS=yes`

`make crawl SITES_CSV=sample-sites.csv FORCE_SITEMAPS=yes`