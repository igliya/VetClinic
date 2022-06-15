<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\Pet;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class PetType extends AbstractType
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Кличка должна быть не менее {{ limit }} символов',
                        'max' => 50,
                    ]),
                ],
                'attr' => [
                    'maxlength' => 50,
                    'placeholder' => 'Введите кличку',
                ],
            ])
            ->add('birthday', DateType::class, [
                'constraints' => [
                    new Callback(function ($object, ExecutionContextInterface $context) {
                        $now = new \DateTime((new \DateTime('now'))->format('Y-m-d'));
                        $current = $object;
                        if ($now->format('U') - $current->format('U') < 0) {
                            $context
                                ->buildViolation('Дата рождения не может быть позже текущей даты')
                                ->addViolation();
                        }
                    }),
                ],
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-select',
                ],
            ])
            ->add('kind', null, [
                'attr' => [
                    'class' => 'form-select',
                ],
            ])
            ->add('sex', ChoiceType::class, [
                'choices' => ['М' => true, 'Ж' => false],
                'attr' => [
                    'class' => 'form-select',
                ],
            ])
            ->add('owner', HiddenType::class)
        ;

        $builder
            ->get('owner')->addModelTransformer(new CallbackTransformer(
                function (Client $client) {
                    return $client->getId();
                },
                function (int $clientId) {
                    return $this->entityManager->getRepository(Client::class)->find($clientId);
                }
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Pet::class,
        ]);
    }
}
