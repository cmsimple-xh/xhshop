XHShop Plugin for CMSimple_XH
=============================

[![beta: 1.0beta1](https://img.shields.io/badge/beta-1.0beta1-red.svg)](https://github.com/cmsimple-xh/xhshop/releases/tag/1.0beta1)
[![License: GPL v3](https://img.shields.io/badge/License-GPL%20v3-blue.svg)](http://www.gnu.org/licenses/gpl-3.0)

This plugin is based on the [wellrad plugin](http://www.wellrad.de/) 1.2.1 by
Martin Damken, and is supposed to be further developed by the CMSimple_XH
community. Many thanks to Martin for allowing us to change the license to GPLv3!

End users
---------

There is growing documentation in [our wiki](https://github.com/cmsimple-xh/xhshop/wiki).

Developers
----------

For development purposes, you can directly clone this repo into the `plugins/`
folder of a CMSimple_XH installation. Afterwards you have to setup the demo content
by running either
````
composer install
phing setup
````
or
````
setup.bat
````
The latter is a convenience for Windows users who don't want to install
[Composer](https://getcomposer.org/), and just want to test a Git checkout. For
full support of the development tools you have to install composer, though. Then
run
````
composer install
phing
````
to see what's supported.
