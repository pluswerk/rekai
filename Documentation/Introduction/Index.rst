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
*  It exposes all relevant rek.ai configuration options through the TYPO3 extension settings UI.

Architecture overview
=====================

The extension does **not** call rek.ai APIs from PHP. Instead, TYPO3 outputs
HTML ``<div>`` elements with ``data-*`` attributes, and the rek.ai JavaScript
client reads those attributes at runtime to render personalized content.

Feature overview
================

*  Middleware-based script embedding in frontend responses
*  Consent mode support (``data-useconsent`` attribute)
*  Optional autocomplete initialization for a configurable CSS selector
*  Content element **Rek.ai Recommendations** (``rekai_recommendations``)
*  Content element **Rek.ai Q&A** (``rekai_qna``)
*  Test mode with project ID and secret key for sandbox environments

Compatibility
=============

*  TYPO3: 12.4 LTS, 13.4 LTS, 14.x
*  PHP: 8.1 or higher
