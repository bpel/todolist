<?php

namespace Tests\AppBundle\Form;

use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\Validation;

class UserTypeTest extends TypeTestCase
{
    protected function getExtensions()
    {
        $validator = Validation::createValidator();
        $validator = Validation::createValidatorBuilder()
            ->enableAnnotationMapping()
            ->getValidator();
        return [
            new ValidatorExtension($validator),
        ];
    }

    public function testSubmitValidData()
    {
        $formData = array(
            'username' => 'test',
            'password' => '123456789',
            'email' => 'demo@test.fr',
        );

        $user = new User();
        $user->setUsername($formData['username']);
        $user->setPassword('123456789');
        $user->setEmail($formData['email']);
        $user->setRoles(array());

        $form = $this->factory->create(UserType::class);
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($user->getUsername(), $form->get('username')->getData());
        $this->assertEquals($user->getPassword(), '123456789');
        $this->assertEquals($user->getEmail(), $form->get('email')->getData());
        $this->assertEquals(array(), $form->get('roles')->getData());
    }

}