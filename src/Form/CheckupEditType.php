<?php

namespace App\Form;

use App\Entity\Checkup;
use App\Entity\Service;
use App\Service\CheckupDateService;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class CheckupEditType extends AbstractType
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
            ->add('complaints', TextareaType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Заполните жалобы',
                    ]),
                    new Length([
                        'max' => 5000,
                        'maxMessage' => 'Длина текста жалоб не должна превышать {{ limit }} символов',
                    ]),
                ],
            ])
            ->add('diagnosis', TextareaType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Заполните диагноз',
                    ]),
                    new Length([
                        'max' => 5000,
                        'maxMessage' => 'Длина текста диагноза не должна превышать {{ limit }} символов',
                    ]),
                ],
            ])
            ->add('treatment', TextareaType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Заполните лечение',
                    ]),
                    new Length([
                        'max' => 5000,
                        'maxMessage' => 'Длина текста лечения не должна превышать {{ limit }} символов',
                    ]),
                ],
            ])
            ->add('services', EntityType::class, [
                'class' => Service::class,
                'choice_label' => 'name',
                'expanded' => true,
                'multiple' => true,
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
