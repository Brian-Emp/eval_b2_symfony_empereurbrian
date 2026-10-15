<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Event;
use App\Form\EventType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class EventController extends AbstractController
{
    #[Route('/evenement/ajouter', name: 'app_event_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // 2. s'il y en a un : le déplacer + stocker son nom dans $event
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );
                $event->setImage($newFilename);
            }
            $em->persist($event);
            $em->flush();

            $this->addFlash('success', 'Votre événement a bien été ajouté.');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('event/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/evenement/{id}', name: 'app_event_show', requirements: ['id' => '\d+'])]
    public function show(Event $event): Response
    {
        return $this->render('event/show.html.twig', [
            'event' => $event,
        ]);
    }

    #[Route('/evenement/{id}/inscription', name: 'app_event_register', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function register(Event $event, Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->isCsrfTokenValid('validate' . $event->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException();
        }
        if ($event->isComplet()) {
            $this->addFlash('error', 'Cet événement est complet.');
            return $this->redirectToRoute('app_event_show', ['id' => $event->getId()]);
        }
        $event->addInscrit($this->getUser());
        $em->flush();
        $this->addFlash('success', 'Vous êtes inscrit à l\'événement avec succès.');
        return $this->redirectToRoute('app_event_show', ['id' => $event->getId()]);
    }


}
