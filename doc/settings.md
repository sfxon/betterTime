# Settings

This file explains how settings are handled.

There are two kinds of settings:

* Settings in the database
* Default settings

In an usual use case, the system first tries to load the setting from the database.
If it cannot find the setting in the database, it tries to load a default setting from a json settings file.

## Usage of the Setting-Service

Loading Settings with the setting service:

```$setting = $settingService->getSettingByTextId('view.project.setting');```

## Location of the default settings

The default setting files are located in ```src/config/dlh```.
