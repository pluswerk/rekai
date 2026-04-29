<?php
declare(strict_types=1);

namespace Pluswerk\Rekai\Middleware;

use Pluswerk\Rekai\Configuration\ExtensionConfigurationService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Page\AssetCollector;

final class RekaiScriptMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly ExtensionConfigurationService $config,
        private readonly AssetCollector $assetCollector,
    ) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!$this->config->isEnabled()) {
            return $handler->handle($request);
        }

        $embedCode = $this->config->getEmbedCode();
        if (!filter_var($embedCode, FILTER_VALIDATE_URL) || !str_starts_with($embedCode, 'https://')) {
            return $handler->handle($request);
        }

        $scriptAttributes = [];
        if ($this->config->isConsentMode()) {
            $scriptAttributes['data-useconsent'] = 'true';
        }

        $this->assetCollector->addJavaScript('rekai_main_script', $embedCode, $scriptAttributes);

        $isBackendUser = $request->getAttribute('backend.user') !== null;
        if ($isBackendUser || $this->config->isTestMode()) {
            $this->assetCollector->addInlineJavaScript(
                'rekai_blocksaveview',
                'window.rek_blocksaveview = true;',
            );
        }

        $autocompleteMode = $this->config->getAutocompleteMode();
        $autocompleteSelector = $this->config->getAutocompleteSelector();
        if (in_array($autocompleteMode, ['auto', 'manual'], true) && $autocompleteSelector !== '') {
            $this->assetCollector->addInlineJavaScript(
                'rekai_autocomplete_init',
                $this->buildAutocompleteScript($autocompleteSelector),
            );
        }

        return $handler->handle($request);
    }

    private function buildAutocompleteScript(string $selector): string
    {
        $options = ['nrOfHits' => $this->config->getAutocompleteNrOfHits()];

        if ($this->config->isAutocompleteUseCurrentLang()) {
            $options['useLang'] = true;
        }

        $script = sprintf(
            '__rekai.ready(function() { var rekAutocomplete = rekai_autocomplete(%s, %s);',
            json_encode($selector, JSON_THROW_ON_ERROR),
            json_encode($options, JSON_THROW_ON_ERROR),
        );

        if ($this->config->isAutocompleteNavigateOnClick()) {
            $script .= ' rekAutocomplete.on("rekai_autocomplete:selected", function(event) { window.location.href = event.detail.link; });';
        }

        $script .= ' });';
        return $script;
    }
}
