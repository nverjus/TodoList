<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints as Assert;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'label' => "Nom d'utilisateur",
                'constraints' => array(new Assert\NotBlank(array('message' => "Vous devez saisir un nom d'utilisateur."))),
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les deux mots de passe doivent correspondre.',
                'required' => false,
                'first_options'  => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Tapez le mot de passe à nouveau'],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse email',
                'constraints' => array(
                    new Assert\NotBlank(array('message' => "Vous devez saisir une adresse email.")),
                    new Assert\Email(array('message' => 'Le format de l\'adresse n\'est pas correcte.'))
                ),

            ])
            ->add('role', ChoiceType::class, [
                'label' => 'Role de l\'utilisateur',
                'choices'  => [
                  'Utilisateur' => 'ROLE_USER',
                  'Administrateur' => 'ROLE_ADMIN',
                ]])
        ;
    }
}
