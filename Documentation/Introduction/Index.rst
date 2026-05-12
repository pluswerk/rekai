============
Introduction
============

`Rek.ai <https://www.rek.ai>`__ is an AI-powered personalization service that
delivers page recommendations, Q&A widgets, and search autocomplete to website
visitors. All personalization logic runs in the visitor's browser via a small
JavaScript client — no personal data is sent from your TYPO3 server to rek.ai.

This extension handles the TYPO3 integration:

*  It injects the rek.ai script tag into every frontend page via a PSR-15 middleware.
*  It provides two content elements that output the required HTML placeholder markup.
*  It exposes all relevant rek.ai configuration options per site — through the
   Site Settings editor on TYPO3 13/14, or through manual ``config.yaml`` edits
   on TYPO3 12.

Architecture overview
=====================

The extension does **not** call rek.ai APIs from PHP. Instead, TYPO3 outputs
HTML ``<div>`` elements with ``data-*`` attributes, and the rek.ai JavaScript
client reads those attributes at runtime to render personalized content.

Feature overview
================

*  Middleware-based script embedding in frontend responses
*  Per-site configuration via Site Settings (TYPO3 13/14) or ``config.yaml``
   fallback (TYPO3 12)
*  Consent mode support (``data-useconsent`` attribute)
*  Optional autocomplete initialization for a configurable CSS selector
*  Content element **Rek.ai Recommendations** (``rekai_recommendations``)
*  Content element **Rek.ai Q&A** (``rekai_qna``)
*  Test mode with project ID and secret key for sandbox environments
*  Automatic ``window.rek_blocksaveview`` for logged-in backend users so editor
   previews are not counted as visits

Compatibility
=============

*  TYPO3: 12.4 LTS, 13.4 LTS, 14.x
*  PHP: 8.1 (TYPO3 12) or 8.2+ (TYPO3 13/14)
*  ``EXT:fluid_styled_content`` (included in TYPO3 core)
