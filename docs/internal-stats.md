# Internal Stats

## 1 Overview

In order to make working with the program as easy as possible for the user,
some frequently used elements (or recently used elements) are offered for quick selection.
For example, when selecting a project for a recorded time, the user can quickly choose of a list of earlier used entries.

To archieve this, the programm stores simple statistical data.
The statistical data is recording, how often certain data entries were clicked,
and when they were last clicked.

Two entities are used for internal statistics:

* InternalStat
* InternalStatEntity


## 2 Entities

### 2.1 Internal Stat

This entity contains the statistical values. It has a many-to-one relation to InternalStatEntity.

### 2.2 InternalStatEntity

InternalStatEntity is a flat category system.
The InternalStatEntity is used to identifiy values in internalStat by a human readable name.
To archieve the readability, it uses a property *technicalName*, that is used as a human readable identififer.

It has a one-to-many relation to InternalStat.

## 3 Critic/Disadvantages

### 3.1 Linear increase in frequency of use

The weighting is currently stored linearly. Every click increases a counter. The counters are ever growing. In a future version, an algorithm should ensure that values ​​that were last clicked, increase more than those that have not been clicked for a longer time. For example, an hourly graduation could ensure that the values ​​slowly continue to decrease.

That way, it would made sure, that new values are coming up much earlier.

In the meantime, we concentrate on using timestamps to display last-used-entries.