<?php

namespace Tests\AppBundle\Form;

use AppBundle\Form\TaskType;
use AppBundle\Entity\Task;
use Symfony\Component\Form\Test\TypeTestCase;

class TaskTypeTest extends TypeTestCase
{

    public function testSubmitValidData()
    {
        $formData = array(
            'title' => 'Title',
            'content' => 'Content',
        );

        $task = new Task();
        $task->setTitle($formData['title']);
        $task->setContent($formData['content']);

        $form = $this->factory->create(TaskType::class);
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($task->getTitle(), $form->get('title')->getData());
        $this->assertEquals($task->getContent(), $form->get('content')->getData());
    }

}