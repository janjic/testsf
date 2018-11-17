<?php


namespace App\Controller;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/users")
 */
class UserController extends AbstractController
{

    /**
     * @Route("/all", methods={"GET"}, name="users_all")
     * @param Request        $request
     * @param UserRepository $userRepository
     * @return JsonResponse
     */
    public function allUser(Request $request, UserRepository $userRepository): JsonResponse
    {

        return $this->json($userRepository->getAllUsers());
    }

    /**
     * @Route("/{id}", methods={"GET"}, name="one_user")
     * @param Request        $request
     * @param UserRepository $userRepository
     * @return JsonResponse
     */
    public function user(Request $request, $id, UserRepository $userRepository): JsonResponse
    {

        return $this->json($userRepository->getUser($id));
    }
}
