<?php

namespace App\Form;

use App\Entity\Fournisseur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FournisseurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code', TextType::class, [
                'label' => 'Code fournisseur',
                'attr' => ['placeholder' => 'ex: FMSYCHAKILA'],
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom fournisseur',
            ])
            ->add('zone', TextType::class, [
                'label' => 'Zone',
                'attr' => ['placeholder' => 'ex: MAROVOAY'],
            ])
            ->add('actif', CheckboxType::class, [
                'label' => 'Actif',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Fournisseur::class]);
    }
}
