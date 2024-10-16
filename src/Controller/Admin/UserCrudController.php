<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;


class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id') ->hideOnForm(),
            ChoiceField::new('civility')->setChoices([
                'Monsieur' =>'Mr',
                'Madame' =>'Mr',
                'Mademoiselle' =>'Mlle',
            ]),
            TextField::new('full_name'),
            EmailField::new('email'),
            TextField::new('password')->hideOnIndex()->hideWhenUpdating(),
            
        ];
    }
    
}
