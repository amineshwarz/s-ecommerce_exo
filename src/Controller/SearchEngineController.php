<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class SearchEngineController extends AbstractController
{
    #[Route('/search/engine', name: 'app_search_engine', methods:['POST','GET'])]
    public function index(Request $request, ProductRepository $productRepository): Response
    {
 
        if ($request->isMethod('GET')){
            $word=$request -> get('word');
            $results = $productRepository->searchEngine($word);
           
        }
        return $this->render('search_engine/index.html.twig', [
            'products' => $results,
            'word' => $word,
        ]);
    }
}
