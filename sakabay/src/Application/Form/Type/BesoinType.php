<?php

namespace App\Application\Form\Type;

use Symfony\Component\Validator\Constraints\NotBlank;
use App\Domain\Model\Besoin;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;

class BesoinType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $translator = $options['translator'];

        $builder
            ->add('title', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => $translator->trans('error_message_field_not_empty'),
                    ]),
                    new Length(['max' => 191]),
                ],
                'required' => true,
            ])
            ->add('description', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => $translator->trans('error_message_field_not_empty'),
                    ]),
                    new Length([
                        'max' => 1000,
                        'maxMessage' => $translator->trans('error_message_n_length')
                    ]),
                ],
                'required' => true,
            ])
            ->add('author', EntityType::class, [
                'class' => 'App:Utilisateur',
                'constraints' => [
                    new NotNull([
                        'message' => $translator->trans('error_message_field_not_empty'),
                    ]),
                ],
            ])
            ->add('category', EntityType::class, [
                'class' => 'App:Category',
                'constraints' => [
                    new NotNull([
                        'message' => $translator->trans('error_message_field_not_empty'),
                    ]),
                ],
            ])
            ->add('sousCategorys', EntityType::class, [
                'class' => 'App:sousCategory',
                'multiple' => true,
            ])
            ->add('company', EntityType::class, [
                'class' => 'App:Company'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Besoin::class,
            'csrf_token_id'   => 'besoin'
        ]);
        $resolver->setRequired('translator');
    }
}
