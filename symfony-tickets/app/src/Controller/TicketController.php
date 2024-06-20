<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Form\TicketType;
use App\Repository\TicketRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[isGranted('ROLE_USER')]
#[Route('/ticket')]
class TicketController extends AbstractController
{
    #[Route('/', name: 'app_ticket_index', methods: ['GET'])]
    public function index(TicketRepository $ticketRepository): Response
    {
        if(in_array('ROLE_ADMIN', $this->getUser()->getRoles())){
            return $this->redirectToRoute('admin_user_list', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('ticket/list.html.twig', [
            'tickets' => $ticketRepository->findAll(),
            'user' => $this->getUser(),
        ]);
    }

    #[Route('/new', name: 'app_ticket_new', methods: ['GET', 'POST'])]
    public function new(Request $request, TicketRepository $ticketRepository): Response
    {
        $ticket = new Ticket();
        $form = $this->createForm(TicketType::class, $ticket);
        $form->handleRequest($request);

        $ticket->setUser($this->getUser());

        if ($form->isSubmitted() && $form->isValid()) {
            $ticketRepository->save($ticket, true);

            $session = $request->getSession();
            $session->getFlashBag()->add('success', 'Ticket ajouté avec succès !');

            return $this->redirectToRoute('app_ticket_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('ticket/new.html.twig', [
            'ticket' => $ticket,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_ticket_show', methods: ['GET'])]
    public function show(Ticket $ticket): Response
    {
        return $this->render('ticket/show.html.twig', [
            'ticket' => $ticket,
            'user' => $this->getUser(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_ticket_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Ticket $ticket, TicketRepository $ticketRepository): Response
    {
        //condition pour verifier le proprietaire du ticket et verif si pas admin
        if($ticket->getUser() !== $this->getUser() && !in_array('ROLE_ADMIN', $this->getUser()->getRoles())){
            $session = $request->getSession();
            $session->getFlashBag()->add('danger', 'Ce ticket ne vous appartient pas !');
            return $this->redirectToRoute('app_ticket_index', [], Response::HTTP_SEE_OTHER);
        }

        $form = $this->createForm(TicketType::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ticketRepository->save($ticket, true);

            $session = $request->getSession();
            $session->getFlashBag()->add('success', 'Ticket modifié avec succès !');

            return $this->redirectToRoute('app_ticket_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('ticket/edit.html.twig', [
            'ticket' => $ticket,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_ticket_delete', methods: ['POST'])]
    public function delete(Request $request, Ticket $ticket, TicketRepository $ticketRepository): Response
    {
        if($ticket->getUser() !== $this->getUser()){
            $session = $request->getSession();
            $session->getFlashBag()->add('danger', 'Ce ticket ne vous appartient pas !');
            return $this->redirectToRoute('app_ticket_index', [], Response::HTTP_SEE_OTHER);
        }

        if ($this->isCsrfTokenValid('delete'.$ticket->getId(), $request->request->get('_token'))) {
            $ticketRepository->remove($ticket, true);
            $session = $request->getSession();
            $session->getFlashBag()->add('danger', 'Ticket supprimé avec succès !');
        }
        return $this->redirectToRoute('app_ticket_index', [], Response::HTTP_SEE_OTHER);
    }
}
