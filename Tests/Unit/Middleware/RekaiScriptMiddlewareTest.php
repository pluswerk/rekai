<?php
declare(strict_types=1);

namespace Pluswerk\Rekai\Tests\Unit\Middleware;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Pluswerk\Rekai\Configuration\SiteConfiguration;
use Pluswerk\Rekai\Configuration\SiteConfigurationService;
use Pluswerk\Rekai\Middleware\RekaiScriptMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Page\AssetCollector;

final class RekaiScriptMiddlewareTest extends TestCase
{
    private function makeConfig(
        bool $isEnabled = true,
        string $embedCode = 'https://test.rekai.se/s.js',
        bool $consentMode = false,
        bool $testMode = false,
        string $autocompleteMode = 'disabled',
        string $autocompleteSelector = '',
        int $autocompleteNrOfHits = 10,
        bool $navigateOnClick = false,
        bool $useCurrentLang = false,
    ): SiteConfiguration {
        return new SiteConfiguration(
            enabled: $isEnabled,
            embedCode: $embedCode,
            consentMode: $consentMode,
            autocompleteMode: $autocompleteMode,
            autocompleteSelector: $autocompleteSelector,
            autocompleteNrOfHits: $autocompleteNrOfHits,
            autocompleteNavigateOnClick: $navigateOnClick,
            autocompleteUseCurrentLang: $useCurrentLang,
            testMode: $testMode,
            mockDataEnabled: false,
            projectId: '',
            secretKey: '',
        );
    }

    private function makeService(?SiteConfiguration $config): SiteConfigurationService
    {
        $service = $this->createMock(SiteConfigurationService::class);
        $service->method('getForRequest')->willReturn($config);
        return $service;
    }

