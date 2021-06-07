<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserCrudController extends AbstractCrudController
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->passwordEncoder = $encoder;
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Сотрудники')
            ->setEntityLabelInSingular('Сотрудник')
            ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::DELETE);
    }

    public function createIndexQueryBuilder(
        SearchDto $searchDto,
        EntityDto $entityDto,
        FieldCollection $fields,
        FilterCollection $filters
    ): QueryBuilder {
        return $this->get(EntityRepository::class)->createQueryBuilder($searchDto, $entityDto, $fields, $filters)
            ->add('where', 'CAST(entity.roles as text) != \'["ROLE_CLIENT"]\'')
            ;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $entityInstance->setPassword(
            $this->passwordEncoder->encodePassword($entityInstance, $entityInstance->getPassword())
        );
        $entityManager->persist($entityInstance);
        $entityManager->flush();
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('login')->setLabel('Логин');
        yield TextField::new('password')->setLabel('Пароль')->onlyWhenCreating();
        yield TextField::new('firstName')->setLabel('Имя');
        yield TextField::new('lastName')->setLabel('Фамилия');
        yield TextField::new('patronymic')->setLabel('Отчество');
        yield TextField::new('phone')->setLabel('Телефон');
        yield ArrayField::new('roles')->setLabel('Роли')->formatValue(
            function ($rolesText, $user) {
                $roles = $user->getRoles();
                $rolesTranslation = [];
                foreach ($roles as $role) {
                    switch ($role) {
                        case 'ROLE_REGISTRAR':
                            $rolesTranslation[] = 'Регистратор';
                            break;
                        case 'ROLE_DOCTOR':
                            $rolesTranslation[] = 'Доктор';
                            break;
                        case 'ROLE_ADMIN':
                            $rolesTranslation[] = 'Администратор';
                            break;
                        case 'ROLE_CLIENT':
                        case 'ROLE_USER':
                            break;
                        default:
                            $rolesTranslation[] = $role;
                            break;
                    }
                }

                return implode(', ', $rolesTranslation);
            }
        );
    }
}
