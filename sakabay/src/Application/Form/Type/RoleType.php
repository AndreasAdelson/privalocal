<?php

namespace App\Application\Form\Type;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Count;
use App\Domain\Model\Role;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoleType extends AbstractType
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
                'attr' => ['class' => 'form-control'],
            ])
            ->add('code', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => $translator->trans('error_message_field_not_empty'),
                    ]),
                ],
                'required' => true,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('fonctions', EntityType::class, [
                'class' => 'App:Fonction',
                'constraints' => [
                    new Count([
                        'min' => 1,
                        'minMessage' => $translator->trans('error_message_field_not_empty')
                    ])
                ],
                'multiple' => true,
            ])
            ->add('groups', EntityType::class, [
                'by_reference' => false,
                'class' => 'App:Group',
                'constraints' => [],
                'multiple' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Role::class,
            'csrf_token_id'   => 'role'
        ]);
        $resolver->setRequired('translator');
    }
}
