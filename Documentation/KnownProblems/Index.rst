==============
Known Problems
==============

No output in frontend
=====================

Check:

* ``isEnabled`` is active
* ``embedCode`` is a valid HTTPS URL
* TYPO3 caches are flushed

Autocomplete not working
========================

Check:

* ``autocompleteMode`` is ``auto`` or ``manual``
* ``autocompleteSelector`` matches an existing input
* rek.ai script is loaded successfully in browser network tab

Theme-specific rendering differences
====================================

Header/subheader and wrapper output (for example around plugin content)
depends on the active TYPO3 sitepackage/theme templates.
This extension follows TYPO3 content rendering conventions and does not
enforce a custom global wrapper.
