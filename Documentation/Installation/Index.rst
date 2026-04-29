============
Installation
============

Requirements
============

*  TYPO3 12.4 LTS, 13.4 LTS, or 14.x
*  PHP 8.1 or higher
*  A `rek.ai <https://www.rek.ai>`__ account with a project and script URL

Install via Composer
====================

..  code-block:: bash

    composer require pluswerk/rekai

Include TypoScript
==================

The extension ships TypoScript that must be included in your site template.

#.  Open :guilabel:`Site Management > Templates`
#.  Edit the root template of your site
#.  In :guilabel:`Includes`, add **Rek.ai** to the list of included static templates

Alternatively, if your setup uses Site Sets (TYPO3 13+), include the extension
TypoScript via your site's ``config.yaml``:

..  code-block:: yaml

    dependencies:
      - pluswerk/rekai

Activate the extension
=======================

In non-Composer setups, activate the extension manually:

#.  Open :guilabel:`Admin Tools > Extensions`
#.  Find ``rekai`` and activate it
#.  Flush all caches

Configure the extension
=======================

After installation, open :guilabel:`Admin Tools > Settings > Extension Configuration > rekai`
and at minimum:

*  Set **Enabled** to ``1``
*  Enter the **Embed Code URL** from your rek.ai dashboard

See :ref:`configuration` for all available settings.
