<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ApiResource(
 * itemOperations={
 *    "get"={
 *       "access_control"="is_granted('IS_AUTHENTICATED_FULLY')",
 *       "normalization_context"={"groups"={"get"}}
 *      },
 *    "put" ={
 *       "access_control"="is_granted('IS_AUTHENTICATED_FULLY') and object == user",
 *       "denormalization_context"={"groups"={"put"}},
 *       "normalization_context"={"groups"={"get"}}
 *      }
 *},
 * collectionOperations={
 *    "post"={
 *      "denormalization_context"={"groups"={"post"}},
 *      "normalization_context"={"groups"={"get"}}
 *     }
 * }) 
 * @UniqueEntity("username")
 * @UniqueEntity("email")
 * 
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"get"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"get", "post", "get-comment-with-author", "get-blog-post-with-author"})
     * @Assert\NotBlank()
     * @Assert\Length(min=6, max=255)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"put", "post"})
     * @Assert\NotBlank()
     * @Assert\Regex(
     *  pattern="/(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[@#$%^&+=_]).{7,}/",
     *  message="Password must be seven characters long and contain at least one digit, one uppercase letter, one lowercase letter and at least one of these special characters @#$%^&+=_")
     */
    private $password;

    /**
     * @Groups({"put", "post"})
     * @Assert\NotBlank()
     * @Assert\Expression(
     * "this.getPassword()===this.getRetypedPassword()",
     * message="Passwords does not match"
     * )
     */
    private $retypedPassword;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"get", "post", "put", "get-comment-with-author", "get-blog-post-with-author"})
     * @Assert\NotBlank()
     * @Assert\Length(min=2, max=255)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"get", "post", "put", "get-comment-with-author", "get-blog-post-with-author"})
     * @Assert\NotBlank()
     * @Assert\Length(min=2, max=255)
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"post", "put"})
     * @Assert\NotBlank()
     * @Assert\Email()
     * @Assert\Length(min=6, max=255)
     */
    private $email;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\BlogPost", mappedBy="author")
     * @Groups({"get"})
     */
    private $posts;
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="author")
     * @Groups({"get"})
     */
    private $comments;

    public function __construct()
    {
        $this->posts  = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }
    /**
     * @return Collection
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    /**
     * Returns the roles granted to the user.
     * <code>
     * public function getRoles()
     * {
     * return array('ROLE_USER');
     * }
     * </code>
     * Alternatively, the roles might be stored on a ``roles`` property, 
     * and populated in any number of different ways when the user object is created
     * @return (Rolestring)[] The user roles
     */
    public function getRoles()
    {
        return ['ROLE_USER'];
    }

    /**
     * Returns the salt that was originally used to encode the password.
     * This can return null if the password was not encoded using a salt.
     * @return string|null The salt
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * Removes sensitive data from the user.
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object
     */
    public function eraseCredentials()
    {
        //implement ereaseCredentials() method
    }
    public function getUserIdentifier()
    {
        //implement getUserIdentifier() method
    }
    public function getRetypedPassword()
    {
        return $this->retypedPassword;
    }
    public function setRetypedPassword($retypedPassword): void
    {
        $this->retypedPassword = $retypedPassword;
    }
}
