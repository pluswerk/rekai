<?php
declare(strict_types=1);

namespace Pluswerk\Rekai\Middleware;

use Pluswerk\Rekai\Configuration\SiteConfiguration;
use Pluswerk\Rekai\Configuration\SiteConfigurationService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Page\AssetCollector;

final class RekaiScriptMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly SiteConfigurationService $configService,
        private readonly AssetCollector $assetCollector,
    ) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $config = $this->configService->getForRequest($request);
        if ($config === null || !$config->isEnabled()) {
            return $handler->handle($request);
        }

        $embedCode = $config->getEmbedCode();
        if (!filter_var($embedCode, FILTER_VALIDATE_URL) || !str_starts_with($embedCode, 'https://')) {
            return $handler->handle($request);
        }

        $scriptAttributes = [];
        if ($config->isConsentMode()) {
            $scriptAttributes['data-useconsent'] = 'true';
        }

        $this->assetCollector->addJavaScript('rekai_main_script', $embedCode, $scriptAttributes);

        $isBackendUser = $request->getAttribute('backend.user') !== null;
        if ($isBackendUser || $config->isTestMode()) {
            $this->assetCollector->addInlineJavaScript(
                'rekai_blocksaveview',
                'window.rek_blocksaveview = true;',
            );
        }

        $autocompleteMode = $config->getAutocompleteMode();
        $autocompleteSelector = $config->getAutocompleteSelector();
        if (in_array($autocompleteMode, ['auto', 'manual'], true) && $autocompleteSelector !== '') {
            $this->assetCollector->addInlineJavaScript(
                'rekai_autocomplete_init',
                $this->buildAutocompleteScript($config),
            );
        }

        return $handler->handle($request);
    }

    private function buildAutocompleteScript(SiteConfiguration $config): string
    {
        $options = ['nrOfHits' => $config->getAutocompleteNrOfHits()];

        if ($config->isAutocompleteUseCurrentLang()) {
            $options['useLang'] = true;
        }

        $script = sprintf(
            '__rekai.ready(function() { var rekAutocomplete = rekai_autocomplete(%s, %s);',
            json_encode($config->getAutocompleteSelector(), JSON_THROW_ON_ERROR),
            json_encode($options, JSON_THROW_ON_ERROR),
        );

        if ($config->isAutocompleteNavigateOnClick()) {
            $script .= ' rekAutocomplete.on("rekai_autocomplete:selected", function(event) { window.location.href = event.detail.link; });';
        }

        $script .= ' });';
        return $script;
    }
}
