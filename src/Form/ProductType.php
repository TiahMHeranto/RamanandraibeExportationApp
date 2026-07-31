<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class)
            ->add('sku', TextType::class, ['label' => 'SKU'])
            ->add('category', ChoiceType::class, [
                'choices' => [
                    'Vanilla' => 'Vanilla',
                    'Spices' => 'Spices',
                    'Cocoa' => 'Cocoa',
                    'Essential oils' => 'Essential oils',
                    'Other' => 'Other',
                ],
            ])
            ->add('unit', ChoiceType::class, [
                'choices' => [
                    'kg' => 'kg',
                    'ton' => 'ton',
                    'liter' => 'liter',
                    'box' => 'box',
                ],
            ])
            ->add('unitPrice', NumberType::class, [
                'label' => 'Unit price (USD)',
                'scale' => 2,
                'html5' => true,
            ])
            ->add('description', TextareaType::class, ['required' => false, 'attr' => ['rows' => 3]])
            ->add('active', CheckboxType::class, ['required' => false, 'label' => 'Active product']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Product::class]);
    }
}
