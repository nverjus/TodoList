<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, array(
                'constraints' => array(new NotBlank(array('message' => "Vous devez saisir un titre."))),
            ))
            ->add('content', TextareaType::class, array(
                'constraints' => array(new NotBlank(array('message' => "Vous devez saisir du contenu."))),
            ))
        ;
    }
}
