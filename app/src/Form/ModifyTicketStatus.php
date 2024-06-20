<?php

namespace App\Form;

use App\Entity\Ticket;
use App\Entity\TicketStatus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ModifyTicketStatus extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ticketStatus', EntityType::class, [
                'class' => TicketStatus::class,
                'choice_label' => 'label',
                'label' => false,
            ])
            ->add('submitButton', SubmitType::class, [
                'label' => 'ok',
    ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ticket::class,
        ]);
    }
}