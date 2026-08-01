<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Arrivage;
use App\Entity\Contrat;
use App\Entity\Couleur;
use App\Entity\Fournisseur;
use App\Entity\Magasin;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArrivageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numero', TextType::class, ['label' => 'N°'])
            ->add('fournisseur', EntityType::class, [
                'class' => Fournisseur::class,
                'choice_label' => fn (Fournisseur $f) => sprintf('%s — %s (%s)', $f->getCode(), $f->getNom(), $f->getZone()),
                'placeholder' => 'Sélectionner un fournisseur',
            ])
            ->add('origine', TextType::class, ['label' => 'Origine'])
            ->add('article', EntityType::class, [
                'class' => Article::class,
                'choice_label' => 'libelle',
                'label' => 'Article (TV)',
                'placeholder' => 'Produit',
            ])
            ->add('couleur', EntityType::class, [
                'class' => Couleur::class,
                'choice_label' => 'libelle',
            ])
            ->add('magasin', EntityType::class, [
                'class' => Magasin::class,
                'choice_label' => 'nom',
            ])
            ->add('contrat', EntityType::class, [
                'class' => Contrat::class,
                'choice_label' => 'reference',
                'required' => false,
                'placeholder' => '—',
            ])
            ->add('poids', NumberType::class, [
                'label' => 'Poids (kg)',
                'scale' => 3,
                'html5' => true,
            ])
            ->add('dateArrivage', DateType::class, [
                'label' => 'Date',
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Arrivage::class]);
    }
}
