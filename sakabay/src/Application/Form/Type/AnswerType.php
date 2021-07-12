<?php

namespace App\Application\Form\Type;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use App\Domain\Model\Answer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;

class AnswerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $translator = $options['translator'];

        $builder
            ->add('message', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => $translator->trans('error_message_field_not_empty')
                    ]),
                    new Length([
                        'max' => 1500,
                        'maxMessage' => $translator->trans('error_message_n_length')
                    ]),
                ],
                'required' => true,
            ])
            ->add('company', EntityType::class, [
                'class' => 'App:Company',
                'constraints' => [
                    new NotBlank([
                        'message' => $translator->trans('error_message_field_not_empty'),
                    ])
                ],
                'required' => true,
            ])->add('besoin', EntityType::class, [
                'class' => 'App:Besoin',
                'constraints' => [
                    new NotNull([
                        'message' => $translator->trans('error_message_field_not_empty')
                    ])
                ],
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Answer::class,
            'csrf_token_id'   => 'answer'
        ]);
        $resolver->setRequired('translator');
    }
}
