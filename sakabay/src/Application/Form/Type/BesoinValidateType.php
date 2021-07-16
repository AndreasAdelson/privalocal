<?php

namespace App\Application\Form\Type;

use App\Domain\Model\Besoin;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;

class BesoinValidateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $translator = $options['translator'];

        $builder
            ->add('besoinStatut', EntityType::class, [
                'class' => 'App:BesoinStatut',
                'constraints' => [
                    new NotNull([
                        'message' => $translator->trans('error_message_field_not_empty'),
                    ]),
                ],
                'required' => true,
            ])
            ->add('companySelected', EntityType::class, [
                'class' => 'App:Company',
                'constraints' => [
                    new NotNull([
                        'message' => $translator->trans('error_message_field_not_empty'),
                    ]),
                ],
                'required' => true
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
