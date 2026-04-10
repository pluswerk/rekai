<?php
// Tests/Unit/Configuration/ExtensionConfigurationServiceTest.php
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
}
