<?php
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
            $attrs['data-srek'] = $secretKey;
        }
        if ($this->config->isMockDataEnabled()) {
            $attrs['data-advanced_mockdata'] = 'true';
        }
        return $attrs;
    }

    protected function getCurrentUid(): int
    {
        return (int)($this->request->getAttribute('currentContentObject')?->data['uid'] ?? 0);
    }

    protected function assignContentData(): void
    {
        $this->view->assign('data', $this->request->getAttribute('currentContentObject')?->data ?? []);
    }
}
