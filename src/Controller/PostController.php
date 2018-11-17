<?php


namespace App\Controller;
use App\Entity\Post;
use App\Mappers\PostSerializer;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/posts")
 */
class PostController extends AbstractController
{

    /**
     * Ovde dodajemo format, i kazemo da moze da bude samo xml ili json,
     * da ne bismo mogli da kucamo npr : all.dasdadas vec samo
     * all.json ili all.xml
     *
     *
     * @Route("/all.{format}", methods={"GET"}, name="posts_all", requirements={"format": "xml|json"})
     * @param Request        $request
     * @param PostRepository $postRepository
     * @return Response
     */
    public function allPosts(Request $request, $format, PostRepository $postRepository): Response
    {
        $postsFromDatabase = $postRepository->getAllPosts();
        $data = PostSerializer::serializePosts($postsFromDatabase, $format);
        $response = new Response($data);
        if ($format === 'xml') {
            $response->headers->set('Content-Type', 'xml');
        } else {
            $response->headers->set('Content-Type', 'application/json');
        }
        return $response;
    }

    /**
     * @Route("/{id}.{format}", methods={"GET"}, name="one_post", requirements={"format": "xml|json"})
     * @param Request        $request
     * @param PostRepository $postRepository
     * @return JsonResponse
     */
    public function post(Request $request, $id, $format, PostRepository $postRepository): Response
    {
        $postFromDatabase = $postRepository->getPost($id);
        $data = PostSerializer::serializePosts($postFromDatabase, $format);
        $response = new Response($data);
        if ($format === 'xml') {
            $response->headers->set('Content-Type', 'xml');
        } else {
            $response->headers->set('Content-Type', 'application/json');
        }
        return $response;
    }
}
