<?php

declare(strict_types=1);

namespace Tests\Feature\Auth\DTOs;

use App\Http\Requests\Auth\RegisterRequest;
use ArgumentCountError;
use Domain\Auth\DTOs\RegisterUserDTO;
use Tests\TestCase;

class RegisterUserDTOTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->userData = [
            'name' => 'test',
            'email' => 'test@email.ru',
            'password' => 'testPassword',
        ];
    }

    public function test_instance_created_from_form_request(): void
    {
        $dto = RegisterUserDTO::fromRequest(
            new RegisterRequest($this->userData)
        );

        $this->assertInstanceOf(RegisterUserDTO::class, $dto);
    }

    public function test_instance_properties(): void
    {
        $dto = RegisterUserDTO::fromRequest(
            new RegisterRequest($this->userData)
        );

        $this->assertEquals($dto->name, $this->userData['name']);
        $this->assertEquals($dto->email, $this->userData['email']);
        $this->assertEquals($dto->password, $this->userData['password']);
    }

    public function test_instance_property_name(): void
    {
        unset($this->userData['name']);

        $this->expectException(ArgumentCountError::class);

        RegisterUserDTO::fromRequest(
            new RegisterRequest($this->userData)
        );
    }

    public function test_instance_property_email(): void
    {
        unset($this->userData['email']);

        $this->expectException(ArgumentCountError::class);

        RegisterUserDTO::fromRequest(
            new RegisterRequest($this->userData)
        );
    }

    public function test_instance_property_password(): void
    {
        unset($this->userData['password']);

        $this->expectException(ArgumentCountError::class);

        RegisterUserDTO::fromRequest(
            new RegisterRequest($this->userData)
        );
    }

    public function test_instance_created_from_make(): void
    {
        $dto = RegisterUserDTO::make(...$this->userData);

        $this->assertInstanceOf(RegisterUserDTO::class, $dto);
    }

    public function test_instance_properties_from_make(): void
    {
        $dto = RegisterUserDTO::make(...$this->userData);

        $this->assertEquals($dto->name, $this->userData['name']);
        $this->assertEquals($dto->email, $this->userData['email']);
        $this->assertEquals($dto->password, $this->userData['password']);
    }

    public function test_instance_property_name_from_make(): void
    {
        unset($this->userData['name']);
        $this->expectException(ArgumentCountError::class);

        RegisterUserDTO::make(...$this->userData);
    }

    public function test_instance_property_email_from_make(): void
    {
        unset($this->userData['email']);
        $this->expectException(ArgumentCountError::class);

        RegisterUserDTO::make(...$this->userData);
    }

    public function test_instance_property_password_from_make(): void
    {
        unset($this->userData['password']);
        $this->expectException(ArgumentCountError::class);

        RegisterUserDTO::make(...$this->userData);
    }
}
