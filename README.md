
# Visual regression testing made easy

Automating the boring stuff.

# Requirements

- PHP 7.4
- a (maybe big) list of urls to crawl

# Install

```composer.json```

# Usage

Create a .csv which contains a list of urls to iterate over (see example.csv).

If using Acquia Site Factory, a command is supplied to generate a list of sites from a sites.json file. 

You can see the available commands by running:

```php bin/visual_regression_bot.php```

For help with a specific command use:

```php bin/visual_regression_bot.php help <command>```

Whilst debugging, increase verbosity by adding a number of `-v` flags.

`-v` : verbose
`-vv` : very verbose
`-vvv` : debug

# Configuration

There are some settings that you can configure, like the headers that you'll send to the site or the concurrency that you want to use.

Move your config.sample.php into config.php and adapt to your needs.

Note: The higher the concurrency is configured, the more sites it will run on each step, but be careful, php is fast and can send high load to a site and put it in trouble. Big power means bigger responsibility.
