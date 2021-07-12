<?php

namespace App\Application\Form\Type;

use App\Domain\Model\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $translator = $options['translator'];
        $builder
            ->add('username', TextType::class, [
                'constraints' => [
                    new Assert\NotNull([
                        'message' => $translator->trans('error_message_field_not_empty')
                    ]),
                ]
            ])
            ->add('firstName', TextType::class, [
                'constraints' => [
                    new Assert\NotNull([
                        'message' => $translator->trans('error_message_field_not_empty')
                    ]),
                ]
            ])
            ->add('lastName', TextType::class, [
                'constraints' => [
                    new Assert\NotNull([
                        'message' => $translator->trans('error_message_field_not_empty')
                    ]),
                ]
            ])
            ->add('email', EmailType::class, [
                'constraints' => [
                    new Assert\NotNull([
                        'message' => $translator->trans('error_message_field_not_empty')
                    ]),
                ]
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => $translator->trans('error_message_password_must_match'),
                'required' => true,
                'first_options'  => ['label' => false],
                'second_options' => ['label' => false],
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => $translator->trans('error_message_field_not_empty'),
                    ]),
                    new Assert\Length([
                        'min' => 6,
                        'minMessage' => $translator->trans('error_message_password_length'),
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new Assert\IsTrue([
                        'message' => $translator->trans('error_message_agree_terms'),
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
            'csrf_token_id'   => 'registration'
        ]);
        $resolver->setRequired('translator');
    }
}
