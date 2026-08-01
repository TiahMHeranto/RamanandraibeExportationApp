<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Couleur;
use App\Entity\TraitementLigne;
use App\Enum\CategorieLigneTraitement;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TraitementLigneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('article', EntityType::class, [
                'class' => Article::class,
                'choice_label' => 'libelle',
                'placeholder' => 'Article',
            ])
            ->add('couleur', EntityType::class, [
                'class' => Couleur::class,
                'choice_label' => 'libelle',
            ])
            ->add('categorie', EnumType::class, [
                'class' => CategorieLigneTraitement::class,
                'choice_label' => fn (CategorieLigneTraitement $c) => $c->label(),
            ])
            ->add('poids', NumberType::class, [
                'label' => 'Poids kg',
                'scale' => 3,
                'html5' => true,
            ])
            ->add('nombre', IntegerType::class, [
                'required' => false,
                'label' => 'Nb pièces',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => TraitementLigne::class]);
    }
}
