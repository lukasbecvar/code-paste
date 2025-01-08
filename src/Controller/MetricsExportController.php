<?php

namespace App\Controller;

use App\Util\AppUtil;
use App\Manager\PasteManager;
use App\Util\VisitorInfoUtil;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class MetricsExportController
 *
 * Controller for exporting paste metrics
 *
 * @package App\Controller
 */
class MetricsExportController extends AbstractController
{
    private AppUtil $appUtil;
    private PasteManager $pasteManager;
    private VisitorInfoUtil $visitorInfoUtil;

    public function __construct(AppUtil $appUtil, PasteManager $pasteManager, VisitorInfoUtil $visitorInfoUtil)
    {
        $this->appUtil = $appUtil;
        $this->pasteManager = $pasteManager;
        $this->visitorInfoUtil = $visitorInfoUtil;
    }

    /**
     * Export paste metrics
     *
     * @return JsonResponse The paste metrics
     */
    #[Route('/metrics/export', methods: ['GET'], name: 'metrics_export')]
    public function exportMetrics(): JsonResponse
    {
        // check if metrics exporter is enabled
        if ($this->appUtil->getEnvValue('METRICS_EXPORTER_ENABLED') != 'true') {
            return $this->json(['error' => 'Metrics exporter is not enabled.'], JsonResponse::HTTP_FORBIDDEN);
        }

        // check if visitor ip is allowed to access metrics
        if ($this->visitorInfoUtil->getIP() !== $this->appUtil->getEnvValue('METRICS_EXPORTER_ALLOWED_IP')) {
            return $this->json(['error' => 'Your IP is not allowed to access metrics.'], JsonResponse::HTTP_FORBIDDEN);
        }

        // return metrics data
        return $this->json([
            'pastes_count' => $this->pasteManager->getPastesCount(),
            'total_paste_views' => $this->pasteManager->getTotalViews()
        ], JsonResponse::HTTP_OK);
    }
}
