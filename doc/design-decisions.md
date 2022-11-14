# Decisions

In this document keep track of decisions we made during the development process.

## 1. Dialogs/Popups/Modals

We try to avoid Dialogs that are popping up.

* We want to keep things simple, at the moment.
* When you close the dialog with changed data, you usually have to update the ui of the main page, or at least parts of it, which leads to more complexity.
* If we want to build more interactive things in the future, we should plan using a framework like react or vue.js.

