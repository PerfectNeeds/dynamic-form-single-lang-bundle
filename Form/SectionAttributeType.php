<?php

namespace PN\DynamicFormBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use PN\Bundle\FormBundle\Entity\SectionAttribute;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class SectionAttributeType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('fieldName')
                ->add('type', ChoiceType::class, [
                    "placeholder" => "Please select",
                    "choices" => SectionAttribute::$types
                ])
                ->add('publish', ChoiceType::class, [
                    "placeholder" => "Please select",
                    "choices" => [
                        "Yes" => true,
                        "No" => false
                    ]
                ])
                ->add('mandatory', ChoiceType::class, [
                    "placeholder" => "Please select",
                    "choices" => [
                        "Yes" => true,
                        "No" => false
                    ]
                ])
                ->add("hint", TextType::class, ["required" => false]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'PN\DynamicFormBundle\Entity\SectionAttribute'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'pn_bundle_formbundle_sectionattribute';
    }

}
