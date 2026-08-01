<?php

namespace App\Form;

use App\Entity\Article;
use App\Enum\FamilleArticle;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code', TextType::class, ['label' => 'Code'])
            ->add('libelle', TextType::class, ['label' => 'Libellé'])
            ->add('famille', EnumType::class, [
                'class' => FamilleArticle::class,
                'choice_label' => fn (FamilleArticle $f) => $f->label(),
            ])
            ->add('actif', CheckboxType::class, ['required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Article::class]);
    }
}
