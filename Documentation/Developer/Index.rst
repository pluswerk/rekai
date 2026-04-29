===============
Developer Notes
===============

Architecture
============

Middleware
----------

``Pluswerk\Rekai\Middleware\RekaiScriptMiddleware`` injects:

* external rek.ai script via TYPO3 ``AssetCollector``
* optional inline bootstrap for autocomplete
* ``window.rek_blocksaveview = true;`` for backend users or test mode

Controllers
-----------

* ``RecommendationsController::showAction()``
* ``QnaController::showAction()``

Both inherit from ``AbstractRekaiController`` and provide HTML placeholder
markup consumed by rek.ai frontend JavaScript.

Templates
---------

Current template split:

* ``Resources/Private/Templates/Content/*`` for content element integration
* ``Resources/Private/Templates/Recommendations/Show.html``
* ``Resources/Private/Templates/Qna/Show.html``

TypoScript
==========

Main setup file:

* ``Configuration/TypoScript/setup.typoscript``

Key parts:

* registers content element template path in ``lib.contentElement``
* maps ``tt_content.rekai_recommendations.templateName``
* maps ``tt_content.rekai_qna.templateName``

Testing
=======

Unit tests are in:

* ``Tests/Unit/Configuration/ExtensionConfigurationServiceTest.php``
* ``Tests/Unit/Middleware/RekaiScriptMiddlewareTest.php``
