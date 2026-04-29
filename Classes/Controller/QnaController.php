<?php
// Classes/Controller/QnaController.php
declare(strict_types=1);

namespace Pluswerk\Rekai\Controller;

use Psr\Http\Message\ResponseInterface;

final class QnaController extends AbstractRekaiController
{
    public function showAction(): ResponseInterface
    {
        $settings = $this->settings;
        $uid = $this->getCurrentUid();
        $id = 'rekai-' . $uid;

        $attrs = [
            'class' => 'rek-prediction',
            'id' => $id,
            'data-entitytype' => 'rekai-qna',
            'data-selector' => '#' . $id,
            'data-nrofhits' => (string)max(1, min(100, (int)($settings['nrOfHits'] ?? 10))),
        ];

        if (!empty($settings['currentPageQuestions'])) {
            $attrs['data-currentpagequestions'] = 'true';
        }

        $attrs = array_merge($attrs, $this->buildFilterAttributes($settings));
        $attrs = array_merge($attrs, $this->buildTestModeAttributes());

        $this->view->assign('divHtml', $this->buildDivHtml($attrs));
        return $this->htmlResponse();
    }
}
