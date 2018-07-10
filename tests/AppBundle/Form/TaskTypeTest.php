<?php
namespace Tests\AppBundle\Form;

use AppBundle\Form\TaskType;
use AppBundle\Entity\Task;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Test\FormIntegrationTestCase;
use Symfony\Component\Validator\Validation;

class TaskTypeTest extends FormIntegrationTestCase
{
    public function testSubmitValidData()
    {
        $formData = array(
          'title' => 'test task',
          'content' => 'This is a test task.',

        );

        $objectToCompare = new Task();
        $form = $this->factory->create(TaskType::class, $objectToCompare);

        $object = new Task();
        $object->setTitle($formData['title']);
        $object->setContent($formData['content']);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($object, $objectToCompare);

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }

    protected function getExtensions()
    {
        return array(new ValidatorExtension(Validation::createValidator()));
    }
}