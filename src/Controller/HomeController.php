<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\EventRepository;
final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, EventRepository $eventRepository): Response
    {
        $categorie = $request->query->get('categorie');
        return $this->render('home/index.html.twig', [
            'events' => $eventRepository->findUpcoming($categorie),
            'categorieActive' => $categorie,
        ]);
    }
}
