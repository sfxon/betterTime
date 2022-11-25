# PHP-CS-Fixer

This document describes the usage of php-cs-fixer in the BetterTime project.
If you want to know, why we use php-cs-fixer, see the file ```coding-standards.md```, that should also have been shipped in the docs folder.


## 1. Usage

To fix all files in the src folder, you can use these commands:

```
cd [symfony-root]
tools/php-cs-fixer/vendor/bin/php-cs-fixer fix src
```

This runs the program ```php-cs-fixer``` in the folder ```tools/php-cs-fixer/vendor/bin/``` to fix all php files in the directory ```src```.



## 2. Installation

When you have installed a new instance of BetterTime and want to use php-cs-fixer, you first have to install it.
The installation comes with the required composer files, but without the vendor files.

```
cd [symfony-root]/tools/php-cs-fixer
composer install
```

Prior, Php-cs-fixer has been installed, using this command. You should not have to run this again.

```
cd [symfony-root]
mkdir tools
cd tools
mkdir php-cs-fixer
cd ..
composer require --working-dir=tools/php-cs-fixer friendsofphp/php-cs-fixer
```
