<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Atrribute\Route;


#[Route(path: 'api/product')]
class ProductController extends AbstractController
{

    #[Route(name: 'products_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->json($productRepository->findAll());
    }

    #[Route(path: '/{id}', name: 'products_show', methods: ['GET'])]
    public function show(int $id, ProductRepository $productRepository): Response
    {
        $products = $productRepository->find($id);

        if(!$products) {
            return $this->json('No product found for id ' .$id, 404);
        }

        return $this->json($products);
    }

    #[Route(name: 'products_create', methods: ['POST'])]
    public function createProduct(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        $product = new Product();
        $product->setName($data['name']);
        $product->setPrice($data['price']);
        $product->setDescription($data['description']);
        $product->setQuantity($data['quantity']);

        $entityManager->persist($product);

        $entityManager->flush();

        return $this->json($product);
    }

    #[Route(path: '/{id}', name: 'products_update', methods: ['PUT'])]
    public function update(EntityManagerInterface $entityManager, int $id, Request $request): Response
    {
        $product = $entityManager->getRepository(Product::class)->find($id);

        if(!$product) {
            return $this->json("No product found with this id: " .$id);
        }

        $data = json_decode($request->getContent(), true);

        $product->setName($data['name']);
        $product->setPrice($data['price']);
        $product->setDescription($data['description']);
        $product->setQuantity($data['quantity']);

        $entityManager->flush();

        return $this->redirectToRoute('products_show', [
            'id' => $product->getId()
        ]);
    }

    #[Route(path: '/{id}', name: 'products_remove', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $entityManager, int $id): Response
    {
        $product = $entityManager->getRepository(Product::class)->find($id);

        if(!$product) {
            return $this->json("No product found with : " .$id, 404);
        }

        $entityManager->remove($product);

        $entityManager->flush();

        return $this->json([], 404);
    }
}