<?php

namespace App\Application\Form\Type;

use Symfony\Component\Validator\Constraints\NotBlank;
use App\Domain\Model\Company;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Constraints\Luhn;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;

class CompanyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $translator = $options['translator'];

        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => $translator->trans('error_message_field_not_empty'),
                    ]),
                ],
                'required' => true,
            ])
            ->add('numSiret', TextType::class, [
                'constraints' => [new Luhn()],
                'required' => true,
            ])
            ->add('category', EntityType::class, [
                'class' => 'App:Category',
                'constraints' => [
                    new NotNull([
                        'message' => $translator->trans('error_message_field_not_empty'),
                    ]),
                ],
                'required' => true,
            ])
            ->add('utilisateur', EntityType::class, [
                'class' => 'App:Utilisateur',
                'constraints' => [
                    new NotNull([
                        'message' => $translator->trans('error_message_field_not_empty'),
                    ]),
                ],
                'required' => true,
            ])
            ->add('address', AddressType::class, [
                'constraints' => [new Valid()],
                'translator' => $translator,
                'required' => true,
                'by_reference' => true
            ])
            ->add('city', EntityType::class, [
                'class' => 'App:City',
                'constraints' => [
                    new NotNull([
                        'message' => $translator->trans('error_message_field_not_empty_city'),
                    ]),

                ],
                'required' => true,
                'multiple' => false
            ])
            ->add('email', EmailType::class, [
                'constraints' => [
                    new NotNull([
                        'message' => $translator->trans('error_message_field_not_empty')
                    ]),
                ]
            ])
            ->add('sousCategorys', EntityType::class, [
                'class' => 'App:SousCategory',
                'multiple' => true,
                'required' => true
            ]);;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Company::class,
            'csrf_token_id'   => 'company'
        ]);
        $resolver->setRequired('translator');
    }
}
