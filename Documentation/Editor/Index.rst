===================
Editor Guide
===================

Content elements
================

Editors can add:

* ``Rek.ai Recommendations``
* ``Rek.ai Q&A``

Both content elements are available in the ``rek.ai`` group in the
new content element wizard.

Standard TYPO3 fields
=====================

The content element type configuration includes TYPO3 header fields
(``header``, ``subheader``, ``header_layout``) via the header palette.
How these are rendered depends on your active site/theme templates.

FlexForm options
================

Each content element contains plugin-specific FlexForm settings such as:

* number of hits
* language filters
* subtree / exclude tree
* tags

Exact labels are defined in:

* ``Configuration/FlexForms/Recommendations.xml``
* ``Configuration/FlexForms/Qna.xml``
