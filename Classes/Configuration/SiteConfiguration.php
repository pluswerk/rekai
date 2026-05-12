<?php
declare(strict_types=1);

namespace Pluswerk\Rekai\Configuration;

final class SiteConfiguration
{
    public function __construct(
        private readonly bool $enabled,
        private readonly string $embedCode,
        private readonly bool $consentMode,
        private readonly string $autocompleteMode,
        private readonly string $autocompleteSelector,
        private readonly int $autocompleteNrOfHits,
        private readonly bool $autocompleteNavigateOnClick,
        private readonly bool $autocompleteUseCurrentLang,
        private readonly bool $testMode,
        private readonly bool $mockDataEnabled,
        private readonly string $projectId,
        private readonly string $secretKey,
    ) {}

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function getEmbedCode(): string
    {
        return $this->embedCode;
    }

    public function isConsentMode(): bool
    {
        return $this->consentMode;
    }

    public function getAutocompleteMode(): string
    {
        return $this->autocompleteMode;
    }

    public function getAutocompleteSelector(): string
    {
        return $this->autocompleteSelector;
    }

    public function getAutocompleteNrOfHits(): int
    {
        return $this->autocompleteNrOfHits;
    }

    public function isAutocompleteNavigateOnClick(): bool
    {
        return $this->autocompleteNavigateOnClick;
    }

    public function isAutocompleteUseCurrentLang(): bool
    {
        return $this->autocompleteUseCurrentLang;
    }

    public function isTestMode(): bool
    {
        return $this->testMode;
    }

    public function isMockDataEnabled(): bool
    {
        return $this->mockDataEnabled;
    }

    public function getProjectId(): string
    {
        return $this->projectId;
    }

    public function getSecretKey(): string
    {
        return $this->secretKey;
    }
}
