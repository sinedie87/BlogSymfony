<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

use App\Entity\Article;
use App\Entity\Comment;
use App\Repository\ArticleRepository;
use App\Form\ArticleType;
use App\Form\CommentType;

class PageController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     */
    public function home()
    {
        return $this->render('page/home.html.twig');
    }

    /**
     * @Route("/articles", name="articles")
     */
    public function articles()
    {
        $repo = $this->getDoctrine()->getRepository(Article::class);
        $articles = $repo->findAll();

        return $this->render('page/articles.html.twig', [
            "articles" => $articles
        ]);
    }
    
    /**
     * @Route("/article/{id}", name="articleUnique")
     */
    public function articleUnique($id)
    {
        $repo = $this->getDoctrine()->getRepository(Article::class);
        $article = $repo->find($id);

        return $this->render('page/articleUnique.html.twig', [
            "article" => $article
        ]);
    }

    /**
     * @Route("/creerArticle", name="createArticle")
     * @Route("/article/{id}/edit", name="editArticle")
     */
    public function createArticle(Article $article = null, Request $request, ObjectManager $manager)
    {

        if(!$article){
            $article = new Article();
        }

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            if(!$article->getId()){
                $article->setCreatedAt(new \DateTime());
            }

            $manager->persist($article);
            $manager->flush();

            return $this->redirectToRoute('articleUnique', ['id' => $article->getId()]);
        }

        return $this->render('page/createArticle.html.twig', [
            'formArticle' => $form->createView(),
            'editMode' => $article->getId()
        ]);
    }

    /**
     * @Route("/article/{id}/delete", name="deleteArticle")
     */
    public function supprimerArticle($id, Request $request, ObjectManager $manager)
    {

        $repo = $this->getDoctrine()->getRepository(Article::class);
        $article = $repo->find($id);

        dump($article);

        $manager->remove($article);
        $manager->flush();

        return $this->redirectToRoute('articles');

    }
    /**
     * @Route("/createComment", name="createComment")
     */
    public function createComment(Request $request, ObjectManager $manager){

        $form = $this->createForm(CommentType::class);

        $form->handleRequest($request);

        return $this->render('page/createComment.html.twig', [
            'formComment' => $form->createView()
        ]);

    }

    /**
     * @Route("/aPropos", name="aPropos")
     */
    public function aPropos()
    {
        return $this->render('page/aPropos.html.twig');
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contact()
    {
        return $this->render('page/contact.html.twig');
    }
}
