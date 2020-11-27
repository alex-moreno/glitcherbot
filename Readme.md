
Install

```composer.json```

Usage

Create a .csv which contains a list of urls to iterate over (see example.csv)

Run like this:

```php scrapperBot.php all-sites.csv```

There are some settings that you can configure, like the headers that you'll send to the site or the concurrency that you want to use.

The higher the concurrency is configured, the more sites it will run on each step, but be careful, php is fast and can send high load to a site and put it in trouble. Big power means bigger responsibility.
