<?php

namespace App\Controller;

use App\Service\TaxonBagService;
use App\Validator\Validation\TaxonBagValidation;
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

#[Route('/api/taxonomies', 'api_')]
class TaxonBagController extends AbstractController
{
    public function __construct(
        private readonly TaxonBagService $service,
        private readonly DecoderInterface $jsonDecode,
        private readonly TaxonBagValidation $validation,
        private readonly Validator $validator
    ) {
    }

    #[Route('/bags', methods: ['GET'])]
    public function getTaxonBags(Request $request): JsonResponse
    {
        $search = $request->query->all('search');
        $bags = $this->service->getTaxonBags($search);

        return $this->json(
            $bags,
            Response::HTTP_OK,
            [
                'total-items' => $bags->count(),
            ],
            [
                'groups' => ['taxon_bags']
            ]
        );
    }
    #[Route('/bags', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN', message: 'Access Denied.')]
    public function insertTaxonBag(Request $request): JsonResponse
    {
        $requestBody = $this->jsonDecode->decode($request->getContent(), JsonEncoder::FORMAT);

        $errors = $this->validator->validate(
            $requestBody,
            $this->validation->getConstraints()
        );

        if (count($errors) > 0) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        $bag = $this->service->insertTaxonBag($requestBody);

        return $this->json(
            $bag,
            Response::HTTP_CREATED,
            [],
            [
                'groups' => ['taxon_bags']
            ]
        );
    }

    #[Route('/bags/{id}', methods: ['GET'])]
    public function getTaxonBag(Request $request): JsonResponse
    {
        $id = $request->attributes->get('id');
        $bag = $this->service->getTaxonBag($id);

        return $this->json(
            $bag,
            Response::HTTP_OK,
            [],
            [
                'groups' => ['taxon_bags']
            ]
        );
    }

    #[Route('/bags/{id}', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN', message: 'Access Denied.')]
    public function updateTaxonBag(Request $request): JsonResponse
    {
        $id = $request->attributes->get('id');
        $requestBody = $this->jsonDecode->decode($request->getContent(), JsonEncoder::FORMAT);

        if ($requestBody === []) {
            throw new BadRequestException('Payload is empty');
        }

        $this->service->getTaxonBag($id);

        $errors = $this->validator->validate(
            $requestBody,
            $this->validation->getConstraints($id)
        );

        if (count($errors) > 0) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        $bag = $this->service->updateTaxonBag($requestBody, $id);

        return $this->json(
            $bag,
            Response::HTTP_OK,
            [],
            [
                'groups' => ['taxon_bags']
            ]
        );
    }

    #[Route('/bags/{id}', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN', message: 'Access Denied.')]
    public function deleteTaxonBag(Request $request): JsonResponse
    {
        $id = $request->attributes->get('id');
        $this->service->deleteTaxonBag($id);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
