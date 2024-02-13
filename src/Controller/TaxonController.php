<?php

namespace App\Controller;

use App\Service\TaxonService;
use App\Validator\Validation\TaxonValidation;
use App\Validator\Validator;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

#[Route('/api/taxonomies', 'api_')]
class TaxonController extends AbstractController
{
    private Request $request;

    private string $taxonomyName;

    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly TaxonService $service,
        private readonly DecoderInterface $jsonDecode,
        private readonly TaxonValidation $validation,
        private readonly Validator $validator
    ) {
        $currentRequest = $this->requestStack->getCurrentRequest();

        if ($currentRequest === null) {
            throw new RuntimeException('Current request not found');
        }

        $this->request = $currentRequest;
        $this->taxonomyName = $this->request->attributes->get('taxonomy_name');
    }

    #[Route('/{taxonomy_name}/taxa', methods: ['GET'])]
    public function getTaxa(): JsonResponse
    {
        $search = $this->request->query->all('search');
        $taxa = $this->service->getTaxa($this->taxonomyName, $search);

        return $this->json(
            $taxa,
            Response::HTTP_OK,
            [
                'total-items' => $taxa->count(),
            ],
            [
                'groups' => ['taxa']
            ]
        );
    }
    #[Route('/{taxonomy_name}/taxa', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN', message: 'Access Denied.')]
    public function insertTaxon(): JsonResponse
    {
        $requestBody = $this->jsonDecode->decode($this->request->getContent(), JsonEncoder::FORMAT);

        $errors = $this->validator->validate(
            $requestBody,
            $this->validation->getConstraints()
        );

        if (count($errors) > 0) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        $requestBody['taxonomy'] = $this->taxonomyName;

        $taxon = $this->service->insertTaxon($requestBody);

        return $this->json(
            $taxon,
            Response::HTTP_CREATED,
            [],
            [
                'groups' => ['taxa']
            ]
        );
    }

    #[Route('/{taxonomy_name}/taxa/{id}', methods: ['GET'])]
    public function getTaxon(): JsonResponse
    {
        $id = $this->request->attributes->get('id');
        $taxon = $this->service->getTaxon($this->taxonomyName, $id);

        return $this->json(
            $taxon,
            Response::HTTP_OK,
            [],
            [
                'groups' => ['taxa']
            ]
        );
    }

    #[Route('/{taxonomy_name}/taxa/{id}', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN', message: 'Access Denied.')]
    public function updateTaxon(): JsonResponse
    {
        $id = $this->request->attributes->get('id');
        $requestBody = $this->jsonDecode->decode($this->request->getContent(), JsonEncoder::FORMAT);

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

        $requestBody['taxonomy'] = $this->taxonomyName;

        $taxon = $this->service->updateTaxon($requestBody, $id);

        return $this->json(
            $taxon,
            Response::HTTP_OK,
            [],
            [
                'groups' => ['taxa']
            ]
        );
    }

    #[Route('/{taxonomy_name}/taxa/{id}', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN', message: 'Access Denied.')]
    public function deleteTaxon(): JsonResponse
    {
        $id = $this->request->attributes->get('id');
        $this->service->deleteTaxon($this->taxonomyName, $id);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
