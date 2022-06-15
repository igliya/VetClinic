<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('login', null, [
                'constraints' => [
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Логин должен быть не менее {{ limit }} символов',
                        'max' => 255,
                    ])
                ],
                'attr' => [
                    'maxlength' => 255,
                    'placeholder' => 'Введите email',
                ],
            ])
            ->add('firstName', TextType::class, [
                'constraints' => [
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Имя должно быть не менее {{ limit }} символов',
                        'max' => 50,
                    ]),
                ],
                'attr' => [
                    'maxlength' => 50,
                    'placeholder' => 'Введите имя',
                ],
            ])
            ->add('lastName', TextType::class, [
                'constraints' => [
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Фамилия должна быть не менее {{ limit }} символов',
                        'max' => 50,
                    ]),
                ],
                'attr' => [
                    'maxlength' => 50,
                    'placeholder' => 'Введите фамилию',
                ],
            ])
            ->add('patronymic', TextType::class, [
                'constraints' => [
                    new Length([
                        'max' => 50,
                    ]),
                ],
                'attr' => [
                    'maxlength' => 50,
                    'placeholder' => 'Введите отчество',
                ],
                'required' => false,
            ])
            ->add('phone', TelType::class, [
                'constraints' => [
                    new Regex([
                        'pattern' => '/^[+][0-9]{1}[(]{1}[0-9]{3}[)]{1}[0-9]{3}[-]{1}[0-9]{4}$/',
                        'message' => 'Номер телефона должен иметь следующий формат: +#(###)###-####',
                    ]),
                ],
                'attr' => [
                    'maxlength' => 15,
                    'placeholder' => 'Введите номер телефона',
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'mapped' => false,
                'type' => PasswordType::class,
                'constraints' => [
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Пароль должен быть не менее {{ limit }} символов',
                        'max' => 255,
                    ]),
                    new Regex([
                        'pattern' => '/(?=.*[0-9])(?=.*[!@#$%^&*])(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z!@#$%^&*]{6,}/',
                        'message' => 'Пароль должен состоять из латинских заглавных и строчных букв, содержать цифры и символы !@#$%^&*',
                    ]),
                ],
                'first_options' => [
                    'attr' => [
                        'maxlength' => 255,
                        'placeholder' => 'Введите пароль',
                    ],
                ],
                'second_options' => [
                    'attr' => [
                        'maxlength' => 255,
                        'placeholder' => 'Повторите пароль',
                    ],
                ],
                'invalid_message' => 'Пароли не совпадают',
            ])
            ->add('address', TextType::class, [
                'constraints' => [
                    new Length([
                        'min' => 20,
                        'minMessage' => 'Адрес должен быть не менее {{ limit }} символов',
                        'max' => 512,
                    ]),
                ],
                'attr' => [
                    'maxlength' => 512,
                    'placeholder' => 'Введите адрес',
                ],
                'mapped' => false,
            ])
//            ->add('passport', TextType::class, [
//                'constraints' => [
//                    new Regex([
//                        'pattern' => '/^[0-9]{10}$/',
//                        'message' => 'Серия и номер паспорта должны вводиться цифрами без пробелов',
//                    ]),
//                ],
//                'attr' => [
//                    'maxlength' => 512,
//                    'placeholder' => 'Введите данные паспорта',
//                ],
//                'mapped' => false,
//            ])
            ->add('agreeTerms', CheckboxType::class, [
                'required' => true,
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Вы должны согласиться на обработку персональных данных',
                    ]),
                ],
                'label' => 'Согласие на обработку персональных данных',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
