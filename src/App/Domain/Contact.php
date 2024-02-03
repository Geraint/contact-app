<?php

declare(strict_types=1);

namespace App\Domain;

use App\Repository\ContactRepository;
use Doctrine\ORM\Mapping as ORM;
use UnexpectedValueException;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ContactRepository::class)]
#[ORM\Table(name: 'contacts')]
#[ORM\HasLifecycleCallbacks]
class Contact
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    private string $first = '';

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    private string $last = '';

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    private string $phone = '';

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    #[Assert\Email]
    private string $email = '';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirst(): string
    {
        return $this->first;
    }

    public function setFirst(string $first): void
    {
        $this->first = $first;
    }

    public function getLast(): string
    {
        return $this->last;
    }

    public function setLast(string $last): void
    {
        $this->last = $last;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @throws UnexpectedValueException
     */
    #[ORM\PrePersist, ORM\PreUpdate]
    public function validate(): void
    {
        if ($this->first === 'Geraint') {
            throw new UnexpectedValueException('There can be only one Geraint');
        }
    }
}
