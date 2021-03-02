# Plugin System

The plugin system is an event-driven mechanism for extending the functionality of the system.

Whilst it is possible to create new plugins of existing types, it is also possible to create your own plugin types.

By default, plugins and plugin types are defined using YAML. It is also possible to define your own discovery mechanisms if required, but it should rarely be necessary.

Plugin types are defined in YAML files using the format `*.plugin.type.yml`, with the type name being defined by the `type` key within that YAML file.

Plugins of a given type are listed in files which use the filename format `<type>.plugins.yml`, where `<type>` is the plugin type as specified in the plugin type's `*.plugin.type.yml` file.

For example, for the storage plugin type, the `storage.plugin.type.yml` file contains the key `type: storage` meaning that plugins of the type `storage` are defined in files name `storage.plugins.yml`.

# Creating a plugin

To create a new plugin, you must know two pieces of information.

1. The 'type' of the plugin.
2. The plugin's interface that you must implement. 

For example, a storage plugin has the type `storage` and implements the `ScraperBot\Storage\StorageInterface` interface.

The plugin 'type' can be found in the `*.plugin.type.yml` file for that plugin type, and uses the key `type`.

The interface you must implement can be found in the same file with the `interface` key.

Once you have this information, you need to do two things.

1. Create a class which implements the interface, and add the path in the file defined in (1)
2. Define a <type>/plugins.yml file and reference your new class there
