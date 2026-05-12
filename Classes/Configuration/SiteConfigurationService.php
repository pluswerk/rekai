<?php
declare(strict_types=1);

namespace Pluswerk\Rekai\Configuration;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Site\Entity\Site;

final class SiteConfigurationService
{
    private readonly Typo3Version $typo3Version;

    public function __construct(?Typo3Version $typo3Version = null)
    {
        $this->typo3Version = $typo3Version ?? new Typo3Version();
    }

    public function getForRequest(ServerRequestInterface $request): ?SiteConfiguration
    {
        $site = $request->getAttribute('site');
        if (!$site instanceof Site) {
            return null;
        }
        return $this->getForSite($site);
    }

    public function getForSite(Site $site): SiteConfiguration
    {
        $rekai = $this->typo3Version->getMajorVersion() >= 13
            ? $this->readFromSiteSettings($site)
            : $this->readFromSiteConfiguration($site);

        $autocomplete = $rekai['autocomplete'] ?? [];
        $advanced = $rekai['advanced'] ?? [];

        return new SiteConfiguration(
            enabled: (bool)($rekai['enabled'] ?? false),
            embedCode: trim((string)($rekai['embedCode'] ?? '')),
            consentMode: (bool)($rekai['consentMode'] ?? false),
            autocompleteMode: trim((string)($autocomplete['mode'] ?? 'disabled')),
            autocompleteSelector: trim((string)($autocomplete['selector'] ?? '')),
            autocompleteNrOfHits: max(1, min(100, (int)($autocomplete['nrOfHits'] ?? 10))),
            autocompleteNavigateOnClick: (bool)($autocomplete['navigateOnClick'] ?? false),
            autocompleteUseCurrentLang: (bool)($autocomplete['useCurrentLang'] ?? false),
            testMode: (bool)($advanced['testMode'] ?? false),
            mockDataEnabled: (bool)($advanced['useMockData'] ?? false),
            projectId: trim((string)($advanced['projectId'] ?? '')),
            secretKey: trim((string)($advanced['secretKey'] ?? '')),
        );
    }

    private function readFromSiteSettings(Site $site): array
    {
        $tree = $site->getSettings()->get('rekai', []);
        return is_array($tree) ? $tree : [];
    }

    private function readFromSiteConfiguration(Site $site): array
    {
        return $site->getConfiguration()['settings']['rekai'] ?? [];
    }
}
