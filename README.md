# PVE2-PHP #

A PHP web client for [Proxmox VE][PVE] 2.0+ clusters.

## Requirements ##

The following requirements are external dependencies.  You must provide
and maintain them separately.

* PHP 5+ (with Apache+mod_php, or any web server using (F)CGI)
    * cURL extension
    * SSL extension
* Proxmox VE 2.0+ node to connect to

The following requirements are either bundled with this project, or rely
on pulic CDNs.  You shouldn't need to do anything to make them available.

* [PVE2 API (PHP Client)][PVE2PHP]
* [CodeIgniter][CI] PHP framework
* [AngularJS][NG] JavaScript framework
* [Angular Dynamic Forms][NGDF]
* [StoreLocal][JSLocal] JavaScript browser local storage abstraction class,
by @flinehan

## Usage ##

At the moment, this project does little more than provide exploratory
functionality.  URIs must be entered manually, and only some of the
advertised endpoints have input fields defined.  For those that do, the
appropriate HTML controls are created automatically when the appropriate
URI is entered.  The rest are served by a textarea where standard JSON can
be entered for manual interaction with unimplemented endpoints.

To use it anyway, you'll need to set it up in one of two ways.

1. Define `PVE_HOST`, `PVE_USER`, `PVE_REALM`, and `PVE_PASS` in your
web server's environment; consult your server's documentation for how
to do this.  Each should be set to the appropriate value for the node/user
you wish to authenticate to/as.

2. Edit `application/config/pve2.php`, and update the appropriate settings
there.

Then, simply point your browser at your server and go.

## To Do ##

Eventually, I'd like to get this working smoothly enough to replace the
manual URI input with a more sophisticated navigation-style UI, probably
along the same lines as the official web UI.  Then, the goal is to support
logging in to multiple clusters simultaneously, so all can be managed from
one place.  Ultimately, I'd like this to be a viable replacement UI, with
a full API Explorer interface in addition to the navigation-based approach
taken by the official UI.  For now, though, it's mostly just a
familiarization exercise.

[PVE]: http://pve.proxmox.com/wiki/Main_Page
[PVE2PHP]: https://github.com/CpuID/pve2-api-php-client
[CI]: https://github.com/EllisLab/CodeIgniter
[NG]: https://github.com/angular/angular.js
[NGDF]: https://github.com/danhunsaker/angular-dynamic-forms
[JSLocal]: http://www.frank-code.com/
