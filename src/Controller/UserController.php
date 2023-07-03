<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Customer;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class UserController extends AbstractController
{
    /**
     * Cette méthode permet de récupérer la liste des Customers.
     *
     * @OA\Response(
     *     response=200,
     *     description="Retourne la liste des Produit",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Customer::class, groups={"getCustomers"}))
     *     )
     * )
     * @OA\Parameter(
     *     name="page",
     *     in="query",
     *     description="La page que l'on veut récupérer",
     *     @OA\Schema(type="integer")
     * )
     * @OA\RequestBody(
     *     @OA\Schema(type="array", @OA\Items(ref=@Model(type=Customer::class, groups={"getCustomers"})))
     * )
     * @OA\Tag(name="Customer")
     * @Route("/api/customers", name="listCustomers", methods={"GET"})
     */
    public function getAllCustomers(CustomerRepository $customerRepository, SerializerInterface $serializer, Request $request): JsonResponse
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 3);

        $user = $this->getUser();

        $paginator = $customerRepository->findAllWithPagination($page, $limit, $user);
        $maxPage = ceil($paginator->count() / $limit);
        $response = [
            "maxItems" => $paginator->count(),
            "href" => [
                "create" => $this->generateUrl("createCustomer"),
                "first" => $this->generateUrl("listCustomers", [
                    "page" => 1,
                    "limit" => $limit,
                ]),
                "last" => $this->generateUrl("listCustomers", [
                    "page" => $maxPage,
                    "limit" => $limit,
                ]),
            ],
            "items" => $paginator,
        ];

        if ($page > 1) {
            $response["href"]["previous"] = $this->generateUrl("listCustomers", [
                "page" => $page - 1,
                "limit" => $limit,
            ]);
        }

        if ($page < $maxPage) {
            $response["href"]["next"] = $this->generateUrl("listCustomers", [
                "page" => $page + 1,
                "limit" => $limit,
            ]);
        }

        $jsonCustomerList = $serializer->serialize($response, 'json', ['groups' => 'getCustomers']);
        return new JsonResponse($jsonCustomerList, Response::HTTP_OK, [], true);
    }

    /**
     * Cette méthode permet de récupérer les details d'un seul Customer.
     *
     * @OA\Response(
     *     response=200,
     *     description="list of Products",
     *     @Model(type=Customer::class, groups={"getCutomers"})
     * )
     *
     * @OA\Response(
     *     response = 403,
     *     description = "Forbidden access to this content"
     * )
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="id of a Product",
     *     @OA\Schema(type="integer", format="int64")
     * )
     * @OA\Tag(name="Customer")
     * @Route("/api/customers/{id}", name="detailCustomer", methods={"GET"})
     */
    public function getDetailCustomer(Customer $customer, SerializerInterface $serializer): JsonResponse
    {
        $user = $this->getUser();
        if ($customer->getUser() !== $user) {
            return new JsonResponse(null, Response::HTTP_FORBIDDEN);
        }
        $jsonCustomer = $serializer->serialize($customer, 'json', ['groups' => 'getCustomers']);
        return new JsonResponse($jsonCustomer, Response::HTTP_OK, ['accept' => 'json'], true);
    }

    /**
     * Cette méthode permet de créer un Customer.
     *
     * @OA\Response(
     *     response=201,
     *     description="User successfully added to the Client",
     *     @Model(type=Customer::class, groups={"getCutomers"})
     * )
     * @OA\Response(
     *     response = 400,
     *     description = "Bad data sent, check fields and try again"
     * )
     * @OA\Response(
     *     response = 401,
     *     description = "You must use a valid token to complete this request"
     * )
     * @OA\Response(
     *     response = 403,
     *     description = "Forbidden access to this content"
     * )
     * @OA\Response(
     *     response = 404,
     *     description = "This resource doesn't exist !"
     * )
     *
     * @OA\RequestBody(@Model(type=Customer::class, groups={"create"}))
     *
     * @OA\Tag(name="Customer")
     * @Route("/api/customers", name="createCustomer", methods={"POST"})
     */
    public function createCustomer(Request $request, SerializerInterface $serializer, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();
        $customer = $serializer->deserialize($request->getContent(), Customer::class, 'json');
        $customer->setUser($user);
        $em->persist($customer);
        $em->flush();

        $jsonProduct = $serializer->serialize($customer, 'json', ['groups' => 'getCustomers']);

        return new JsonResponse($jsonProduct, Response::HTTP_CREATED, [], true);
    }

    /**
     * Cette méthode permet de faire un update sur un Customer.
     * @OA\Response(
     *     response=200,
     *     description="Update a Customer",
     *     @Model(type=Customer::class, groups={"getCutomers"})
     * )
     * @OA\Response(
     *     response = 401,
     *     description = "You must use a valid token to complete this request"
     * )
     * @OA\Response(
     *     response = 404,
     *     description = "This resource doesn't exist !"
     * )
     *
     * @OA\RequestBody(@Model(type=Customer::class, groups={"update"}))
     *
     * @OA\Tag(name="Customer")
     * @Route("/api/customers/{id}", name="updateCustomer", methods={"PUT"})
     */
    public function updateCustomer(Request $request, SerializerInterface $serializer, Customer $currentCustomer, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();
        if ($currentCustomer->getUser() !== $user) {
            return new JsonResponse(null, Response::HTTP_FORBIDDEN);
        }

        $updatedCustomer = $serializer->deserialize($request->getContent(),
            Customer::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $currentCustomer]);

        $em->persist($updatedCustomer);
        $em->flush();
        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    /**
     * Cette méthode permet de supprimer un Customer.
     *
     * @OA\Response(
     *     response=204,
     *     description="Delete a Customer",
     *     @Model(type=Customer::class, groups={"getCutomers"})
     * )
     *
     * @OA\Tag(name="Customer")
     * @Route("/api/customers/{id}", name="deleteCustomer", methods={"DELETE"})
     */
    public function deleteCustomer(Customer $customer, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();
        if ($customer->getUser() !== $user) {
            return new JsonResponse(null, Response::HTTP_FORBIDDEN);
        }
        $em->remove($customer);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
