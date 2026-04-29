..  _editor:

============
Editor Guide
============

Adding a content element
========================

Both rek.ai content elements are available in the **new content element wizard**
under the **rek.ai** group:

*  **Rek.ai Recommendations** — displays personalized page recommendations
*  **Rek.ai Q&A** — displays context-aware questions and answers

Standard TYPO3 header fields (header, subheader, header layout) are available
on each element and rendered by your site template in the standard TYPO3 way.

Rek.ai Recommendations
=======================

Tab: General
------------

.. list-table::
   :header-rows: 1
   :widths: 30 70

   * - Field
     - Description
   * - Number of Hits
     - How many recommendations rek.ai should return. Range: 1–100. Default: 10.
   * - Render Style
     - Visual layout of the recommendations widget: ``list``, ``pills``, or
       ``advanced`` (card grid). Default: ``list``.
   * - Show Image
     - If enabled, rek.ai includes a page thumbnail in each recommendation.
   * - Show Ingress
     - If enabled, rek.ai includes a short teaser text in each recommendation.
   * - Columns
     - Number of columns for the ``advanced`` grid style. Range: 1–6. Default: 2.

Tab: Filter
-----------

.. list-table::
   :header-rows: 1
   :widths: 30 70

   * - Field
     - Description
   * - Subtree
     - Comma-separated relative URL paths. Limits recommendations to pages
       under these paths (e.g. ``/news/,/blog/``).
   * - Exclude Tree
     - Comma-separated relative URL paths to exclude from recommendations.
   * - Allowed Languages
     - Comma-separated ISO language codes (e.g. ``de,en``). Limits
       recommendations to pages in these languages.
   * - Tags
     - Comma-separated rek.ai tags for additional content filtering.

Rek.ai Q&A
===========

Tab: General
------------

.. list-table::
   :header-rows: 1
   :widths: 30 70

   * - Field
     - Description
   * - Number of Hits
     - How many Q&A items rek.ai should return. Range: 1–100. Default: 10.
   * - Current page questions only
     - If enabled, only Q&As indexed for the current page are shown
       (sets ``data-currentpagequestions``).

Tab: Filter
-----------

Same filter fields as Rek.ai Recommendations (Subtree, Exclude Tree, Allowed
Languages, Tags).
