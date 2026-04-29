<?php
// Classes/Controller/RecommendationsController.php
declare(strict_types=1);

namespace Pluswerk\Rekai\Controller;

use Psr\Http\Message\ResponseInterface;

final class RecommendationsController extends AbstractRekaiController
{
    public function showAction(): ResponseInterface
    {
        $settings = $this->settings;
        $uid = $this->getCurrentUid();
        $id = 'rekai-' . $uid;

        $attrs = [
            'class' => 'rek-prediction',
            'id' => $id,
            'data-selector' => '#' . $id,
            'data-nrofhits' => (string)max(1, min(100, (int)($settings['nrOfHits'] ?? 10))),
        ];

        if (!empty($settings['renderStyle'])) {
            $attrs['data-renderstyle'] = $settings['renderStyle'];
        }
        if ((bool)($settings['showImage'] ?? false)) {
            $attrs['data-showimage'] = 'true';
        }
        if ((bool)($settings['showIngress'] ?? false)) {
            $attrs['data-showingress'] = 'true';
        }
        if ((int)($settings['cols'] ?? 0) > 0) {
            $attrs['data-cols'] = (string)(int)$settings['cols'];
        }

        $attrs = array_merge($attrs, $this->buildFilterAttributes($settings));
        $attrs = array_merge($attrs, $this->buildTestModeAttributes());

        $this->assignContentData();
        $this->view->assign('divHtml', $this->buildDivHtml($attrs));
        return $this->htmlResponse();
    }
}
