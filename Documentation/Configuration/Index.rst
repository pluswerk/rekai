..  _configuration:

=============
Configuration
=============

Extension settings are configured in:

:guilabel:`Admin Tools > Settings > Extension Configuration > rekai`

General
=======

* ``isEnabled`` (boolean)
  Master switch.
* ``embedCode`` (string)
  HTTPS URL to the rek.ai script.
* ``consentMode`` (boolean)
  Adds ``data-useconsent="true"`` to the script tag.

Autocomplete
============

* ``autocompleteMode``: ``disabled`` / ``auto`` / ``manual``
* ``autocompleteSelector``: CSS selector for search input
* ``autocompleteNrOfHits``: integer, 1..100
* ``autocompleteNavigateOnClick``: boolean
* ``autocompleteUseCurrentLang``: boolean

Advanced
========

* ``testMode``: boolean
* ``useMockData``: boolean
* ``projectId``: string
* ``secretKey``: password

Validation behavior
===================

If extension is enabled but ``embedCode`` is not a valid HTTPS URL, the script
is not injected.
