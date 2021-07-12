<?php

namespace App\Application\Form\Type;

use Symfony\Component\Validator\Constraints\NotBlank;
use App\Domain\Model\CompanySubscription;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;

class CompanySubscriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $translator = $options['translator'];

        $builder
            ->add('dtDebut', DateType::class, [

                'widget' => 'single_text',
                'format' => 'yyyy/MM/dd HH:mm:ss',
                'constraints' => [
                    new NotNull([
                        'message' => $translator->trans('error_message_field_not_empty'),
                    ]),
                ],
            ])
            ->add('dtFin', DateType::class, [
                'widget' => 'single_text',
                'format' => 'yyyy/MM/dd HH:mm:ss',
                'constraints' => [
                    new NotNull([
                        'message' => $translator->trans('error_message_field_not_empty'),
                    ]),

                ],
            ])
            ->add('subscription', EntityType::class, [
                'class' => 'App:Subscription',
                'constraints' => [
                    new NotNull([
                        'message' => $translator->trans('error_message_field_not_empty'),
                    ]),
                ],
                'required' => true,
            ])
            ->add('company', EntityType::class, [
                'class' => 'App:Company',
                'constraints' => [
                    new NotNull([
                        'message' => $translator->trans('error_message_field_not_empty'),
                    ]),
                ],
                'required' => true,
            ])
            ->add('stripeId', TextType::class, [
                'constraints' => [
                    new Length(['max' => 100]),
                ],
                'required' => false,
            ])
            ->add('subscriptionStatus', EntityType::class, [
                'class' => 'App:SubscriptionStatus',
                'constraints' => [
                    new NotNull([
                        'message' => $translator->trans('error_message_field_not_empty'),
                    ]),
                ],
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CompanySubscription::class,
            'csrf_token_id'   => 'companySubscription'
        ]);
        $resolver->setRequired('translator');
    }
}
