<?php

namespace App\Form;

use App\Entity\Room;
use App\Entity\Wedding;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WeddingFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $years = [];

        for ($i = 0; $i <= 5; ++$i) {
            $years[] = (new \DateTime(sprintf('+%d years', $i)))->format('Y');
        }

        $builder
            ->add('date', DateTimeType::class, [
                'years' => $years,
            ])
            ->add('brideFirstName')
            ->add('brideLastName')
            ->add('groomFirstName')
            ->add('groomLastName')
            ->add('room', EntityType::class, [
                'class' => Room::class,
                'choice_label' => function ($room) {
                    /* @var Room $room */
                    return sprintf('%s (%d miejsc) - ul. %s %s %s %s', $room->getName(), $room->getSize(),
                        $room->getStreet(), $room->getHouseNumber(), $room->getPostcode(), $room->getAddress());
                },
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Wedding::class,
        ]);
    }
}
