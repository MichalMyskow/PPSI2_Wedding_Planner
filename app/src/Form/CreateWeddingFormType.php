<?php

namespace App\Form;

use App\Entity\Room;
use App\Entity\Wedding;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateWeddingFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date')
            ->add('brideFirstName')
            ->add('brideLastName')
            ->add('groomFirstName')
            ->add('groomLastName')
            ->add('room', EntityType::class, [
                'class' => Room::class,
                'choice_label' => function ($room){
                    /** @var Room $room */
                    return sprintf('%s (%d miejsc) - %s', $room->getName(), $room->getSize(), $room->getAddress());
                },
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Wedding::class,
        ]);
    }
}
