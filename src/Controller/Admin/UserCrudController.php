<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Symfony\Component\Form\FormEvents;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Symfony\Component\Form\FormBuilderInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserCrudController extends AbstractCrudController
{
    public function __construct(
        public UserPasswordHasherInterface $userPasswordHasher
    ) {}

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureActions(Actions $actions): Actions{
        return $actions
        ->add(Crud::PAGE_EDIT, Action::INDEX)
        ->add(Crud::PAGE_INDEX, Action::DETAIL)
        ->add(Crud::PAGE_EDIT, Action::DETAIL)


        ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            ChoiceField::new('civility')->setChoices([
                'Monsieur' => 'Mr',
                'Madame' => 'Mme',
                'Mademoiselle' => 'Mlle',
            ]),
            TextField::new('full_name'),
            EmailField::new('email'),
            TextField::new('password')
                ->setFormType(RepeatedType::class)
                ->setFormTypeOptions([
                    'type' => PasswordType::class,
                    'first_options' => [
                        'label' => 'Password',
                        'row_attr' => ['class' => "col-md-6 col-xxl-5"]
                    ],
                    'second_options' => [
                        'label' => 'Confirm Password',
                        'row_attr' => ['class' => "col-md-6 col-xxl-5"]
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
        AdminContext $context
    ): FormBuilderInterface {
        $formBuilder = parent::createNewFormBuilder($entityDto, $formOptions, $context);

        // Appel de la méthode pour ajouter l'écouteur d'événements
        return $this->addPasswordEventListener($formBuilder);
    }

    // Méthode qui ajoute un écouteur d'événements au formulaire
    public function addPasswordEventListener(FormBuilderInterface $formBuilder)
    {
        return $formBuilder->addEventListener(
            FormEvents::POST_SUBMIT,
            $this->hashPassword()
        );
    }

    // Fonction pour hacher le mot de passe après la soumission du formulaire
    public function hashPassword()
    {
        return function($event) {
            $form = $event->getForm();

            // Vérifie si le formulaire est valide
            if (!$form->isValid()) {
                return;
            }

            // Récupère le mot de passe soumis
            $password = $form->get('password')->getData();

            // Si aucun mot de passe n'est soumis, on ne fait rien
            if ($password === null) {
                return;
            }

            // Hachage du mot de passe et mise à jour de l'entité User
            $hash = $this->userPasswordHasher->hashPassword($form->getData(), $password);
            $form->getData()->setPassword($hash);
        };
    }
}
