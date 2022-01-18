<?php

namespace App\Form;

use App\Entity\Guest;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GuestConflictFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var User $user */
        $user = $options['user'];

        /** @var Guest $actualGuest */
        $actualGuest = $options['actualGuest'];

        $builder
            ->add('conflictedGuests', EntityType::class, [
                'class' => Guest::class,
                'query_builder' => function (EntityRepository $er) use ($actualGuest, $user) {
                    return $er->createQueryBuilder('g')
                        ->innerJoin(Guest::class, 'ag', Join::WITH, 'ag.id = :guestId')
                        ->andWhere('g.id != :guestId')
                        ->setParameter('guestId', $actualGuest->getId())
                        ->andWhere('g.wedding = :wedding')
                        ->setParameter('wedding', $user->getWedding());
                },
                'choice_label' => function ($guest) {
                    /* @var Guest $guest */
                    return sprintf('%s %s', $guest->getLastName(), $guest->getFirstName());
                },
                'multiple' => true,
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Guest::class,
            'actualGuest' => null,
            'user' => null,
        ]);
    }
}
