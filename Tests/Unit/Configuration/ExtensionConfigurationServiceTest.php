<?php
declare(strict_types=1);

namespace Pluswerk\Rekai\Tests\Unit\Configuration;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Pluswerk\Rekai\Configuration\ExtensionConfigurationService;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

final class ExtensionConfigurationServiceTest extends TestCase
{
    private function makeService(array $config): ExtensionConfigurationService
    {
        $extConfig = $this->createMock(ExtensionConfiguration::class);
        $extConfig->method('get')->with('rekai')->willReturn($config);
        return new ExtensionConfigurationService($extConfig);
    }

    #[Test]
    public function isEnabledReturnsFalseByDefault(): void
    {
        self::assertFalse($this->makeService([])->isEnabled());
    }

    #[Test]
    public function isEnabledReturnsTrueWhenSet(): void
    {
        self::assertTrue($this->makeService(['isEnabled' => '1'])->isEnabled());
    }

    #[Test]
    public function getEmbedCodeReturnsEmptyStringByDefault(): void
    {
        self::assertSame('', $this->makeService([])->getEmbedCode());
    }

    #[Test]
    public function getEmbedCodeReturnsConfiguredValue(): void
    {
        $svc = $this->makeService(['embedCode' => 'https://example.rekai.se/s.js']);
        self::assertSame('https://example.rekai.se/s.js', $svc->getEmbedCode());
    }

    #[Test]
    public function isConsentModeReturnsFalseByDefault(): void
    {
        self::assertFalse($this->makeService([])->isConsentMode());
    }

    #[Test]
    public function getAutocompleteModeReturnsDisabledByDefault(): void
    {
        self::assertSame('disabled', $this->makeService([])->getAutocompleteMode());
    }

    #[Test]
    public function getAutocompleteSelectorReturnsEmptyByDefault(): void
    {
        self::assertSame('', $this->makeService([])->getAutocompleteSelector());
    }

    #[Test]
    public function getAutocompleteNrOfHitsReturnsTenByDefault(): void
    {
        self::assertSame(10, $this->makeService([])->getAutocompleteNrOfHits());
    }

    #[Test]
    public function getAutocompleteNrOfHitsReturnsConfiguredValue(): void
    {
        $svc = $this->makeService(['autocompleteNrOfHits' => '25']);
        self::assertSame(25, $svc->getAutocompleteNrOfHits());
    }

    #[Test]
    public function isAutocompleteNavigateOnClickReturnsFalseByDefault(): void
    {
        self::assertFalse($this->makeService([])->isAutocompleteNavigateOnClick());
    }

    #[Test]
    public function isAutocompleteUseCurrentLangReturnsFalseByDefault(): void
    {
        self::assertFalse($this->makeService([])->isAutocompleteUseCurrentLang());
    }

    #[Test]
    public function isTestModeReturnsFalseByDefault(): void
    {
        self::assertFalse($this->makeService([])->isTestMode());
    }

    #[Test]
    public function getProjectIdReturnsEmptyByDefault(): void
    {
        self::assertSame('', $this->makeService([])->getProjectId());
    }

    #[Test]
    public function getSecretKeyReturnsEmptyByDefault(): void
    {
        self::assertSame('', $this->makeService([])->getSecretKey());
    }

    #[Test]
    public function isMockDataEnabledReturnsFalseByDefault(): void
    {
        self::assertFalse($this->makeService([])->isMockDataEnabled());
    }

    #[Test]
    public function isMockDataEnabledReturnsTrueWhenSet(): void
    {
        self::assertTrue($this->makeService(['useMockData' => '1'])->isMockDataEnabled());
    }

    #[Test]
    public function isConsentModeReturnsTrueWhenSet(): void
    {
        self::assertTrue($this->makeService(['consentMode' => '1'])->isConsentMode());
    }

    #[Test]
    public function isAutocompleteNavigateOnClickReturnsTrueWhenSet(): void
    {
        self::assertTrue($this->makeService(['autocompleteNavigateOnClick' => '1'])->isAutocompleteNavigateOnClick());
    }

    #[Test]
    public function isAutocompleteUseCurrentLangReturnsTrueWhenSet(): void
    {
        self::assertTrue($this->makeService(['autocompleteUseCurrentLang' => '1'])->isAutocompleteUseCurrentLang());
    }

    #[Test]
    public function isTestModeReturnsTrueWhenSet(): void
    {
        self::assertTrue($this->makeService(['testMode' => '1'])->isTestMode());
    }

    #[Test]
    public function getAutocompleteModeReturnsConfiguredValue(): void
    {
        $svc = $this->makeService(['autocompleteMode' => 'auto']);
        self::assertSame('auto', $svc->getAutocompleteMode());
    }

    #[Test]
    public function getProjectIdReturnsConfiguredValue(): void
    {
        $svc = $this->makeService(['projectId' => 'my-project-123']);
        self::assertSame('my-project-123', $svc->getProjectId());
    }

    #[Test]
    public function constructorReturnsEmptyConfigOnNotConfiguredException(): void
    {
        $extConfig = $this->createMock(ExtensionConfiguration::class);
        $extConfig->method('get')->willThrowException(
            new \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException()
        );
        $svc = new ExtensionConfigurationService($extConfig);
        self::assertFalse($svc->isEnabled());
        self::assertSame('', $svc->getEmbedCode());
    }
}
