<?php
// Classes/Controller/AbstractRekaiController.php
declare(strict_types=1);

namespace Pluswerk\Rekai\Controller;

use Pluswerk\Rekai\Configuration\ExtensionConfigurationService;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

abstract class AbstractRekaiController extends ActionController
{
    protected ExtensionConfigurationService $config;

    public function injectExtensionConfigurationService(ExtensionConfigurationService $config): void
    {
        $this->config = $config;
    }

    /**
     * Builds a <div> HTML string with all configured data-* attributes.
     * Only attributes with a value are output — empty values are omitted.
     */
    protected function buildDivHtml(array $attrs): string
    {
        $attrString = implode(
            ' ',
            array_map(
                static fn(string $k, string $v): string => sprintf(
                    '%s="%s"',
                    htmlspecialchars($k, ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                    htmlspecialchars($v, ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                ),
                array_keys($attrs),
                array_values($attrs),
            ),
        );
        return '<div ' . $attrString . '></div>';
    }

    /**
     * Returns shared filter attributes (same for both content elements).
     */
    protected function buildFilterAttributes(array $settings): array
    {
        $attrs = [];
        foreach ([
            'subtree' => 'data-subtree',
            'excludeTree' => 'data-excludetree',
            'allowedLangs' => 'data-allowedlangs',
            'tags' => 'data-tags',
        ] as $key => $dataAttr) {
            if (!empty($settings[$key])) {
                $attrs[$dataAttr] = (string)$settings[$key];
            }
        }
        return $attrs;
    }

    /**
     * Returns test mode attributes if testMode is active in Extension Configuration.
     */
    protected function buildTestModeAttributes(): array
    {
        if (!$this->config->isTestMode()) {
            return [];
        }
        $attrs = [];
        $projectId = $this->config->getProjectId();
        $secretKey = $this->config->getSecretKey();
        if ($projectId !== '') {
            $attrs['data-projectid'] = $projectId;
        }
        if ($secretKey !== '') {
            $attrs['data-secretkey'] = $secretKey;
        }
        return $attrs;
    }

    /**
     * Reads the UID of the current content element from the request.
     */
    protected function getCurrentUid(): int
    {
        return (int)($this->request->getAttribute('currentContentObject')?->data['uid'] ?? 0);
    }
}
