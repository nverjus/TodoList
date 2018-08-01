<?php
namespace Tests\AppBundle\Form;

use AppBundle\Form\UserType;
use AppBundle\Entity\User;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Test\FormIntegrationTestCase;
use Symfony\Component\Validator\Validation;

class UserTypeTest extends FormIntegrationTestCase
{
    public function testSubmitValidData()
    {
        $formData = array(
          'username' => 'user',
          'password' => array('first' => 'pass', 'second' => 'pass'),
          'email' => 'user@mail.com',
          'role' => 'ROLE_USER',
        );

        $objectToCompare = new User();
        $form = $this->factory->create(UserType::class, $objectToCompare);

        $object = new User();
        $object->setUsername($formData['username']);
        $object->setEmail($formData['email']);
        $object->setPassword($formData['password']['first']);
        $object->setRole($formData['role']);

        $form->submit($formData);

        static::assertTrue($form->isSynchronized());

        static::assertEquals($object, $objectToCompare);

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            static::assertArrayHasKey($key, $children);
        }
    }

    protected function getExtensions()
    {
        return array(new ValidatorExtension(Validation::createValidator()));
    }
}
