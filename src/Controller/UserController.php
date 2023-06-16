<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Customer;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
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
     * @Route("/api/customers/{id}", name="detailCustomer", methods={"GET"})
     */
    public function getDetailCustomer(Customer $customer, SerializerInterface $serializer): JsonResponse
    {
        $user = $this->getUser();
        if ($customer->getUser() !== $user){
            return new JsonResponse(null, Response::HTTP_FORBIDDEN);
        }
        $jsonCustomer = $serializer->serialize($customer, 'json', ['groups' => 'getCustomers']);
        return new JsonResponse($jsonCustomer, Response::HTTP_OK, ['accept' => 'json'], true);
    }

    /**
     * @Route("/api/customers", name="createCustomer", methods={"POST"})
     */
    public function createProduct(Request $request, SerializerInterface $serializer, EntityManagerInterface $em): JsonResponse
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
     * @Route("/api/Customers/{id}", name="updateCustomer", methods={"PUT"})
     */
    public function updateProduct(Request $request, SerializerInterface $serializer, Customer $currentCustomer, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();
        if ($currentCustomer->getUser() !== $user){
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
     * @Route("/api/customers/{id}", name="deleteCustomer", methods={"DELETE"})
     */
    public function deleteProduct(Customer $customer, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();
        if ($customer->getUser() !== $user){
            return new JsonResponse(null, Response::HTTP_FORBIDDEN);
        }
        $em->remove($customer);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
