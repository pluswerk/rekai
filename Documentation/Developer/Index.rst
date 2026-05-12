===============
Developer Notes
===============

Architecture overview
=====================

The extension has three independent parts:

1. **Middleware** — injects the rek.ai script tag into every frontend response
2. **Controllers** — produce ``<div data-*>`` placeholder markup for the content elements
3. **TypoScript / Fluid** — integrates the content elements into ``lib.contentElement``

Middleware
==========

``Pluswerk\Rekai\Middleware\RekaiScriptMiddleware`` runs in the frontend
middleware stack (after ``prepare-tsfe-rendering``, before ``output-compression``)
and uses TYPO3's ``AssetCollector`` to inject:

*  the external rek.ai script from ``embedCode``
*  an optional ``data-useconsent="true"`` attribute when consent mode is active
*  an optional inline autocomplete bootstrap script
*  ``window.rek_blocksaveview = true;`` for backend users and in test mode

The script is **not** loaded with ``defer`` — the rek.ai widget runtime
expects to initialise during HTML parsing, before its containers are
discovered.

Controllers
===========

``RecommendationsController::showAction()`` and ``QnaController::showAction()``
both inherit from ``AbstractRekaiController``. They build a ``<div>`` element
with the rek.ai ``data-*`` attributes derived from the content element's
FlexForm settings and the site-resolved configuration, then assign it to the
Fluid template as ``{divHtml}``.

The controllers do not perform any HTTP calls to rek.ai. All data fetching
happens in the visitor's browser via the injected JavaScript.

Templates
=========

Content element integration uses the ``lib.contentElement`` FLUIDTEMPLATE
mechanism. Two templates are registered under a dedicated template root path:

*  ``Resources/Private/Templates/Content/RekaiRecommendations.fluid.html``
*  ``Resources/Private/Templates/Content/RekaiQna.fluid.html``

The plugin action templates (consumed by Extbase) are separate:

*  ``Resources/Private/Templates/Recommendations/Show.html``
*  ``Resources/Private/Templates/Qna/Show.html``

These output only ``{divHtml}`` — the raw ``<div>`` placeholder.

TypoScript
==========

``Configuration/TypoScript/setup.typoscript`` adds the content template root
path to ``lib.contentElement`` and registers the FlexForm default values for
both plugins.

Configuration service
=====================

``Pluswerk\Rekai\Configuration\SiteConfigurationService`` resolves the rek.ai
configuration for a given site and returns an immutable
``Pluswerk\Rekai\Configuration\SiteConfiguration`` DTO.

The service inspects the TYPO3 major version via the injected
``Typo3Version`` and dispatches to one of two readers:

*  **TYPO3 13/14:** ``$site->getSettings()->get('rekai', [])`` — reads the
   nested tree built from the flat ``settings.yaml`` of the Site Set.
*  **TYPO3 12:** ``$site->getConfiguration()['settings']['rekai']`` — reads
   the nested ``settings.rekai`` block from ``config.yaml`` directly, since
   Site Sets and the ``SiteSettings`` API do not exist on 12.

The Site Set definition lives in ``Configuration/Sets/Rekai/`` and is loaded
automatically when a site declares ``pluswerk/rekai`` as a dependency. On
TYPO3 12 the Site Set is ignored; the TypoScript is still loaded via
``ext_localconf.php``.

Dependency injection
====================

All classes use TYPO3's standard DI container (``Configuration/Services.yaml``
with ``autowire: true`` and ``autoconfigure: true``). ``Typo3Version`` is an
optional constructor argument on ``SiteConfigurationService`` so the service
can also be instantiated via ``GeneralUtility::makeInstance()`` (e.g. from
Extbase ``inject`` methods) without DI wiring.

Testing
=======

Unit tests cover ``SiteConfigurationService`` (both the TYPO3 13/14 and
TYPO3 12 branches) and ``RekaiScriptMiddleware``:

..  code-block:: bash

    vendor/bin/phpunit --configuration vendor/pluswerk/rekai/phpunit.xml
