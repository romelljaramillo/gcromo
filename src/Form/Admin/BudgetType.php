<?php

namespace Gcromo\Form\Admin;

use Gcromo\Form\Data\BudgetData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BudgetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quoteReference', TextType::class, [
                'label' => 'Quote reference',
                'required' => false,
                'disabled' => true,
            ])
            ->add('quoteDate', DateType::class, [
                'label' => 'Quote date',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('customerId', ChoiceType::class, [
                'label' => 'Customer',
                'choices' => $options['customer_choices'],
                'placeholder' => 'Select a customer',
                'required' => false,
            ])
            ->add('productName', TextType::class, [
                'label' => 'Product name',
                'required' => false,
            ])
            ->add('productSummary', TextareaType::class, [
                'label' => 'Product description',
                'required' => false,
            ])
            ->add('workScope', TextType::class, [
                'label' => 'Work scope',
                'required' => false,
            ])
            ->add('dimensionHeightCm', NumberType::class, [
                'label' => 'Height (cm)',
                'required' => false,
                'scale' => 2,
            ])
            ->add('dimensionWidthPrimaryCm', NumberType::class, [
                'label' => 'Primary width (cm)',
                'required' => false,
                'scale' => 2,
            ])
            ->add('dimensionWidthSecondaryCm', NumberType::class, [
                'label' => 'Secondary width (cm)',
                'required' => false,
                'scale' => 2,
            ])
            ->add('salesRep', TextType::class, [
                'label' => 'Sales representative',
                'required' => false,
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'Status',
                'choices' => $options['status_choices'],
                'required' => true,
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Save',
                'attr' => ['class' => 'btn btn-primary'],
            ])
            ->add('saveAndStay', SubmitType::class, [
                'label' => 'Save and stay',
                'attr' => ['class' => 'btn btn-outline-primary'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BudgetData::class,
            'status_choices' => [],
            'customer_choices' => [],
        ]);
    }
}
