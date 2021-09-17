<?php

namespace App\DataFixtures;

use App\Entity\BlogPost;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    /**
     * Load data fixtures with the passed EntityManager
     * @param ObjectManager $manager
     */

    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);

        $blogPost = new BlogPost();
        $blogPost->setTitle('A first post!');
        $blogPost->setPublished(new \DateTime('2018-07-01 12:00:00'));
        $blogPost->setUpdated(new \DateTime('2018-07-01 12:00:00'));
        $blogPost->setAuthor('Nora Sumane');
        $blogPost->setContent('Post text');
        $blogPost->setSlug('first-post');

        $manager->persist($blogPost);

        $blogPost = new BlogPost();
        $blogPost->setTitle('A second post!');
        $blogPost->setPublished(new \DateTime('2018-07-01 12:00:00'));
        $blogPost->setUpdated(new \DateTime('2019-07-01 12:00:00'));
        $blogPost->setAuthor('Nora Sumane');
        $blogPost->setContent('Post text 2');
        $blogPost->setSlug('second post');

        $manager->persist($blogPost);

        $blogPost = new BlogPost();
        $blogPost->setTitle('A third post!');
        $blogPost->setPublished(new \DateTime('2012-07-01 12:00:00'));
        $blogPost->setUpdated(new \DateTime('2020-07-01 12:00:00'));
        $blogPost->setAuthor('Nora Sumane');
        $blogPost->setContent('Post text 3');
        $blogPost->setSlug('third-post');

        $manager->persist($blogPost);

        $manager->flush();
    }
}
