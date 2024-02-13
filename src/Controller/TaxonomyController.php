<?php

namespace App\Controller;

use App\Service\TaxonomyService;
use App\Validator\Validation\TaxonomyValidation;
use App\Validator\Validator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

#[Route('/api', 'api_')]
#[IsGranted('ROLE_ADMIN', message: 'Access Denied.')]
class TaxonomyController extends AbstractController
{
    public function __construct(
        private readonly TaxonomyService $service,
        private readonly DecoderInterface $jsonDecode,
        private readonly TaxonomyValidation $validation,
        private readonly Validator $validator
    ) {
    }

    #[Route('/taxonomies', methods: ['GET'])]
    public function getTaxonomies(Request $request): JsonResponse
    {
        $search = $request->query->all('search');
        $taxonomies = $this->service->getTaxonomies($search);

        return $this->json(
            $taxonomies,
            Response::HTTP_OK,
            [
                'total-items' => $taxonomies->count(),
            ],
            [
                'groups' => ['taxonomies']
            ]
        );
    }
    #[Route('/taxonomies', methods: ['POST'])]
    public function insertTaxonomy(Request $request): JsonResponse
    {
        $requestBody = $this->jsonDecode->decode($request->getContent(), JsonEncoder::FORMAT);

        $errors = $this->validator->validate(
            $requestBody,
            $this->validation->getConstraints()
        );

        if (count($errors) > 0) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        $taxonomy = $this->service->insertTaxonomy($requestBody['name']);

        return $this->json(
            $taxonomy,
            Response::HTTP_CREATED,
            [],
            [
                'groups' => ['taxonomies']
            ]
        );
    }

    #[Route('/taxonomies/{id}', methods: ['GET'])]
    public function getTaxonomy(Request $request): JsonResponse
    {
        $id = $request->attributes->get('id');
        $taxonomy = $this->service->getTaxonomy($id);

        return $this->json(
            $taxonomy,
            Response::HTTP_OK,
            [],
            [
                'groups' => ['taxonomies']
            ]
        );
    }

    #[Route('/taxonomies/{id}', methods: ['PUT'])]
    public function updateTaxonomy(Request $request): JsonResponse
    {
        $id = $request->attributes->get('id');
        $requestBody = $this->jsonDecode->decode($request->getContent(), JsonEncoder::FORMAT);

        if ($requestBody === []) {
            throw new BadRequestException('Payload is empty');
        }

        $this->service->getTaxonomy($id);

        $errors = $this->validator->validate(
            $requestBody,
            $this->validation->getConstraints($id)
        );

        if (count($errors) > 0) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        $taxonomy = $this->service->updateTaxonomy($requestBody['name'], $id);

        return $this->json(
            $taxonomy,
            Response::HTTP_OK,
            [],
            [
                'groups' => ['taxonomies']
            ]
        );
    }

    #[Route('/taxonomies/{id}', methods: ['DELETE'])]
    public function deleteTaxonomy(Request $request): JsonResponse
    {
        $id = $request->attributes->get('id');
        $this->service->deleteTaxonomy($id);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
