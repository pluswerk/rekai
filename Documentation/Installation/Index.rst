============
Installation
============

Requirements
============

*  TYPO3 12.4 LTS, 13.4 LTS, or 14.x
*  PHP 8.1 (TYPO3 12) or PHP 8.2+ (TYPO3 13/14)
*  A `rek.ai <https://www.rek.ai>`__ account with a project and script URL

Install via Composer
====================

..  code-block:: bash

    composer require pluswerk/rekai

Activate the extension
=======================

In non-Composer setups, activate the extension manually:

#.  Open :guilabel:`Admin Tools > Extensions`
#.  Find ``rekai`` and activate it
#.  Flush all caches

Configure the site (TYPO3 13 / 14)
==================================

Add the Rek.ai Site Set as a dependency of your site:

..  code-block:: yaml
    :caption: config/sites/<identifier>/config.yaml

    dependencies:
      - pluswerk/rekai

The settings then appear in the Site module under
:guilabel:`Site Management > Sites > [your site] > Settings > rekai`. At
minimum set ``rekai.enabled`` to ``true`` and enter ``rekai.embedCode``.

Configure the site (TYPO3 12)
=============================

Site Sets and the Settings UI do not exist in TYPO3 12. Settings must be
written directly into ``config/sites/<identifier>/config.yaml`` under the
``settings.rekai`` key:

..  code-block:: yaml
    :caption: config/sites/<identifier>/config.yaml

    settings:
      rekai:
        enabled: true
        embedCode: 'https://cdn.rek.ai/PROJECT/s.js'
        consentMode: true
        autocomplete:
          mode: auto
          selector: '#tx-solr-q'
          nrOfHits: 10
          navigateOnClick: true
          useCurrentLang: true
        advanced:
          testMode: false

The TypoScript (templates and FlexForm defaults) is loaded automatically via
the static template ``Rek.ai`` which must be included in the site's root
TypoScript template.

See :ref:`configuration` for all available settings and the full migration
table from 1.x.
