<?php

namespace App\Controller;

use App\Service\PostService;
use App\Validator\Validation\PostValidation;
use App\Validator\Validator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

#[Route('/api', 'api_')]
class PostController extends AbstractController
{
    public function __construct(
        private readonly PostService $service,
        private readonly DecoderInterface $jsonDecode,
        private readonly PostValidation $validation,
        private readonly Validator $validator
    ) {
    }

    #[Route('/posts', methods: ['GET'])]
    public function getPosts(Request $request): JsonResponse
    {
        $search = $request->query->all('search');
        $posts = $this->service->getPosts($search);

        return $this->json(
            $posts,
            Response::HTTP_OK,
            [
                'total-items' => $posts->count(),
            ],
            [
                'groups' => ['posts']
            ]
        );
    }
    #[Route('/posts', methods: ['POST'])]
    public function insertPost(Request $request): JsonResponse
    {
        $requestBody = $this->jsonDecode->decode($request->getContent(), JsonEncoder::FORMAT);

        if (array_key_exists('title', $requestBody) && !array_key_exists('slug', $requestBody)) {
            $requestBody['slug'] = $requestBody['title'];
        }

        $errors = $this->validator->validate(
            $requestBody,
            $this->validation->getConstraints()
        );

        if (count($errors) > 0) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        $post = $this->service->insertPost($requestBody);

        return $this->json(
            $post,
            Response::HTTP_CREATED,
            [],
            [
                'groups' => ['posts']
            ]
        );
    }

    #[Route('/posts/{id}', methods: ['GET'])]
    public function getPost(Request $request): JsonResponse
    {
        $id = $request->attributes->get('id');
        $post = $this->service->getPost($id);

        return $this->json(
            $post,
            Response::HTTP_OK,
            [],
            [
                'groups' => ['posts']
            ]
        );
    }

    #[Route('/posts/{id}', methods: ['PUT'])]
    public function updatePost(Request $request): JsonResponse
    {
        $id = $request->attributes->get('id');
        $post = $this->service->getPost($id);

        $this->denyAccessUnlessGranted('edit', $post);

        $requestBody = $this->jsonDecode->decode($request->getContent(), JsonEncoder::FORMAT);

        if ($requestBody === []) {
            throw new BadRequestException('Payload is empty');
        }

        $errors = $this->validator->validate(
            $requestBody,
            $this->validation->getConstraints($id)
        );

        if (count($errors) > 0) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        $post = $this->service->updatePost($requestBody, $id);

        return $this->json(
            $post,
            Response::HTTP_OK,
            [],
            [
                'groups' => ['posts']
            ]
        );
    }

    #[Route('/posts/{id}', methods: ['DELETE'])]
    public function deletePost(Request $request): JsonResponse
    {
        $id = $request->attributes->get('id');
        $post = $this->service->getPost($id);

        $this->denyAccessUnlessGranted('delete', $post);

        $this->service->deletePost($id);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
