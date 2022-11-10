<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Form\CustomerType;
use App\Repository\CustomerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/customers')]
class CustomerController extends AbstractController
{
    // #[Route('/', name: 'app_customer_index', methods: ['GET'])]
    // public function index(CustomerRepository $customerRepository): Response
    // {
    //     return $this->render('customer/index.html.twig', [
    //         'customers' => $customerRepository->findAll(),
    //     ]);
    // }

    #[Route('/', name: 'app_customer_index', methods: ['GET'])]
    public function index(CustomerRepository $customerRepository): Response
    {
        return $this->redirectToRoute('app_customer_paginate', ['currentPage' => 1], Response::HTTP_SEE_OTHER);
    }

    #[Route('/page/{currentPage}', name: 'app_customer_paginate', methods: ['GET'])]
    public function paginate(CustomerRepository $customerRepository, $currentPage): Response
    {
      $limit = 10;
      $customers = $customerRepository->getAllCustomers($currentPage, $limit);
      $customersResult = $customers['paginator'];
      $customersQuery =  $customers['query'];

      $maxPages = ceil($customersResult->count() / $limit);

      return $this->render('customer/index.html.twig', array(
            'customers' => $customersResult,
            'maxPages'=> $maxPages,
            'thisPage' => $currentPage,
            'all_items' => $customersQuery
        ) );
    }

    #[Route('/new', name: 'app_customer_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CustomerRepository $customerRepository): Response
    {
        $customer = new Customer();
        $form = $this->createForm(CustomerType::class, $customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $customerRepository->save($customer, true);

            return $this->redirectToRoute('app_customer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('customer/new.html.twig', [
            'customer' => $customer,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_customer_show', methods: ['GET'])]
    public function show(Customer $customer): Response
    {
        return $this->render('customer/show.html.twig', [
            'customer' => $customer,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_customer_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Customer $customer, CustomerRepository $customerRepository): Response
    {
        $form = $this->createForm(CustomerType::class, $customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $customerRepository->save($customer, true);

            return $this->redirectToRoute('app_customer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('customer/edit.html.twig', [
            'customer' => $customer,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_customer_delete', methods: ['POST'])]
    public function delete(Request $request, Customer $customer, CustomerRepository $customerRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$customer->getId(), $request->request->get('_token'))) {
            $customerRepository->remove($customer, true);
        }

        return $this->redirectToRoute('app_customer_index', [], Response::HTTP_SEE_OTHER);
    }
}
