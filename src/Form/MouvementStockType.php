<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Contrat;
use App\Entity\Couleur;
use App\Entity\Fournisseur;
use App\Entity\Hangar;
use App\Entity\Magasin;
use App\Entity\MouvementStock;
use App\Enum\SensMouvement;
use App\Enum\TypeOperationStock;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MouvementStockType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateMouvement', DateType::class, [
                'label' => 'Date',
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
            ])
            ->add('typeOperation', EnumType::class, [
                'class' => TypeOperationStock::class,
                'choice_label' => fn (TypeOperationStock $t) => $t->label(),
                'label' => 'Type d\'opération',
            ])
            ->add('sens', EnumType::class, [
                'class' => SensMouvement::class,
                'choice_label' => fn (SensMouvement $s) => $s->label(),
            ])
            ->add('article', EntityType::class, [
                'class' => Article::class,
                'choice_label' => 'libelle',
                'placeholder' => 'Article',
            ])
            ->add('couleur', EntityType::class, [
                'class' => Couleur::class,
                'choice_label' => 'libelle',
                'placeholder' => 'Couleur',
            ])
            ->add('magasin', EntityType::class, [
                'class' => Magasin::class,
                'choice_label' => 'nom',
            ])
            ->add('poids', NumberType::class, [
                'label' => 'Poids (kg)',
                'scale' => 3,
                'html5' => true,
            ])
            ->add('reference', TextType::class, ['required' => false, 'label' => 'N° BP/BS'])
            ->add('fournisseur', EntityType::class, [
                'class' => Fournisseur::class,
                'choice_label' => 'nom',
                'required' => false,
                'placeholder' => '—',
            ])
            ->add('contrat', EntityType::class, [
                'class' => Contrat::class,
                'choice_label' => 'reference',
                'required' => false,
                'placeholder' => '—',
            ])
            ->add('hangar', EntityType::class, [
                'class' => Hangar::class,
                'choice_label' => 'numero',
                'required' => false,
                'placeholder' => '—',
            ])
            ->add('observations', TextareaType::class, ['required' => false, 'attr' => ['rows' => 2]]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => MouvementStock::class]);
    }
}
