<?php

namespace App\Controller;

use App\DTO\LowestPriceEnquiry;
use App\Entity\Promotion;
use App\Filter\PromotionsFilterInterface;
use App\Repository\ProductRepository;
use App\Service\Serializer\DTOSerializer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductsController extends AbstractController
{
    public function __construct(
        private ProductRepository $productRepository,
        private EntityManagerInterface $entityManager
    )
    {
    }

    #[Route('/products/{id}/lowest-price', name: 'lowest-price', methods: 'POST')]
    public function lowestPrice(
        Request $request,
        int $id,
        DTOSerializer $serializer,
        PromotionsFilterInterface $promotionsFilter
    ): Response
    {
        if ($request->headers->has('force-fail')) {
            return new JsonResponse(
                ['error' => 'Promotions Engine failure message'],
                $request->headers->get('force-fail')
            );
        }

        /** @var LowestPriceEnquiry $lowestPriceEnquiry */
        $lowestPriceEnquiry = $serializer->deserialize(
            $request->getContent(),
            LowestPriceEnquiry::class,
            'json'
        );

        $product = $this->productRepository->find($id);

        $lowestPriceEnquiry->setProduct($product);

        $promotions = $this->entityManager->getRepository(Promotion::class)->findValidForProduct(
            $product,
            date_create_immutable($lowestPriceEnquiry->getRequestDate())
        ); // Handle null case

        $modifiedEnquiry = $promotionsFilter->apply($lowestPriceEnquiry, ...$promotions);

        $responseContent = $serializer->serialize($modifiedEnquiry, 'json');

        return new Response($responseContent, 200, ['Content-Type' => 'application/json']);
    }

    #[Route('/products/{id}/promotions', name: 'promotions', methods: 'GET')]
    public function promotions()
    {

    }

    /*
    private MessageBusInterface $commandBus;
    private ValidatorInterface $validator;

    public function __construct(MessageBusInterface $commandBus, ValidatorInterface $validator)
    {
        $this->commandBus = $commandBus;
        $this->validator = $validator;
    }


    #[Route('/v1/items/{itemId}/', name: 'store_item', methods: ['DELETE', 'PUT'])]
    public function itemAction(string $itemId, Request $request) : Response {
        if ($request->isMethod('DELETE')) {
            $this->commandBus->dispatch(new RemoveItem($itemId));

            return new Response('', 204);
        }

        try {
            $errors = $this->validator->validate($request);

            if (empty($errors)) {
                if ($request->query->get('dry_run)') !== '1') {
                    $this->commandBus->dispatch(new createItem($itemId));
                }
            }

            return new Response('', 201);
        } catch($e InvalidPutItemRequestException()) {
            return new Response($e->getMessage(), 400);
        } catch($e UnknownContentTypeException()) {
            return new Response($e->getMessage(), 501);
        } catch($e ItemAlreadyExists()) {
            if ($request->query->get('dry_run)') !== '1') {
                $this->commandBus->dispatch(new UpdateItem($itemId));
            }
            return new Response('', 200);
        }
    }
*/
}