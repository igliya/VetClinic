<?php

namespace App\Form;

use App\Entity\Checkup;
use App\Entity\Pet;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\NotBlank;

class CheckupType extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
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
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-select',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Checkup::class,
        ]);
    }
}
