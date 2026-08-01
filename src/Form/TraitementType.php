<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Contrat;
use App\Entity\Couleur;
use App\Entity\Fournisseur;
use App\Entity\Hangar;
use App\Entity\Magasin;
use App\Entity\Personnel;
use App\Entity\Traitement;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TraitementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('reference', TextType::class, ['label' => 'Référence'])
            ->add('dateTraitement', DateType::class, [
                'label' => 'Date',
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
            ])
            ->add('hangar', EntityType::class, [
                'class' => Hangar::class,
                'choice_label' => fn (Hangar $h) => (string) $h,
            ])
            ->add('trieuse', EntityType::class, [
                'class' => Personnel::class,
                'choice_label' => fn (Personnel $p) => (string) $p,
                'label' => 'Trieuse',
            ])
            ->add('controleuse', EntityType::class, [
                'class' => Personnel::class,
                'choice_label' => fn (Personnel $p) => (string) $p,
                'required' => false,
                'placeholder' => '—',
                'label' => 'Contrôleuse',
            ])
            ->add('fournisseur', EntityType::class, [
                'class' => Fournisseur::class,
                'choice_label' => fn (Fournisseur $f) => (string) $f,
            ])
            ->add('contrat', EntityType::class, [
                'class' => Contrat::class,
                'choice_label' => 'reference',
                'required' => false,
                'placeholder' => '—',
            ])
            ->add('articleSource', EntityType::class, [
                'class' => Article::class,
                'choice_label' => 'libelle',
                'label' => 'Produit sorti',
            ])
            ->add('couleurSource', EntityType::class, [
                'class' => Couleur::class,
                'choice_label' => 'libelle',
                'label' => 'Couleur sortie',
            ])
            ->add('magasin', EntityType::class, [
                'class' => Magasin::class,
                'choice_label' => 'nom',
            ])
            ->add('poidsSortie', NumberType::class, [
                'label' => 'Poids sortie (kg)',
                'scale' => 3,
                'html5' => true,
            ])
            ->add('observations', TextareaType::class, ['required' => false, 'attr' => ['rows' => 2]])
            ->add('lignes', CollectionType::class, [
                'entry_type' => TraitementLigneType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => 'Résultats (entrées)',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Traitement::class]);
    }
}
