<?php

namespace App\Controller;


use App\Entity\BlogPost;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @Route("/blog")
 */
class BlogController extends AbstractController
{

    private const POSTS = [
        [
            "id" => 1,
            "slug" => "hello-world",
            "title" => "Hello world!"
        ],
        [
            "id" => 2,
            "slug" => "another-post",
            "title" => "This is another post"
        ],
        [
            "id" => 3,
            "slug" => "last-example",
            "title" => "This is the last example"
        ],
    ];

    /**
     * @Route("/{page}", name="blog_list", defaults={"page":5}, requirements={"page"="\d+"})
     */
    public function list($page = 1, Request $request)
    {
        $limit = $request->get('limit', 10);
        $repository = $this->getDoctrine()->getRepository(BlogPost::class);
        $items = $repository->findAll();
        // return new JsonResponse(self::POSTS);
        // return new JsonResponse([
        //     'page'=>$page,
        //     'data'=>self::POSTS
        // ]);
        return $this->json(
            [
                'page' => $page,
                'limit' => $limit,
                // 'data' => array_map(function ($item) {
                //     return $this->generateUrl('blog_by_slug', ['slug' => $item['slug']]);
                // }, self::POSTS)
                'data' => array_map(function (BlogPost $item) {
                    return $this->generateUrl('blog_by_slug', ['slug' => $item->getSlug()]);
                }, $items)
            ]
        );
    }


    /**
     * @Route("/post/{id}", name="blog_by_id", requirements={"id"="\d+"}, methods={"GET"})
     * @ParamConverter("post", class="App:BlogPost")
     */
    // public function post($id)
    // public function post(BlogPost $post)
    public function post($post)
    {
        return $this->json(
            // self::POSTS[array_search($id, array_column(self::POSTS, 'id'))]
            // $this->getDoctrine()->getRepository(BlogPost::class)->find($id)
            $post
        );
    }


    /**
     * @Route("/post/{slug}", name="blog_by_slug", methods={"GET"})
     * @ParamConverter("post", class="App:BlogPost", options={"mapping":{"slug" : "slug"}})
     */
    // public function postBySlug($slug)
    // public function postBySlug($post)
    public function postBySlug(BlogPost $post)
    {
        return $this->json(
            // self::POSTS[array_search($slug, array_column(self::POSTS, 'slug'))]
            // $this->getDoctrine()->getRepository(BlogPost::class)->findOneBy(['slug'=>$slug])
            $post
        );
    }


    /**
     * @Route("/add", name="blog_add", methods={"POST"})
     */
    public function add(Request $request)
    {
        /** @var Serializer $serializer */
        $serializer = $this->get('serializer');
        $blogPost = $serializer->deserialize($request->getContent(), BlogPost::class, 'json');

        // connection
        $em = $this->getDoctrine()->getManager();
        $em->persist($blogPost);
        $em->flush();

        return $this->json($blogPost);
    }

    /**
     * @Route("/delete/{id}", name="blog_delete", methods={"DELETE"})
     */
    public function delete(BlogPost $post)
    {
        // connection
        $em = $this->getDoctrine()->getManager();
        $em->remove($post);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
