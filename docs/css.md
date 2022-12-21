# CSS

This document describes, which css components are included and how to use them.


## 1. Base (Bootstrap)

As a basis, [Bootstrap v5.2.2](https://getbootstrap.com/) is used in the project as a HTML and CSS Framework.
It is free under MIT license.


## 2. Light Mode/Dark Mode

The project uses [@vinorodrigues](https://github.com/vinorodrigues/) bootstrap dark theme, to show dark variants.
It is free under MIT license and can be found here:

https://github.com/vinorodrigues/bootstrap-dark-5

* In the frontend, the user can choose between light and dark mode.
* In the backend admin, we only support dark mode.

Currently, the project uses Bootstrap 5.2.2 in our light mode, but version 5.1.1 in dark mode. This is an inconsistency that may be fixed in the future. The README.md of the dark mode project states, that bootstrap 5.2.2 has flaws, which make it hard, to build a dark mode. In the mean-time, I try to avoid using components, that are making troubles, when used in light and dark mode.