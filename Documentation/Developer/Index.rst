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

Controllers
===========

``RecommendationsController::showAction()`` and ``QnaController::showAction()``
both inherit from ``AbstractRekaiController``. They build a ``<div>`` element
with the rek.ai ``data-*`` attributes derived from the content element's
FlexForm settings and the global extension configuration, then assign it to the
Fluid template as ``{divHtml}``.

The controllers do not perform any HTTP calls to rek.ai. All data fetching
happens in the visitor's browser via the injected JavaScript.

Templates
=========

Content element integration uses the ``lib.contentElement`` FLUIDTEMPLATE
mechanism. Two templates are registered under a dedicated template root path:

*  ``Resources/Private/Templates/Content/RekaiRecommendations.fluid.html``
*  ``Resources/Private/Templates/Content/RekaiQna.fluid.html``

These templates use ``<f:layout name="Default"/>`` and define only a ``Main``
section, delegating header rendering to the standard TYPO3 ``Default`` layout
from ``EXT:fluid_styled_content``.

The plugin action templates (consumed by Extbase) are separate:

*  ``Resources/Private/Templates/Recommendations/Show.html``
*  ``Resources/Private/Templates/Qna/Show.html``

These output only ``{divHtml}`` — the raw ``<div>`` placeholder.

TypoScript
==========

``Configuration/TypoScript/setup.typoscript`` adds the content template root
path to ``lib.contentElement``:

..  code-block:: typoscript

    lib.contentElement {
        templateRootPaths.20 = EXT:rekai/Resources/Private/Templates/Content/
    }

The ``templateName`` mapping (``RekaiRecommendations`` / ``RekaiQna``) is set
in ``ext_localconf.php`` via ``ExtensionManagementUtility::addTypoScript()``
with position ``defaultContentRendering``, ensuring it runs after
``ExtensionUtility::configurePlugin()`` and overrides the generic default.

Extension configuration
=======================

``Pluswerk\Rekai\Configuration\ExtensionConfigurationService`` wraps
``TYPO3\CMS\Core\Configuration\ExtensionConfiguration::get()`` and provides
typed accessor methods for all settings. It is injected via constructor
injection (autowired).

Dependency injection
====================

All classes use TYPO3's standard DI container (``Configuration/Services.yaml``
with ``autowire: true`` and ``autoconfigure: true``). No manual service wiring
is required.

Testing
=======

Unit tests cover ``ExtensionConfigurationService`` and
``RekaiScriptMiddleware``:

..  code-block:: bash

    vendor/bin/phpunit --configuration vendor/pluswerk/rekai/phpunit.xml
