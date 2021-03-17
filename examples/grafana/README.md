# Grafana

If you're using sqlite for glitcherbot storage, it's possible to configure a data source in Grafana.

If you want to play with this, there's an example `docker-compose.yml` file in this folder. The compose file will run 
Grafana on localhost:3000, installing the sqlite plugin.

It will also mount the Glitcherbot project root folder at `/mnt/glitcherbot` in the container, because the container 
requires access to the sqlite DB file.

Once up and running with `docker-compose up -d`, login to Grafana at locahost:3000 using admin/admin.

Navigate to configuration/datasources and choose to add a sqlite data source.

Give the source a name (or leave default) and set the path to the DB file to `/mnt/glitcherbot/glitcherbot.sqlite3`.

You can now use queries to grab your Glitcherbot data when configuring panels etc. 

Full explanation of configuring Grafana is outside of the scope of this README.