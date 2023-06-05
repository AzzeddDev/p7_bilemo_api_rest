<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class ProductController extends AbstractController
{
    /**
     * @Route("/api/products", name="listProducts", methods={"GET"})
     */
    public function getAllProducts(ProductRepository $productRepository, SerializerInterface $serializer): JsonResponse
    {
        $productList = $productRepository->findAll();

        $jsonProductList = $serializer->serialize($productList, 'json');
        return new JsonResponse($jsonProductList, Response::HTTP_OK, [], true);
    }

    /**
     * @Route("/api/products/{id}", name="detailProduct", methods={"GET"})
     */
    public function getDetailProduct(Product $product, SerializerInterface $serializer): JsonResponse
    {
        $jsonProduct = $serializer->serialize($product, 'json');
        return new JsonResponse($jsonProduct, Response::HTTP_OK, ['accept' => 'json'], true);
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//
//    /**
//     * @Route("/api/products/{id}", name="deleteProduct", methods={"DELETE"})
//     */
//    public function deleteProduct(Product $product, EntityManagerInterface $em): JsonResponse
//    {
//        $em->remove($product);
//        $em->flush();
//
//        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
//    }
//
//    /**
//     * @Route("/api/products", name="createProduct", methods={"POST"})
//     */
//    public function createProduct(Request $request, SerializerInterface $serializer, EntityManagerInterface $em): JsonResponse
//    {
//        $product = $serializer->deserialize($request->getContent(), Product::class, 'json');
//        $em->persist($product);
//        $em->flush();
//
//        $jsonProduct = $serializer->serialize($product, 'json', ['groups' => 'getProducts']);
//
//        return new JsonResponse($jsonProduct, Response::HTTP_CREATED, [],true);
//    }
//
//    /**
//     * @Route("/api/products/{id}", name="updateProduct", methods={"PUT"})
//     */
//    public function updateProduct(Request $request, SerializerInterface $serializer, Product $currentProduct, EntityManagerInterface $em): JsonResponse
//    {
//        $updatedProduct = $serializer->deserialize($request->getContent(),
//            Product::class,
//            'json',
//            [AbstractNormalizer::OBJECT_TO_POPULATE => $currentProduct]);
//
//        $em->persist($updatedProduct);
//        $em->flush();
//        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
//    }

}
