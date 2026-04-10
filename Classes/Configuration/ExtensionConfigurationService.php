<?php
// Classes/Configuration/ExtensionConfigurationService.php
declare(strict_types=1);

namespace Pluswerk\Rekai\Configuration;

use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

final class ExtensionConfigurationService
{
    private readonly array $config;

    public function __construct(ExtensionConfiguration $extensionConfiguration)
    {
        try {
            $this->config = (array)$extensionConfiguration->get('rekai');
        } catch (\TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException $e) {
            $this->config = [];
        }
    }

    public function isEnabled(): bool
    {
        return (bool)($this->config['isEnabled'] ?? false);
    }

    public function getEmbedCode(): string
    {
        return (string)($this->config['embedCode'] ?? '');
    }

    public function isConsentMode(): bool
    {
        return (bool)($this->config['consentMode'] ?? false);
    }

    public function getAutocompleteMode(): string
    {
        return (string)($this->config['autocompleteMode'] ?? 'disabled');
    }

    public function getAutocompleteSelector(): string
    {
        return (string)($this->config['autocompleteSelector'] ?? '');
    }

    public function getAutocompleteNrOfHits(): int
    {
        return (int)($this->config['autocompleteNrOfHits'] ?? 10);
    }

    public function isAutocompleteNavigateOnClick(): bool
    {
        return (bool)($this->config['autocompleteNavigateOnClick'] ?? false);
    }

    public function isAutocompleteUseCurrentLang(): bool
    {
        return (bool)($this->config['autocompleteUseCurrentLang'] ?? false);
    }

    public function isTestMode(): bool
    {
        return (bool)($this->config['testMode'] ?? false);
    }

    public function isMockDataEnabled(): bool
    {
        return (bool)($this->config['useMockData'] ?? false);
    }

    public function getProjectId(): string
    {
        return (string)($this->config['projectId'] ?? '');
    }

    public function getSecretKey(): string
    {
        return (string)($this->config['secretKey'] ?? '');
    }
}
