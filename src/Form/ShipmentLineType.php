<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\ShipmentLine;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShipmentLineType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('product', EntityType::class, [
                'class' => Product::class,
                'choice_label' => fn (Product $product) => sprintf('%s — %s', $product->getSku(), $product->getName()),
                'placeholder' => 'Product',
            ])
            ->add('quantity', NumberType::class, [
                'scale' => 3,
                'html5' => true,
            ])
            ->add('unitPrice', NumberType::class, [
                'label' => 'Unit price',
                'scale' => 2,
                'html5' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => ShipmentLine::class]);
    }
}
