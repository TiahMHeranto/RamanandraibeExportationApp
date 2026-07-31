<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\Shipment;
use App\Enum\ShipmentStatus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShipmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('reference', TextType::class, ['label' => 'Reference'])
            ->add('client', EntityType::class, [
                'class' => Client::class,
                'choice_label' => 'companyName',
                'placeholder' => 'Select client',
            ])
            ->add('status', EnumType::class, [
                'class' => ShipmentStatus::class,
                'choice_label' => fn (ShipmentStatus $status) => $status->label(),
            ])
            ->add('originPort', TextType::class, ['label' => 'Origin port'])
            ->add('destinationPort', TextType::class, ['label' => 'Destination port'])
            ->add('departureDate', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
                'input' => 'datetime_immutable',
            ])
            ->add('arrivalDate', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
                'input' => 'datetime_immutable',
            ])
            ->add('notes', TextareaType::class, ['required' => false, 'attr' => ['rows' => 3]])
            ->add('lines', CollectionType::class, [
                'entry_type' => ShipmentLineType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => 'Cargo lines',
                'attr' => ['class' => 'lines-collection'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Shipment::class]);
    }
}
