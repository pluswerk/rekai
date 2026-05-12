<?php
declare(strict_types=1);

namespace Pluswerk\Rekai\Tests\Unit\Configuration;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Pluswerk\Rekai\Configuration\SiteConfigurationService;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteSettings;

final class SiteConfigurationServiceTest extends TestCase
{
    private function makeService(int $majorVersion = 14): SiteConfigurationService
    {
        $version = $this->createMock(Typo3Version::class);
        $version->method('getMajorVersion')->willReturn($majorVersion);
        return new SiteConfigurationService($version);
    }

    private function makeSite13(array $settingsTree): Site
    {
        $site = $this->createMock(Site::class);
        $site->method('getSettings')->willReturn(SiteSettings::createFromSettingsTree($settingsTree));
        return $site;
    }

    private function makeSite12(array $configuration): Site
    {
        $site = $this->createMock(Site::class);
        $site->method('getConfiguration')->willReturn($configuration);
        return $site;
    }

    private function makeRequest(?Site $site): ServerRequestInterface
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getAttribute')
            ->willReturnCallback(static fn(string $attr) => match ($attr) {
                'site' => $site,
                default => null,
            });
        return $request;
    }

    #[Test]
    public function returnsNullWhenRequestHasNoSite(): void
    {
        self::assertNull($this->makeService()->getForRequest($this->makeRequest(null)));
    }

    #[Test]
    public function returnsConfigWithDefaultsForEmptySite(): void
    {
        $config = $this->makeService()->getForSite($this->makeSite13([]));

        self::assertFalse($config->isEnabled());
        self::assertSame('', $config->getEmbedCode());
        self::assertFalse($config->isConsentMode());
        self::assertSame('disabled', $config->getAutocompleteMode());
        self::assertSame('', $config->getAutocompleteSelector());
        self::assertSame(10, $config->getAutocompleteNrOfHits());
        self::assertFalse($config->isAutocompleteNavigateOnClick());
        self::assertFalse($config->isAutocompleteUseCurrentLang());
        self::assertFalse($config->isTestMode());
        self::assertFalse($config->isMockDataEnabled());
        self::assertSame('', $config->getProjectId());
        self::assertSame('', $config->getSecretKey());
    }

    #[Test]
    public function readsConfiguredValuesFromSiteSettings(): void
    {
        $config = $this->makeService()->getForSite($this->makeSite13([
            'rekai' => [
                'enabled' => true,
                'embedCode' => 'https://cdn.rek.ai/foo/s.js',
                'consentMode' => true,
                'autocomplete' => [
                    'mode' => 'auto',
                    'selector' => '#search',
                    'nrOfHits' => 25,
                    'navigateOnClick' => true,
                    'useCurrentLang' => true,
                ],
                'advanced' => [
                    'testMode' => true,
                    'useMockData' => true,
                    'projectId' => 'my-project',
                    'secretKey' => 'shh',
                ],
            ],
        ]));

        self::assertTrue($config->isEnabled());
        self::assertSame('https://cdn.rek.ai/foo/s.js', $config->getEmbedCode());
        self::assertTrue($config->isConsentMode());
        self::assertSame('auto', $config->getAutocompleteMode());
        self::assertSame('#search', $config->getAutocompleteSelector());
        self::assertSame(25, $config->getAutocompleteNrOfHits());
        self::assertTrue($config->isAutocompleteNavigateOnClick());
        self::assertTrue($config->isAutocompleteUseCurrentLang());
        self::assertTrue($config->isTestMode());
        self::assertTrue($config->isMockDataEnabled());
        self::assertSame('my-project', $config->getProjectId());
        self::assertSame('shh', $config->getSecretKey());
    }

    #[Test]
    public function readsConfiguredValuesFromSiteConfigurationOnTypo3v12(): void
    {
        $config = $this->makeService(majorVersion: 12)->getForSite($this->makeSite12([
            'settings' => [
                'rekai' => [
                    'enabled' => true,
                    'embedCode' => 'https://cdn.rek.ai/foo/s.js',
                    'consentMode' => true,
                    'autocomplete' => [
                        'mode' => 'manual',
                        'selector' => '#legacy',
                        'nrOfHits' => 12,
                    ],
                    'advanced' => [
                        'testMode' => true,
                        'projectId' => 'legacy-project',
                    ],
                ],
            ],
        ]));

        self::assertTrue($config->isEnabled());
        self::assertSame('https://cdn.rek.ai/foo/s.js', $config->getEmbedCode());
        self::assertTrue($config->isConsentMode());
        self::assertSame('manual', $config->getAutocompleteMode());
        self::assertSame('#legacy', $config->getAutocompleteSelector());
        self::assertSame(12, $config->getAutocompleteNrOfHits());
        self::assertTrue($config->isTestMode());
        self::assertSame('legacy-project', $config->getProjectId());
    }

    #[Test]
    public function returnsDefaultsWhenSiteConfigurationHasNoRekaiKeyOnTypo3v12(): void
    {
        $config = $this->makeService(majorVersion: 12)->getForSite($this->makeSite12([
            'base' => '/',
        ]));

        self::assertFalse($config->isEnabled());
        self::assertSame('', $config->getEmbedCode());
        self::assertSame('disabled', $config->getAutocompleteMode());
    }

    #[Test]
    public function clampsNrOfHitsToValidRange(): void
    {
        $service = $this->makeService();
        self::assertSame(
            1,
            $service->getForSite($this->makeSite13(['rekai' => ['autocomplete' => ['nrOfHits' => 0]]]))
                ->getAutocompleteNrOfHits(),
        );
        self::assertSame(
            100,
            $service->getForSite($this->makeSite13(['rekai' => ['autocomplete' => ['nrOfHits' => 9999]]]))
                ->getAutocompleteNrOfHits(),
        );
    }

    #[Test]
    public function trimsWhitespaceFromEmbedCode(): void
    {
        $config = $this->makeService()->getForSite(
            $this->makeSite13(['rekai' => ['embedCode' => ' https://cdn.rek.ai/foo/s.js ']]),
        );
        self::assertSame('https://cdn.rek.ai/foo/s.js', $config->getEmbedCode());
    }

    #[Test]
    public function getForRequestDelegatesToGetForSite(): void
    {
        $service = $this->makeService();
        $site = $this->makeSite13(['rekai' => ['enabled' => true]]);
        $config = $service->getForRequest($this->makeRequest($site));
        self::assertNotNull($config);
        self::assertTrue($config->isEnabled());
    }
}
