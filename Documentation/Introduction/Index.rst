============
Introduction
============

The extension does not call rek.ai APIs directly from TYPO3 PHP code.
Instead, TYPO3 outputs HTML and JavaScript configuration, and the rek.ai
client script performs rendering and personalization in the visitor's browser.

Feature overview
================

* Middleware-based script embedding in frontend responses
* Optional autocomplete initialization for a configurable CSS selector
* Content element ``Rek.ai Recommendations``
* Content element ``Rek.ai Q&A``

Compatibility
=============

According to extension constraints:

* TYPO3: 12.4 LTS, 13.4 LTS, 14 (as declared by composer/ext_emconf)
* PHP: 8.1+
