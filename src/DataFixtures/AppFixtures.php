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

        $admin = $this->getReference('user_admin');
        $user1 = $this->getReference('user_1');

        for ($i = 0; $i < 100; $i++) {
            $blogPost = new BlogPost();
            $blogPost->setTitle($this->faker->realText(30));
            $blogPost->setPublished($this->faker->dateTime);
            $blogPost->setUpdated($this->faker->dateTimeThisYear);
            $blogPost->setAuthor($admin);
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
                $comment->setAuthor($this->getReference('user_admin'));
                $comment->setBlogPost($this->getReference("blog_post_$i"));
                $manager->persist($comment);
            }
        }
        $manager->flush();
    }
    public function loadUsers(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('admin');
        $user->setEmail('admin@admin.com');
        $user->setFirstname('Nora');
        $user->setLastName('Sumane');
        $user->setPassword($this->passwordHasher->hashPassword($user, 'admin123!'));

        $this->addReference('user_admin', $user);

        $manager->persist($user);

        $user = new User();
        $user->setUsername('user_1');
        $user->setEmail('user1@user.com');
        $user->setFirstname('Alfred');
        $user->setLastName('Dupont');
        $user->setPassword($this->passwordHasher->hashPassword($user, 'user123!'));

        $this->addReference('user_1', $user);

        $manager->persist($user);

        $manager->flush();
    }
}
