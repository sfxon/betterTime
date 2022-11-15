# Decisions

In this document keep track of decisions we made during the development process.

## 1. Dialogs/Popups/Modals

We try to avoid Dialogs that are popping up.

* We want to keep things simple, at the moment.
* When you close the dialog with changed data, you usually have to update the ui of the main page, or at least parts of it, which leads to more complexity.
* If we want to build more interactive things in the future, we should plan using a framework like react or vue.js.

## 2. TimeTrackings and assigned invoices

### 2.a) Multiple invoices

We do not allow multiple invoice-assignments to one time-tracking, because:

* We want to keep things as simple as possible.

* If we would allow to assign multiple invoices, we would have a problem, defining which invoice took how much of the time of a specific tracked time.
