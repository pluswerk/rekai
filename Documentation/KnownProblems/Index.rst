==============
Known Problems
==============

No output in frontend
=====================

*  Verify that **Enabled** is set to ``1`` in
   :guilabel:`Admin Tools > Settings > Extension Configuration > rekai`.
*  Verify that **Embed Code URL** is a valid ``https://`` URL.
*  Flush all TYPO3 caches.
*  Check the browser developer tools network tab to confirm the rek.ai script
   is loaded without errors.

Header not rendered
===================

The extension registers its content elements using TYPO3's standard content
element API and the ``--palette--;;headers`` palette. Header rendering is
handled by your site template's ``Default`` layout (from
``EXT:fluid_styled_content``). If your sitepackage overrides
``lib.contentElement`` without including the standard layout root path
(``EXT:fluid_styled_content/Resources/Private/Layouts/``), the header may not
appear.

Autocomplete not working
========================

*  Verify that ``autocompleteMode`` is set to ``auto`` or ``manual``.
*  Verify that ``autocompleteSelector`` matches an input element that exists
   on the page.
*  Confirm the rek.ai script is loaded successfully (no console errors).

TypoScript not loaded
=====================

If the content elements render no output at all, the extension TypoScript may
not be included in your site template. See :ref:`installation` for how to add
the static template or Site Set dependency.

Content element not available in wizard
=======================================

Flush the TYPO3 backend cache after installation. If the problem persists,
verify the extension is active under :guilabel:`Admin Tools > Extensions`.
