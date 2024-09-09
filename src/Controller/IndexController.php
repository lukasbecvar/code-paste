<?php

namespace App\Controller;

use App\Entity\Paste;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\String\ByteString;

class IndexController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'app_index')]
    public function index(): Response
    {
        return $this->render('save.twig');
    }

    #[Route('/save', methods:['POST'], name: 'app_save')]
    public function save(Request $request): Response
    {
        $content = $request->request->get('paste-content');

        $content = htmlspecialchars($content);

        $paste = new Paste();


        $paste->setToken($request->request->get('filename'));
        $paste->setContent($content);
        $paste->setTime(new \DateTime());

        $this->entityManager->persist($paste);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_view', ['f' => $paste->getToken()]);

    }

    #[Route('/view', name: 'app_view')]
    public function view(Request $request): Response
    {
        $paste = $this->entityManager->getRepository(Paste::class)->findOneBy(['token' => $request->query->get('f')]);
       
        return $this->render('view.twig', [
            'paste' => $paste->getContent(),
        ]);
    }
}
