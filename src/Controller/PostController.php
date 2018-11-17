<?php


namespace App\Controller;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/api/posts")
 */
class PostController extends AbstractController
{

    /**
     * @Route("/all", methods={"GET"}, name="posts_all")
     * @param Request        $request
     * @param PostRepository $postRepository
     * @return JsonResponse
     */
    public function allPosts(Request $request, PostRepository $postRepository): JsonResponse
    {
        $normalizer = new ObjectNormalizer();
        //$normalizer->setCircularReferenceLimit(3);
        $callback = function ($dateTime) {
            return $dateTime instanceof \DateTime
                ? $dateTime->format('Y-m-d H:i:s')
                : '';
        };

        $normalizer->setCallbacks(array('publishedAt' => $callback));
        //$normalizer->setIgnoredAttributes(array('age'));
        $encoder = new JsonEncoder();

        $serializer = new Serializer(array($normalizer), array($encoder));
        $mappings = array('attributes' =>
            array(
                'id',
                'title',
                'slug',
                'content',
                'author'=> ['id', 'fullName', 'username'],
                'tags'=> ['id', 'name'],
                'comments'=> ['id', 'content', 'publishedAt',
                              'author'=> ['id', 'fullName']
                             ]
            ));
        $data = $serializer->normalize($postRepository->getAllPosts(), 'json', $mappings);

        return $this->json($data);
    }

//    /**
//     * @Route("/{id}", methods={"GET"}, name="one_user")
//     * @param Request        $request
//     * @param UserRepository $userRepository
//     * @return JsonResponse
//     */
//    public function user(Request $request, $id, UserRepository $userRepository): JsonResponse
//    {
//
//        return $this->json($userRepository->getUser($id));
//    }
}
