<?php

namespace App\Controller\Admin;

use App\Entity\AnimalKind;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class AnimalKindCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return AnimalKind::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Типы животных')
            ->setEntityLabelInSingular('Тип');
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::DELETE);
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('name')->setLabel('Название');
        yield BooleanField::new('status')->setLabel('Доступность');
    }
}
