<?php

namespace App\DataFixtures;

// use App\DataFixtures\Faker\Factory;
use Faker\Factory;
use App\Entity\User;
use App\Entity\Comment;
use App\Entity\BlogPost;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{


    private const USERS = [
        [
            'username' => 'admin',
            'email' => 'admin@admin.lv',
            'firstname' => 'Alfred',
            'lastname' => 'Dupont',
            'password' => 'admin123',
            'roles' => [user::ROLE_SUPERADMIN]
        ],
        [
            'username' => 'user',
            'email' => 'user@user.lv',
            'firstname' => 'Roberto',
            'lastname' => 'Meloni',
            'password' => 'user123',
            'roles' => [user::ROLE_COMMENTATOR]
        ],
        [
            'username' => 'user2',
            'email' => 'user2@user.lv',
            'firstname' => 'Steve',
            'lastname' => 'Jobs',
            'password' => 'user2-123',
            'roles' => [user::ROLE_ADMIN]
        ],
        [
            'username' => 'user3',
            'email' => 'user3@user.lv',
            'firstname' => 'Cecile',
            'lastname' => 'Bonnefoy',
            'password' => 'user3-123',
            'roles' => [user::ROLE_WRITER]
        ],
        [
            'username' => 'user4',
            'email' => 'user4@user.lv',
            'firstname' => 'Jeff',
            'lastname' => 'Writer',
            'password' => 'user4-123',
            'roles' => [user::ROLE_WRITER]
        ],
        [
            'username' => 'user5',
            'email' => 'user5@user.lv',
            'firstname' => 'Alice',
            'lastname' => 'Editor',
            'password' => 'user5-123',
            'roles' => [user::ROLE_EDITOR]
        ],
    ];

    /**
     * @var UserPasswordHasherInterface
     */
    private $paswordEncoder;

    /**
     * @var Faker\Factory
     */
    private $faker;

    public function __construct(UserPasswordHasherInterface $passwordEncoder)
    {
        $this->passwordHasher = $passwordEncoder;
        $this->faker = \Faker\Factory::create();
    }
    /**
     * Load data fixtures with the passed EntityManager
     * @param ObjectManager $manager
     */

    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);

        $this->loadUsers($manager);
        $this->loadBlogPosts($manager);
        $this->loadComments($manager);
    }
    public function loadBlogPosts(ObjectManager $manager)
    {

        // $admin = $this->getReference('user_admin');
        // $user1 = $this->getReference('user_1');

        for ($i = 0; $i < 100; $i++) {
            $blogPost = new BlogPost();
            $blogPost->setTitle($this->faker->realText(30));
            $blogPost->setPublished($this->faker->dateTime);
            $blogPost->setUpdated($this->faker->dateTimeThisYear);
            $authorReference = $this->getReference($this->getRandomUserReference($blogPost));
            $blogPost->setAuthor($authorReference);
            $blogPost->setContent($this->faker->realText());
            $blogPost->setSlug($this->faker->slug);

            $this->setReference("blog_post_$i", $blogPost);

            $manager->persist($blogPost);
        }

        $manager->flush();
    }

    public function loadComments(ObjectManager $manager)
    {
        for ($i = 0; $i < 100; $i++) {
            for ($j = 0; $j < rand(1, 10); $j++) {

                $comment = new Comment();
                $comment->setContent($this->faker->realText());
                $comment->setPublished($this->faker->dateTimeThisYear);
                $authorReference = $this->getReference($this->getRandomUserReference($comment));
                $comment->setAuthor($authorReference);
                $comment->setBlogPost($this->getReference("blog_post_$i"));
                $manager->persist($comment);
            }
        }
        $manager->flush();
    }
    public function loadUsers(ObjectManager $manager)
    {
        foreach (self::USERS as $userFixture) {
            $user = new User();
            $user->setUsername($userFixture['username']);
            $user->setEmail($userFixture['email']);
            $user->setFirstname($userFixture['firstname']);
            $user->setLastName($userFixture['lastname']);
            $user->setPassword($this->passwordHasher->hashPassword($user, $userFixture['password']));


            $user->setRoles($userFixture['roles']);
            $this->addReference('user_' . $userFixture['username'], $user);

            $manager->persist($user);
        }

        $manager->flush();
    }


    // returns random user from table const USERS with index 0 to 3
    /**
     * @return string
     */
    protected function getRandomUserReference($entity): string
    {
        $randomUser = self::USERS[rand(0, 5)];

        if ($entity instanceof BlogPost && !count(array_intersect(
            $randomUser['roles'],
            [
                User::ROLE_SUPERADMIN,
                User::ROLE_ADMIN,
                User::ROLE_WRITER
            ]
        ))) {
            return $this->getRandomUserReference($entity);
        }
        if ($entity instanceof Comment && !count(array_intersect(
            $randomUser['roles'],
            [
                User::ROLE_SUPERADMIN,
                User::ROLE_ADMIN,
                User::ROLE_WRITER,
                User::ROLE_COMMENTATOR
            ]
        ))) {
            return $this->getRandomUserReference($entity);
        }

        return 'user_' . $randomUser['username'];
    }
}
