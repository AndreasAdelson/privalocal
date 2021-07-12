<?php

namespace App\Application\Form\Type;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use App\Domain\Model\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $translator = $options['translator'];

        $builder
            ->add('postalAddress', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => $translator->trans('error_message_field_not_empty')
                    ]),
                    new Length(['max' => 191]),
                ],
                'required' => true,
            ])
            ->add('postalCode', NumberType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => $translator->trans('error_message_field_not_empty'),
                    ]),
                    new Length(['max' => 5]),
                ],
                'required' => true,
            ])->add('latitude', NumberType::class, [
                'constraints' => [
                    new NotNull([
                        'message' => $translator->trans('error_message_field_not_empty')
                    ]),
                    new Length(['max' => 11])
                ]
            ])
            ->add('longitude', NumberType::class, [
                'constraints' => [
                    new NotNull([
                        'message' => $translator->trans('error_message_field_not_empty')
                    ]),
                    new Length(['max' => 11])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
            'csrf_token_id'   => 'address'
        ]);
        $resolver->setRequired('translator');
    }
}
