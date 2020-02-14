<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Entity\Commentaire;
use App\Form\CommentaireType;
use App\Repository\BlogRepository;
use App\Repository\CategorieRepository;
use App\Repository\MarqueRepository;
use App\Repository\ProduitRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class AppController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @param ProduitRepository $produitRepository
     * @param CategorieRepository $categorieRepository
     * @return Response
     */
    public function home(
        ProduitRepository $produitRepository,
        CategorieRepository $categorieRepository,
        MarqueRepository $marqueRepository
    ) {
        $produits = $produitRepository->findBy([], ['id' => 'DESC'], 10);
        $categories = $categorieRepository->findBy([], ['titre' => 'ASC'], 4);
        $marques = $marqueRepository->findBy([], ['name' => 'ASC']);

        return $this->render('app/home.html.twig',
                             ['produits' => $produits, 'categories' => $categories, 'marques' => $marques]);
    }

    /**
     * @Route("/marque", name="marque")
     * @param MarqueRepository $marqueRepository
     * @return Response
     */
    public function marque(MarqueRepository $marqueRepository)
    {
        $marques = $marqueRepository->findAll();

        return $this->render('app/marque.html.twig', ['marques' => $marques]);
    }


    /**
     * @Route("/categorie", name="categorie")
     * @param CategorieRepository $categorieRepository
     * @return Response
     */
    public function categorie(CategorieRepository $categorieRepository)
    {
        $categories = $categorieRepository->findAll();

        return $this->render('app/categorie.html.twig', ['categories' => $categories]);
    }

    /**
     * @Route("/produit/{id}", name="produit")
     * @param ProduitRepository $produitRepository
     * @param $id
     * @param Request $request
     * @return Response
     */
    public function produit(ProduitRepository $produitRepository, $id, Request $request)
    {
        $produit = $produitRepository->find($id);

        $commentaire = new Commentaire();
        $commentaire->setProduit($produit);

        $form = $this->createForm(CommentaireType::class, $commentaire);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($commentaire);
            $em->flush();
        }

        return $this->render('app/produit.html.twig', ['produit' => $produit, 'form' => $form->createView()]);
    }

    /**
     * @Route("/categorie/{id}", name="singleCategorie")
     * @param CategorieRepository $categorieRepository
     * @return Response
     */
    public function singleCategory(CategorieRepository $categorieRepository, $id)
    {
        $categorie = $categorieRepository->find($id);

        return $this->render('app/single-categorie.html.twig', ['categorie' => $categorie]);
    }

    /**
     * @Route("/marque/{id}", name="singleMarque")
     * @param MarqueRepository $marqueRepository
     * @param $id
     * @return Response
     */
    public function singleMarque(MarqueRepository $marqueRepository, $id)
    {
        $marque = $marqueRepository->find($id);

        return $this->render('app/single-marque.html.twig', ['marque' => $marque]);
    }

    /**
     * @Route("/blog", name="blogs")
     * @param BlogRepository $blogRepository
     * @param CategorieRepository $categorieRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function blog(
        BlogRepository $blogRepository,
        CategorieRepository $categorieRepository,
        PaginatorInterface $paginator,
        Request $request
    ) {
        $query = $blogRepository->findByPublished(true);

        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            Blog::$nbParPage /*limit per page*/
        );

        $categories = $categorieRepository->findAll();

        return $this->render('app/blogs.html.twig', ['blogs' => $pagination, 'categories' => $categories]);
    }

    /**
     * @Route("/article/{id}", name="singleBlog")
     * @param $id
     * @param Request $request
     * @param BlogRepository $blogRepository
     * @return Response
     */
    public function singleBlog($id, Request $request, BlogRepository $blogRepository)
    {
        $article = $blogRepository->find($id);

        $commentaire = new Commentaire();
        $commentaire->setBlog($article);

        $form = $this->createForm(CommentaireType::class, $commentaire);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($commentaire);
            $em->flush();
        }

        return $this->render('app/single-blog.html.twig', ['article' => $article, 'form' => $form->createView()]);
    }
}
