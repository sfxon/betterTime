# Configuration

This document describes, how configuration on multiple user levels works in betterTime.
Configuration is used to define, how specific parts of the software work.
Multiple user levels are used, to make it possible to have values for specific parts of the program.

Example:
Thinking of email configuration, the system might have a default smtp configuration, that may be altered by the backend to send mails over a different server,
and configuration for the frontend, where every team account might be able to send emails over their own infrastructure.

## Workflow

1. Load configDefinition for a specific config.
2. Load the related level definitions in one query. (SELECT FROM configValue WHERE configDefinitionId = ...)
3. Use in program definition of the levels (integer list, in steps of 10) to sort the entries.
4. Optional: required-level: sort out unwanted settings: If you are in the admin and want to send a mail, and the functionality requires to send a backend-mail, this may be defined in code as the required level)
5. Take the highest setting level and return an object or scalar value.

---

## Level

Levels are used, to define the right priority for a setting value.
Currently, I defined this initial levels for the application:

* initial (10)
* system (20)
* backend (30)
* frontend (40)
* team (50)
* user (60)

The number in brackets defines the level of the setting. The higher the number is, the higher is the settings level.
A higher number means a higher priority. If you load settings for all levels, the highest one is the one, that is returned.
This way, a user may override a system-setting, that overrides an initial setting.

A configurationDefinition defines, which kind of levels it uses.
If it does not use user or team definition, it sorts this values out while loading.
The sort-out part can also be done by code. If I do not want a specific part of the application, to use the user setting, I can tell the loader,
to exclude this config-Levels.

The levels are defined as json or mysql-set in the configDefinition database, depending on, what the framework makes possible.

The initial level, if set, represents the default value.
If no setting at all is present, the loader will return null.

---

## Pattern

The architecture does not implement some special kind of pattern. I thought of a factory-pattern, since the approach seems factory-like, but at least, it is just some simple algorithm that is based on the database.
The received objects to not differ in their functionality. The programm creates a sorted stack of data, and the upmost item of the stack is taken in the end. Sorting can include hierarchical sorting, as well as sorting levels out.

---

## Entities

### ConfigDefinition

This entity is used, to define a configuration in general.
It defines a technical name, to make it easier to refer to a specific configuration. The technical name is used especially for technical users, to identify a config easily.
It also defines the levels, that this configuration is available to.

* **id** - Uuid
* **technicalName** - varchar 256 - Used to have a "talking" name for the setting.
* **availableLevels** - json or a set in mysql - See section ```User Levels``` for more information.
* **description** - text - A description of the setting, aiming at developers and sysadmins, to have fast access to in-program documentation.

---

### ConfigValue

This entity is used, to save values for ConfigDefinitions. This are the values, that a user gives to specific configurations.
Their might be multiple entries with the same configDefinitionId, but with different level, team, and/or user id.

* **id** - Uuid
* **configDefinitionId** - Foreign-Key on the table configDefinition.
* **level** - varchar (32) - Defines the level.
* **foreignId** - Foreign key on the team table or the user table, depending on level. This field is nullable.
* **value** - varchar (4096) - Can be a skalar or json or something absolutely different, that can be stored as text.