<?php

namespace App\Controller;

use App\Util\AppUtil;
use App\Util\VisitorInfoUtil;
use App\Manager\PasteManager;
use Symfony\Component\HttpFoundation\Request;
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
     * @param Request $request The request object
     *
     * @return JsonResponse The paste metrics
     */
    #[Route('/metrics/export', methods: ['GET'], name: 'metrics_export')]
    public function exportMetrics(Request $request): JsonResponse
    {
        // get time period from request
        $timePeriod = (string) $request->query->get('time_period', 'H');

        // check if metrics exporter is enabled
        if ($this->appUtil->getEnvValue('METRICS_EXPORTER_ENABLED') != 'true') {
            return $this->json(['error' => 'Metrics exporter is not enabled.'], JsonResponse::HTTP_FORBIDDEN);
        }

        // check if visitor ip is allowed to access metrics
        $allowedIp = $this->appUtil->getEnvValue('METRICS_EXPORTER_ALLOWED_IP');
        if ($allowedIp !== '%' && $this->visitorInfoUtil->getIP() !== $allowedIp) {
            return $this->json(['error' => 'Your IP is not allowed to access metrics.'], JsonResponse::HTTP_FORBIDDEN);
        }

        // return metrics data
        return $this->json([
            'pastes_count' => $this->pasteManager->getPastesCountByTimePeriod($timePeriod),
        ], JsonResponse::HTTP_OK);
    }
}
