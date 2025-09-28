<?php

namespace Gcromo\Form\Admin;

use Gcromo\Form\Data\ConfigurationData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('referencePrefix', TextType::class, [
                'label' => 'Reference prefix',
                'required' => true,
            ])
            ->add('defaultStatus', ChoiceType::class, [
                'label' => 'Default status',
                'choices' => $options['status_choices'],
            ])
            ->add('defaultSalesRep', TextType::class, [
                'label' => 'Default sales representative',
                'required' => false,
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Save settings',
                'attr' => ['class' => 'btn btn-primary'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ConfigurationData::class,
            'status_choices' => [],
        ]);
    }
}
