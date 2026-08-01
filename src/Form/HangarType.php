<?php

namespace App\Form;

use App\Entity\Hangar;
use App\Entity\Magasin;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HangarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numero', TextType::class, ['label' => 'Hangar numéro'])
            ->add('code', TextType::class, ['label' => 'Code'])
            ->add('magasin', EntityType::class, [
                'class' => Magasin::class,
                'choice_label' => 'nom',
                'required' => false,
                'placeholder' => '—',
            ])
            ->add('actif', CheckboxType::class, ['label' => 'Actif', 'required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Hangar::class]);
    }
}
