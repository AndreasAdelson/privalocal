<?php

namespace App\Application\Form\Type;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use App\Domain\Model\Answer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotNull;

class AnswerSendQuoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $translator = $options['translator'];

        $builder
            ->add('messageEmail', TextType::class, [
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
            ->add('quote', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NotNull([
                        'message' => $translator->trans('error_message_field_not_empty')
                    ]),
                ]
            ])
            ->add('file', FileType::class, [
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new File([
                        'maxSize' => '20M',
                        'mimeTypes' => [
                            'application/pdf'
                        ],
                        'mimeTypesMessage' => $translator->trans('error_mime_types_message'),
                        'maxSizeMessage' => sprintf(
                            $translator->trans('error_max_size_message'),
                            '{{ size }}',
                            '{{ suffix }}',
                            '{{ limit }}',
                            '{{ suffix }}'
                        )
                    ]),
                    new NotNull([
                        'message' => $translator->trans('error_message_field_not_empty')
                    ]),
                ]
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
