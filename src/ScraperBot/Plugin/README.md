# Events and Plugins

There are two ways to extend the functionality of Glitcherbot.

At it's core, Glitcherbot uses an event dispatcher to provide an inversion of control mechanism, allowing core and 
custom (3rd party) code to extend behaviour at various points of the application during execution.

Additionally, there is a plugin mechanism. Plugins consist of two parts - a type, describing the interface that any 
plugins must implement (as well as some other metadata), and the plugins themselves; essentially PHP classes that 
implement the appropriate interface defined by their type.

Core uses plugins, but 3rd party plugins can also be created.

Without going into too much detail, the core discovery mechanism for plugins is itself driven by the event dispatcher.
In most cases, you won't need to worry about this detail, unless you plan on creating your own plugin types.

# Plugin System

## Overview

The plugin system is an event-driven mechanism for extending the functionality of the system.

The core plugin system uses YAML to define both new types and plugin instances.

## Plugin Types

Whilst it is possible to create new plugins of existing types, it is also possible to create your own plugin types.

By default, plugins and plugin types are defined using YAML. It is also possible to define your own discovery 
mechanisms if required, but it should rarely be necessary.

Plugin types are defined in YAML files using the format `*.plugin.type.yaml`, with the type name being defined by 
the `type` key within that YAML file.  It's conventional to use the type as the wildcard too 
(e.g. storage.plugin.type.yaml) but it's not mandatory in this case (in contrast to the YAML that defines plugin 
instances).

## Plugin Instances

Plugins of a given type are listed in files which use the filename format `<type>.plugins.yml`, where `<type>` is the 
plugin type as specified in the plugin type's `*.plugin.type.yml` file.

For example, for the core `storage` plugin type, the `storage.plugin.type.yml` file contains the key `type: storage` 
meaning that plugins of the type `storage` are defined in files named `storage.plugins.yml`.

Plugins are only active if they are listed in the Active Plugin Store.

## Plugin Discovery and Activation

Discovered plugins and plugin types are stored in the Plugin Registry, and active plugins are controlled by the Active 
Plugin Store.

So, for example, code that uses plugins would ask the Active Plugin Store for all active plugin instances of a given 
type.

At the moment, the Active Plugin Store's default storage mechanism is quite crude, and consists of a YAML file in the
project root called `active_plugin_store.yaml`. Longer term, alternative storage mechanisms could be defined since the 
store itself is swappable.

The `active_plugin_store.yaml` file format consists of the plugin type as a key, followed by an array of plugin IDs 
that are active.

For example:

```
storage:
  - sqlite3
  - mysql
``` 

## Creating a plugin

To create a new plugin, you must know two pieces of information.

1. The 'type' of the plugin.
2. The plugin's interface that you must implement. 

For example, a storage plugin has the type `storage` and implements the `ScraperBot\Storage\StorageInterface` interface.

The plugin 'type' can be found in the `*.plugin.type.yml` file for that plugin type, and uses the key `type`.

The interface you must implement can be found in the same file with the `interface` key.

Another place to conveniently find both of these pieces of information is at the `/plugins` URL, where you can see the 
list of registered plugin types, as well as the metadata such as the interface that a plugin of that type must 
implement.  At this URL you can also see a list of currently active plugins.

**Plugins must live sub-folders of the `custom` folder. This name becomes part of your class namespace, so it is
important to choose this name with that in mind.**

Once you have this information, you need to do two things.

1. Create a class which implements the interface defined by the type you are implementing.  
    - The file should live under a `src' folder that sits alongside the *.plugins.yml file you'll define in the next step.
    - The namespace prefix should consist of `\ScraperBot` and the parent directory of the file created in the next step. 
2. Define a <type>/plugins.yml file and reference your new class there using the fully qualified name as the `class` 
key.

Plugins in the *.plugins.yml file are keyed by ID, and you can define multiple instances.

For example, the core storage plugin is defined in `storage.plugins.yml` and looks like

```
sqlite3:
  class: ScraperBot\Storage\Plugin\SqlLite3Storage
  description: SqlLite3 storage plugin
```

When adding custom plugins under the 'custom' folder, the autoloader is automatically updated based on the location of
the discovered `*.plugins.yaml` file.

As mentioned earlier, the parent folder of your `*.plugins.yaml` file will be used as
part of the namespace for loading of your custom classes. There should be a 'src' folder alongside your yaml file that
contains your classes.

For example, your structure might be

`custom/MySqlStorage/storage.plugins.yaml`

The contents might be

```
mysql:
  class: ScraperBot\MySqlStorage\Plugin\Storage\MySqlStorage
  description: MySQL storage plugin
```

Notice the namespace of the class. Such namespaces always use `ScraperBot\` as a prefix, followed by the folder name.
Anything in this namespace must live in the 'src' folder alongside the *.plugins.yaml file.

So, given the namespace above, our class would exist in

`custom/MySqlStorage/src/Plugin/Storage/MySqlStorage.php`

3. Register your plugin as an active plugin.

Add an entry into the `active_plugin_store.yaml` file in your project root. See above for the file format. You will 
need your plugin ID from your `*.plugins.yaml` file. 

# Core Plugin Types

TODO

# Listing Available Plugin Types

TODO - /plugins, via plugin registry code or finding files

