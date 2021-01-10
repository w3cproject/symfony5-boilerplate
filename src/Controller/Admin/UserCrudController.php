<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $roles = ArrayField::new('roles')->hideOnForm();

        if (Crud::PAGE_EDIT === $pageName || Crud::PAGE_NEW === $pageName) {
            $roles = ChoiceField::new('roles')
                                ->setChoices(User::$rolesChoices)
                                ->allowMultipleChoices(true);
        }

        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('email'),
            $roles,
            TextField::new('password')->onlyOnForms(),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }
}
