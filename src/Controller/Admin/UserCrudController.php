<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;


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
            TextField::new('password')
            ->setFormType(RepeatedType::class)
            ->setFormTypeOptions([
                'type' => PasswordType::class,
                'first_options' => [
                    'label' => 'Password',
                    'row_attr' => [
                        'class'=>"col-md-6 col-xxl-5"
                    ]
                ],
                'second_options' =>[
                    'label' =>'Cofirm Password',
                    'row_attr' => [
                        'class'=>"col-md-6 col-xxl-5"
                    ],
                ],
                'mapped' => false,
            ])
            ->setRequired($pageName === Crud::PAGE_NEW)
            ->onlyOnForms(),
            
        ];
    }
    public function createNewFormBuilder(
        EntityDto $entityDto,
        KeyValueStore $formOptions,
        AdminContext $context): FormBulderInterface
    {
        $formBuilder = parent::createNewFormBuilder($entityDto,$formOptions,$context);

        return $this->addPasswordEvenListener($formBuilder);
    }
    public function addPasswordEventListener(FormBuilderInterface $formBuilder){
        return  $formBuilder->addEventListener(ForEvents::POST_SUBMIT,$this->hashPassword());
    }
    public function hashPassword(){
        
    }
    
}
