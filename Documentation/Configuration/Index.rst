..  _configuration:

=============
Configuration
=============

Extension settings
==================

All global settings are managed in:

:guilabel:`Admin Tools > Settings > Extension Configuration > rekai`

General
-------

.. list-table::
   :header-rows: 1
   :widths: 30 15 55

   * - Setting
     - Type
     - Description
   * - ``isEnabled``
     - boolean
     - Master switch. Disabling this prevents the rek.ai script from being
       injected on any page. Default: ``0``.
   * - ``embedCode``
     - string
     - HTTPS URL to the rek.ai client script (e.g.
       ``https://cdn.rek.ai/YOUR_PROJECT/s.js``). Must start with ``https://``.
   * - ``consentMode``
     - boolean
     - Adds ``data-useconsent="true"`` to the script tag, enabling rek.ai's
       built-in consent handling. Default: ``0``.

Autocomplete
------------

.. list-table::
   :header-rows: 1
   :widths: 30 15 55

   * - Setting
     - Type
     - Description
   * - ``autocompleteMode``
     - select
     - ``disabled`` — no autocomplete. ``auto`` — rek.ai initializes
       autocomplete automatically. ``manual`` — you call
       ``rekai_autocomplete()`` yourself. Default: ``disabled``.
   * - ``autocompleteSelector``
     - string
     - CSS selector targeting the search input field, e.g. ``#tx-solr-search-q``.
       Required when mode is ``auto`` or ``manual``.
   * - ``autocompleteNrOfHits``
     - integer
     - Number of autocomplete suggestions shown (1–100). Default: ``10``.
   * - ``autocompleteNavigateOnClick``
     - boolean
     - If enabled, the browser navigates to the result URL when a suggestion is
       clicked. Default: ``0``.
   * - ``autocompleteUseCurrentLang``
     - boolean
     - Passes the current TYPO3 page language to rek.ai autocomplete for
       language-filtered suggestions. Default: ``0``.

Advanced / Test mode
--------------------

.. list-table::
   :header-rows: 1
   :widths: 30 15 55

   * - Setting
     - Type
     - Description
   * - ``testMode``
     - boolean
     - Enables test mode. In this mode no visitor data is tracked, and
       ``window.rek_blocksaveview = true`` is injected. Also active for
       logged-in backend users regardless of this setting. Default: ``0``.
   * - ``useMockData``
     - boolean
     - Replaces live rek.ai data with synthetic mock data for local development.
       Default: ``0``.
   * - ``projectId``
     - string
     - Your rek.ai project ID. Required in test mode.
   * - ``secretKey``
     - password
     - Your rek.ai API secret key. Required in test mode.

Content element FlexForm settings
==================================

Each content element exposes additional settings through a FlexForm panel
visible in the backend. See :ref:`editor` for field descriptions.

Validation behaviour
====================

If the extension is enabled but ``embedCode`` is not a valid HTTPS URL, the
rek.ai script is silently not injected. No error is thrown.