    private function makeRequest(bool $hasBackendUser = false): ServerRequestInterface
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getAttribute')
            ->willReturnCallback(static fn(string $attr) => match ($attr) {
                'backend.user' => $hasBackendUser ? new \stdClass() : null,
                default => null,
            });
        return $request;
    }

    private function makeHandler(): array
    {
        $response = $this->createMock(ResponseInterface::class);
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn($response);
        return [$handler, $response];
    }

    #[Test]
    public function passesThroughWhenNoSiteResolved(): void
    {
        $assetCollector = $this->createMock(AssetCollector::class);
        $assetCollector->expects(self::never())->method('addJavaScript');
        $middleware = new RekaiScriptMiddleware($this->makeService(null), $assetCollector);
        [$handler, $response] = $this->makeHandler();
        self::assertSame($response, $middleware->process($this->makeRequest(), $handler));
    }

    #[Test]
    public function doesNothingAndPassesThroughWhenDisabled(): void
    {
        $assetCollector = $this->createMock(AssetCollector::class);
        $assetCollector->expects(self::never())->method('addJavaScript');
        $middleware = new RekaiScriptMiddleware(
            $this->makeService($this->makeConfig(isEnabled: false)),
            $assetCollector,
        );
        [$handler, $response] = $this->makeHandler();
        self::assertSame($response, $middleware->process($this->makeRequest(), $handler));
    }

    #[Test]
    public function doesNothingWithNonHttpsUrl(): void
    {
        $assetCollector = $this->createMock(AssetCollector::class);
        $assetCollector->expects(self::never())->method('addJavaScript');
        $middleware = new RekaiScriptMiddleware(
            $this->makeService($this->makeConfig(embedCode: 'http://unsecure.example.com/s.js')),
            $assetCollector,
        );
        [$handler] = $this->makeHandler();
        $middleware->process($this->makeRequest(), $handler);
    }

    #[Test]
    public function doesNothingWithInvalidUrl(): void
    {
        $assetCollector = $this->createMock(AssetCollector::class);
        $assetCollector->expects(self::never())->method('addJavaScript');
        $middleware = new RekaiScriptMiddleware(
            $this->makeService($this->makeConfig(embedCode: 'not-a-url')),
            $assetCollector,
        );
        [$handler] = $this->makeHandler();
        $middleware->process($this->makeRequest(), $handler);
    }

    #[Test]
    public function addsScriptFileWithoutConsentAttributeByDefault(): void
    {
        $capturedAttributes = null;
        $assetCollector = $this->createMock(AssetCollector::class);
        $assetCollector->method('addJavaScript')
            ->willReturnCallback(function() use (&$capturedAttributes, $assetCollector) {
                $args = func_get_args();
                $capturedAttributes = $args[2] ?? [];
                return $assetCollector;
            });
        $middleware = new RekaiScriptMiddleware(
            $this->makeService($this->makeConfig(consentMode: false)),
            $assetCollector,
        );
        [$handler] = $this->makeHandler();
        $middleware->process($this->makeRequest(), $handler);
        self::assertArrayNotHasKey('data-useconsent', $capturedAttributes ?? []);
    }

    #[Test]
    public function addsConsentAttributeWhenConsentModeEnabled(): void
    {
        $capturedAttributes = null;
        $assetCollector = $this->createMock(AssetCollector::class);
        $assetCollector->method('addJavaScript')
            ->willReturnCallback(function() use (&$capturedAttributes, $assetCollector) {
                $args = func_get_args();
                $capturedAttributes = $args[2] ?? [];
                return $assetCollector;
            });
        $middleware = new RekaiScriptMiddleware(
            $this->makeService($this->makeConfig(consentMode: true)),
            $assetCollector,
        );
        [$handler] = $this->makeHandler();
        $middleware->process($this->makeRequest(), $handler);
        self::assertSame('true', $capturedAttributes['data-useconsent'] ?? null);
    }

    #[Test]
    public function addsBlocksaveviewScriptForBackendUser(): void
    {
        $addedInlineScripts = [];
        $assetCollector = $this->createMock(AssetCollector::class);
        $assetCollector->method('addJavaScript')->willReturn($assetCollector);
        $assetCollector->method('addInlineJavaScript')
            ->willReturnCallback(function() use (&$addedInlineScripts, $assetCollector) {
                $args = func_get_args();
                $addedInlineScripts[$args[0]] = $args[1];
                return $assetCollector;
            });
        $middleware = new RekaiScriptMiddleware(
            $this->makeService($this->makeConfig()),
            $assetCollector,
        );
        [$handler] = $this->makeHandler();
        $middleware->process($this->makeRequest(hasBackendUser: true), $handler);
        self::assertArrayHasKey('rekai_blocksaveview', $addedInlineScripts);
        self::assertStringContainsString('window.rek_blocksaveview = true', $addedInlineScripts['rekai_blocksaveview']);
    }

    #[Test]
    public function addsBlocksaveviewScriptInTestMode(): void
    {
        $addedInlineScripts = [];
        $assetCollector = $this->createMock(AssetCollector::class);
        $assetCollector->method('addJavaScript')->willReturn($assetCollector);
        $assetCollector->method('addInlineJavaScript')
            ->willReturnCallback(function() use (&$addedInlineScripts, $assetCollector) {
                $args = func_get_args();
                $addedInlineScripts[$args[0]] = $args[1];
                return $assetCollector;
            });
        $middleware = new RekaiScriptMiddleware(
            $this->makeService($this->makeConfig(testMode: true)),
            $assetCollector,
        );
        [$handler] = $this->makeHandler();
        $middleware->process($this->makeRequest(hasBackendUser: false), $handler);
        self::assertArrayHasKey('rekai_blocksaveview', $addedInlineScripts);
    }

    #[Test]
    public function doesNotAddAutocompleteScriptWhenDisabled(): void
    {
        $addedInlineScripts = [];
        $assetCollector = $this->createMock(AssetCollector::class);
        $assetCollector->method('addJavaScript')->willReturn($assetCollector);
        $assetCollector->method('addInlineJavaScript')
            ->willReturnCallback(function() use (&$addedInlineScripts, $assetCollector) {
                $args = func_get_args();
                $addedInlineScripts[$args[0]] = $args[1];
                return $assetCollector;
            });
        $middleware = new RekaiScriptMiddleware(
            $this->makeService($this->makeConfig(autocompleteMode: 'disabled')),
            $assetCollector,
        );
        [$handler] = $this->makeHandler();
        $middleware->process($this->makeRequest(), $handler);
        self::assertArrayNotHasKey('rekai_autocomplete_init', $addedInlineScripts);
    }

    #[Test]
    public function addsAutocompleteScriptWhenModeIsAuto(): void
    {
        $addedInlineScripts = [];
        $assetCollector = $this->createMock(AssetCollector::class);
        $assetCollector->method('addJavaScript')->willReturn($assetCollector);
        $assetCollector->method('addInlineJavaScript')
            ->willReturnCallback(function() use (&$addedInlineScripts, $assetCollector) {
                $args = func_get_args();
                $addedInlineScripts[$args[0]] = $args[1];
                return $assetCollector;
            });
        $middleware = new RekaiScriptMiddleware(
            $this->makeService($this->makeConfig(autocompleteMode: 'auto', autocompleteSelector: '#search-input')),
            $assetCollector,
        );
        [$handler] = $this->makeHandler();
        $middleware->process($this->makeRequest(), $handler);
        self::assertArrayHasKey('rekai_autocomplete_init', $addedInlineScripts);
        self::assertStringContainsString('rekai_autocomplete', $addedInlineScripts['rekai_autocomplete_init']);
        self::assertStringContainsString('#search-input', $addedInlineScripts['rekai_autocomplete_init']);
    }

    #[Test]
    public function doesNotAddAutocompleteScriptWhenSelectorIsEmpty(): void
    {
        $addedInlineScripts = [];
        $assetCollector = $this->createMock(AssetCollector::class);
        $assetCollector->method('addJavaScript')->willReturn($assetCollector);
        $assetCollector->method('addInlineJavaScript')
            ->willReturnCallback(function() use (&$addedInlineScripts, $assetCollector) {
                $args = func_get_args();
                $addedInlineScripts[$args[0]] = $args[1];
                return $assetCollector;
            });
        $middleware = new RekaiScriptMiddleware(
            $this->makeService($this->makeConfig(autocompleteMode: 'auto', autocompleteSelector: '')),
            $assetCollector,
        );
        [$handler] = $this->makeHandler();
        $middleware->process($this->makeRequest(), $handler);
        self::assertArrayNotHasKey('rekai_autocomplete_init', $addedInlineScripts);
    }

    #[Test]
    public function includesNavigateOnClickHandlerWhenEnabled(): void
    {
        $addedInlineScripts = [];
        $assetCollector = $this->createMock(AssetCollector::class);
        $assetCollector->method('addJavaScript')->willReturn($assetCollector);
        $assetCollector->method('addInlineJavaScript')
            ->willReturnCallback(function() use (&$addedInlineScripts, $assetCollector) {
                $args = func_get_args();
                $addedInlineScripts[$args[0]] = $args[1];
                return $assetCollector;
            });
        $middleware = new RekaiScriptMiddleware(
            $this->makeService($this->makeConfig(autocompleteMode: 'auto', autocompleteSelector: '#q', navigateOnClick: true)),
            $assetCollector,
        );
        [$handler] = $this->makeHandler();
        $middleware->process($this->makeRequest(), $handler);
        self::assertStringContainsString(
            'rekai_autocomplete:selected',
            $addedInlineScripts['rekai_autocomplete_init'],
        );
    }

    #[Test]
    public function doesNotAddBlocksaveviewScriptForNormalFrontendUser(): void
    {
        $addedInlineScripts = [];
        $assetCollector = $this->createMock(AssetCollector::class);
        $assetCollector->method('addJavaScript')->willReturn($assetCollector);
        $assetCollector->method('addInlineJavaScript')
            ->willReturnCallback(function() use (&$addedInlineScripts, $assetCollector) {
                $args = func_get_args();
                $addedInlineScripts[$args[0]] = $args[1];
                return $assetCollector;
            });

        $middleware = new RekaiScriptMiddleware(
            $this->makeService($this->makeConfig(testMode: false)),
            $assetCollector,
        );
        [$handler] = $this->makeHandler();
        $middleware->process($this->makeRequest(hasBackendUser: false), $handler);

        self::assertArrayNotHasKey('rekai_blocksaveview', $addedInlineScripts);
    }

    #[Test]
    public function includesUseLangOptionWhenEnabled(): void
    {
        $addedInlineScripts = [];
        $assetCollector = $this->createMock(AssetCollector::class);
        $assetCollector->method('addJavaScript')->willReturn($assetCollector);
        $assetCollector->method('addInlineJavaScript')
            ->willReturnCallback(function() use (&$addedInlineScripts, $assetCollector) {
                $args = func_get_args();
                $addedInlineScripts[$args[0]] = $args[1];
                return $assetCollector;
            });

        $middleware = new RekaiScriptMiddleware(
            $this->makeService($this->makeConfig(
                autocompleteMode: 'auto',
                autocompleteSelector: '#q',
                useCurrentLang: true,
            )),
            $assetCollector,
        );
        [$handler] = $this->makeHandler();
        $middleware->process($this->makeRequest(), $handler);

        self::assertStringContainsString(
            '"useLang":true',
            $addedInlineScripts['rekai_autocomplete_init'],
        );
    }
}
