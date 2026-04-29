<?php
declare(strict_types=1);

namespace Pluswerk\Rekai\Tests\Unit\Middleware;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Pluswerk\Rekai\Configuration\ExtensionConfigurationService;
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
    ): ExtensionConfigurationService {
        $config = $this->createMock(ExtensionConfigurationService::class);
        $config->method('isEnabled')->willReturn($isEnabled);
        $config->method('getEmbedCode')->willReturn($embedCode);
        $config->method('isConsentMode')->willReturn($consentMode);
        $config->method('isTestMode')->willReturn($testMode);
        $config->method('getAutocompleteMode')->willReturn($autocompleteMode);
        $config->method('getAutocompleteSelector')->willReturn($autocompleteSelector);
        $config->method('getAutocompleteNrOfHits')->willReturn($autocompleteNrOfHits);
        $config->method('isAutocompleteNavigateOnClick')->willReturn($navigateOnClick);
        $config->method('isAutocompleteUseCurrentLang')->willReturn($useCurrentLang);
        return $config;
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
    public function doesNothingAndPassesThroughWhenDisabled(): void
    {
        $assetCollector = $this->createMock(AssetCollector::class);
        $assetCollector->expects(self::never())->method('addJavaScript');
        $middleware = new RekaiScriptMiddleware($this->makeConfig(isEnabled: false), $assetCollector);
        [$handler, $response] = $this->makeHandler();
        $result = $middleware->process($this->makeRequest(), $handler);
        self::assertSame($response, $result);
    }

    #[Test]
    public function doesNothingWithNonHttpsUrl(): void
    {
        $assetCollector = $this->createMock(AssetCollector::class);
        $assetCollector->expects(self::never())->method('addJavaScript');
        $middleware = new RekaiScriptMiddleware(
            $this->makeConfig(embedCode: 'http://unsecure.example.com/s.js'),
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
            $this->makeConfig(embedCode: 'not-a-url'),
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
        $middleware = new RekaiScriptMiddleware($this->makeConfig(consentMode: false), $assetCollector);
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
        $middleware = new RekaiScriptMiddleware($this->makeConfig(consentMode: true), $assetCollector);
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
        $middleware = new RekaiScriptMiddleware($this->makeConfig(), $assetCollector);
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
        $middleware = new RekaiScriptMiddleware($this->makeConfig(testMode: true), $assetCollector);
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
        $middleware = new RekaiScriptMiddleware($this->makeConfig(autocompleteMode: 'disabled'), $assetCollector);
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
            $this->makeConfig(autocompleteMode: 'auto', autocompleteSelector: '#search-input'),
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
            $this->makeConfig(autocompleteMode: 'auto', autocompleteSelector: ''),
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
            $this->makeConfig(autocompleteMode: 'auto', autocompleteSelector: '#q', navigateOnClick: true),
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
            $this->makeConfig(testMode: false),
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
            $this->makeConfig(
                autocompleteMode: 'auto',
                autocompleteSelector: '#q',
                useCurrentLang: true,
            ),
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
