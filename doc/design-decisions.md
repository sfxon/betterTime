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

## 3. DateTimeInputs 

*(Decided on 2022-11-24)*

I'll use [Tempus Dominus](https://getdatepicker.com/) as input for all date and time selections.
I thought about writing custom components, but took a step back and looked at the available options.
I soon recognized, I'd have to invest too much time in building custom components. I could invest this time also
in working into something existing, that is well tested und running stable.
That way I can concentrate better on the goals of the project, and probably will not loose myself in details.

Tempus Dominus is a DateTimePicker library by Eonasdan, that is developed for a couple of years now.
Very stable, very trustworthy. Go, spend him a coffee.
