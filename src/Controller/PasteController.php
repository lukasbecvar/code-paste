<?php

namespace App\Controller;

use Exception;
use App\Manager\PasteManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class PasteController
 *
 * Main controller for code paste save/view functionality
 *
 * @package App\Controller
 */
class PasteController extends AbstractController
{
    private PasteManager $pasteManager;

    public function __construct(PasteManager $pasteManager)
    {
        $this->pasteManager = $pasteManager;
    }

    /**
     * Index action (show save paste view)
     *
     * @return Response The save paste page response
     */
    #[Route('/', name: 'app_index')]
    public function index(): Response
    {
        return $this->render('save.twig');
    }

    /**
     * Save paste to database (API endpoint)
     *
     * @param Request $request The request object
     *
     * @return Response The json status response
     */
    #[Route('/save', methods:['POST'], name: 'app_save_paste')]
    public function save(Request $request): Response
    {
        // get paste data
        $content = (string) $request->request->get('paste-content');
        $token = (string) $request->request->get('token');

        try {
            // save paste
            $this->pasteManager->savePaste($token, $content);

            // return success response
            return $this->json([
                'code' => Response::HTTP_OK,
                'status' => 'success',
                'message' => 'Paste saved successfully',
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->json([
                'code' => Response::HTTP_BAD_REQUEST,
                'status' => 'error',
                'message' => 'Error to save paste: ' . $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * View paste paste by token from database
     *
     * @param Request $request The request object
     *
     * @return Response The paste view response
     */
    #[Route('/view', name: 'app_view_paste')]
    public function view(Request $request): Response
    {
        // get paste file from query string
        $pasteFile = (string) $request->request->get('f');

        // get paste content
        $paste = $this->pasteManager->getPaste($pasteFile);

        // return paste view
        return $this->render('view.twig', [
            'paste' => $paste,
        ]);
    }
}
