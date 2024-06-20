<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Entity\TicketStatus;
use App\Entity\User;
use App\Form\ModifyTicketStatus;
use App\Repository\TicketRepository;
use App\Repository\TicketStatusRepository;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[isGranted('ROLE_ADMIN')]
#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route('/users', name: 'admin_user_list', methods: ['GET'])]
    public function listUsers(UserRepository $userRepository): Response
    {
        return $this->render('admin/list_user.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/user/{id}/tickets', name: 'admin_user_ticket_list', methods: ['GET', 'POST'])]
    public function showUserTickets(TicketRepository $ticketRepository, User $user): Response
    {
        $ticketList = $ticketRepository->findBy(['user' => $user]);
        $formStatus = array();
        foreach ($ticketList as $ticket){
            $formStatus[$ticket->getId()] = $this->createForm(ModifyTicketStatus::class, $ticket, [
                'action' => '/admin/user/'. $user->getId() .'/ticket/'.$ticket->getId().'/update'
            ]);
        }

        return $this->renderForm('ticket/index.html.twig', [
            'tickets' => $ticketRepository->findBy(['user' => $user]),
            'user' => $this->getUser(),
            'formStatus' => array_map(function ($form){
                return $form->createView();
            }, $formStatus),
        ]);
    }

    /**
     * @ParamConverter("ticket", options={"id" = "ticketId"})
     */
    #[Route('/user/{id}/ticket/{ticketId}/update', name: 'admin_ticket_modify_status', methods: ['POST'])]
    public function modifyStatus(Request $request, TicketRepository $ticketRepository, TicketStatusRepository $ticketStatusRepository, User $user, Ticket $ticket): Response
    {
        $ticketId = $request->get('ticketId');
        $ticket = $ticketRepository->findOneBy(['id' => $ticketId]);
        $statusId = $request->get('modify_ticket_status')['ticketStatus'];
        $ticket->setTicketStatus($ticketStatusRepository->find($statusId));
        $ticketRepository->save($ticket, true);

        return $this->redirectToRoute('admin_user_ticket_list', [
            'id' => $user->getId(),
        ], Response::HTTP_SEE_OTHER);
    }
}