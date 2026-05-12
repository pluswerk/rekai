==============
Known Problems
==============

No script in the frontend head
==============================

*  Verify ``rekai.enabled`` is ``true`` in the site settings.
*  Verify ``rekai.embedCode`` is a valid ``https://`` URL. Leading or trailing
   whitespace is tolerated (the service trims it), but other invalid values
   silently disable injection.
*  Flush all TYPO3 caches.
*  On TYPO3 13/14: confirm the site has ``pluswerk/rekai`` listed under
   ``dependencies`` in ``config/sites/<id>/config.yaml``.
*  On TYPO3 12: confirm the values are written under ``settings.rekai`` in
   ``config/sites/<id>/config.yaml`` (the Site Settings editor does not exist
   on 12).

Recommendations remain empty in the browser
============================================

The script is loaded and the ``rek-prediction`` containers are rendered, but
they stay empty (or contain only an empty inner wrapper).

In almost all cases this is a rek.ai **backend** issue, not an extension bug.
Check the following in order:

#.  **Allowed domains:** the requesting hostname must be on the rek.ai
    Allowed-Domains list (rek.ai dashboard → settings). After adding a domain
    it can take up to 30 minutes for edge caches to refresh.
#.  **Visitor context:** rek.ai personalisation needs either a configured
    Cold-Start fallback in the dashboard or some visitor history. The
    AI Simulator in the rek.ai dashboard can verify whether the backend
    has recommendations for a given filter combination.
#.  **Browser state:** stale cookies or 304 responses can mask a fixed
    backend. Test in an incognito window with DevTools → Network →
    *Disable cache* enabled.
#.  **Language mismatch:** ``allowedlangs`` (FlexForm) must overlap with the
    page language emitted as ``pl=…`` to the predict call. A site that only
    has ``en-US`` will not match an ``allowedlangs=de`` filter.

To produce a deterministic preview while diagnosing, set both
``rekai.advanced.testMode`` and ``rekai.advanced.useMockData`` to ``true`` —
rek.ai then returns synthetic data regardless of indexing state.

..  warning::
    With ``testMode`` enabled the configured ``projectId`` and ``secretKey``
    are rendered as ``data-projectid`` and ``data-srek`` attributes into the
    public HTML. Never enable test mode on a production site.

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

*  Verify ``rekai.autocomplete.mode`` is set to ``auto`` or ``manual``.
*  Verify ``rekai.autocomplete.selector`` matches an input element that
   exists on the page.
*  Confirm the rek.ai script is loaded successfully (no console errors).

TypoScript not loaded
=====================

If the content elements render no output at all, the extension TypoScript may
not be included in your site template. See :ref:`installation` for how to add
the Site Set dependency (TYPO3 13/14) or include the static template
(TYPO3 12).

Content element not available in wizard
=======================================

Flush the TYPO3 backend cache after installation. If the problem persists,
verify the extension is active under :guilabel:`Admin Tools > Extensions`.
