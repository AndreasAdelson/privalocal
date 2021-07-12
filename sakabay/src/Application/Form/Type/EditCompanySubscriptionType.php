<?php

namespace App\Application\Form\Type;

use App\Application\Utils\DateUtils;
use App\Domain\Model\CompanySubscription;
use DateTime;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class EditCompanySubscriptionType extends AbstractType
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
                    new Callback(function (
                        ?DateTime $submittedValue,
                        ExecutionContextInterface $context
                    ) use ($translator) {
                        $errorMessage = $translator->trans('error_message_start_before_end');
                        $dtFin = $context->getRoot()->get('dtFin')->getData();
                        if ($submittedValue && $dtFin && $submittedValue > $dtFin) {
                            $context->buildViolation($errorMessage)->addViolation();
                        }
                    }),
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
            ->add('subscriptionStatus', EntityType::class, [
                'class' => 'App:SubscriptionStatus',
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
