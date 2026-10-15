<?php

namespace App\Controller;

use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Event;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

final class ValidateController extends AbstractController
{
    #[Route('/validate', name: 'app_validate')]
    public function index(EventRepository $eventRepository): Response
    {
        $events = $eventRepository->findBy(['valide' => false], ['date' => 'ASC']);

        return $this->render('validate/index.html.twig', [
            'events' => $events,
        ]);
    }

    #[Route('/validate/{id}', name: 'app_validate_event', methods: ['POST'])]
    public function validate(Event $event, EntityManagerInterface $em, Request $request): Response
    {
        if (!$this->isCsrfTokenValid('validate' . $event->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException();
        }

        $event->setValide(true);
        $em->flush();

        $this->addFlash('success', 'L\'événement a été validé avec succès.');

        return $this->redirectToRoute('app_validate');
    }
}
