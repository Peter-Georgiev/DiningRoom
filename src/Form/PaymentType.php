<?php

namespace App\Form;

use App\Entity\Payment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaymentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('price',TextType::class,
                array('disabled' =>'true')
            )
            ->add('payment')
            ->add('namePayer')
            ->add('seller', TextType::class,
                array('disabled' =>'true')
            )
            //->add('datePurchases')
            //->add('lastEdit')
            //->add('products')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Payment::class,
        ]);
    }
}
