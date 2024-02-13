<?php

namespace App\Controller;

use App\Service\ContentTaxonService;
use App\Validator\Validation\ContentTaxonValidation;
use App\Validator\Validator;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

#[Route('/api/taxonomies/content', 'api_')]
class ContentTaxonController extends AbstractController
{
    private Request $request;

    private int $postId;

    private string $bagName;

    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly ContentTaxonService $service,
        private readonly DecoderInterface $jsonDecode,
        private readonly ContentTaxonValidation $validation,
        private readonly Validator $validator
    ) {
        $currentRequest = $this->requestStack->getCurrentRequest();

        if ($currentRequest === null) {
            throw new RuntimeException('Current request not found');
        }

        $this->request = $currentRequest;
        $this->postId = $this->request->attributes->get('post_id');
        $this->bagName = $this->request->attributes->get('bag_name');
    }

    #[Route('/{post_id}/taxa/{bag_name}', methods: ['GET'])]
    public function getContentTaxa(): JsonResponse
    {
        $search = $this->request->query->all('search');
        $contentTaxa = $this->service->getContentTaxa($this->postId, $this->bagName, $search);

        return $this->json(
            $contentTaxa,
            Response::HTTP_OK,
            [
                'total-items' => $contentTaxa->count(),
            ],
            [
                'groups' => ['content_taxa']
            ]
        );
    }
    #[Route('/{post_id}/taxa/{bag_name}', methods: ['POST'])]
    public function assignContentTaxon(): JsonResponse
    {
        $requestBody = $this->jsonDecode->decode($this->request->getContent(), JsonEncoder::FORMAT);

        $errors = $this->validator->validate(
            $requestBody,
            $this->validation->getConstraints()
        );

        if (count($errors) > 0) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        $contentTaxon = $this->service->assignContentTaxon($this->postId, $this->bagName, $requestBody['taxon']);

        return $this->json(
            $contentTaxon,
            Response::HTTP_CREATED,
            [],
            [
                'groups' => ['content_taxa']
            ]
        );
    }

    #[Route('/{post_id}/taxa/{bag_name}/{id}', methods: ['GET'])]
    public function getContentTaxon(): JsonResponse
    {
        $id = $this->request->attributes->get('id');
        $contentTaxon = $this->service->getContentTaxon($this->postId, $this->bagName, $id);

        return $this->json(
            $contentTaxon,
            Response::HTTP_OK,
            [],
            [
                'groups' => ['content_taxa']
            ]
        );
    }

    #[Route('/{post_id}/taxa/{bag_name}/{id}', methods: ['DELETE'])]
    public function deleteContentTaxon(): JsonResponse
    {
        $id = $this->request->attributes->get('id');
        $this->service->deleteContentTaxon($this->postId, $this->bagName, $id);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
