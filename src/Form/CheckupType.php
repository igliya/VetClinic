<?php

namespace App\Form;

use App\Entity\Checkup;
use App\Entity\Pet;
use App\Entity\User;
use App\Service\CheckupDateService;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class CheckupType extends AbstractType
{
    private $security;
    private $checkupDateService;

    public function __construct(Security $security, CheckupDateService $checkupDateService)
    {
        $this->security = $security;
        $this->checkupDateService = $checkupDateService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pet', EntityType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Вы должны выбрать питомца! Если его нет в списке, то Вы всегда можете добавить его в личном кабинете',
                    ]),
                ],
                'class' => Pet::class,
                'query_builder' => function (EntityRepository $entityManager) {
                    return $entityManager->createQueryBuilder('p')
                        ->andWhere('p.owner = :owner')
                        ->setParameter('owner', $this->security->getUser()->getClient())
                        ->andWhere('p.status = true')
                        ->orderBy('p.name', 'ASC')
                        ;
                },
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'form-select',
                ],
            ])
            ->add('doctor', EntityType::class, [
                'class' => User::class,
                'query_builder' => function (EntityRepository $entityManager) {
                    return $entityManager->createQueryBuilder('u')
                        ->andWhere('CAST(u.roles as text) = \'["ROLE_DOCTOR"]\'')
                        ->orderBy('u.lastName', 'ASC')
                        ->orderBy('u.firstName', 'ASC')
                        ->orderBy('u.patronymic', 'ASC')
                        ;
                },
                'choice_label' => 'fullName',
                'attr' => [
                    'class' => 'form-select',
                ],
            ])
            ->add('date', DateType::class, [
                'constraints' => [
                    new Callback(function ($object, ExecutionContextInterface $context) {
                        $now = new \DateTime((new \DateTime('now'))->format('Y-m-d'));
                        $current = $object;
                        $lastCheckupTime = new \DateTime($current->format('Y-m-d') . ' 16:30');
                        if ($current->format('U') - $now->format('U') < 0) {
                            if (0 === $current->format('U') - (new \DateTime('1970-01-01 11:11:11'))->format('U')) {
                                $context
                                    ->buildViolation('На сегодня не осталось мест')
                                    ->addViolation();
                            } else {
                                $context
                                    ->buildViolation('Приём не может быть назначен на прошедшую дату')
                                    ->addViolation();
                            }
                        } elseif ($lastCheckupTime->format('U') - (new \DateTime())->format('U') < 0) {
                            $context
                                ->buildViolation('На сегодня приёмы завершены')
                                ->addViolation();
                        }
                    }),
                ],
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-select',
                ],
            ])
        ;
        $builder->get('date')
            ->addModelTransformer(new CallbackTransformer(
                function () {
                    return new \DateTime();
                },
                function ($date) {
                    return $this->checkupDateService->getNextDate($date);
                }
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Checkup::class,
        ]);
    }
}
