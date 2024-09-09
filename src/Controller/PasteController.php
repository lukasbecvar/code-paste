<?php

namespace App\Controller;

use App\Entity\Paste;
use App\Manager\PasteManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PasteController extends AbstractController
{
    private PasteManager $pasteManager;

    public function __construct(PasteManager $pasteManager)
    {
        $this->pasteManager = $pasteManager;
    }

    #[Route('/', name: 'app_index')]
    public function index(): Response
    {
        return $this->render('save.twig');
    }

    #[Route('/save', methods:['POST'], name: 'app_save_paste')]
    public function save(Request $request): Response
    {
        $content = $request->request->get('paste-content');
        $token = $request->request->get('token');

        $this->pasteManager->savePaste($token, $content);

        return $this->redirectToRoute('app_view_paste', ['f' => $token]);
    }

    #[Route('/view', name: 'app_view_paste')]
    public function view(Request $request): Response
    {
        $paste = $this->pasteManager->getPaste($request->query->get('f'));

        return $this->render('view.twig', [
            'paste' => $paste,
        ]);
    }
}
