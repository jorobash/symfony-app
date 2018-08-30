<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 8/30/2018
 * Time: 9:40 PM
 */

namespace App\Controller;
use App\Repository\MicroPostRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route;

/**
 * @Route("/micro-post")
 */

class MicroPostController
{
    /**
     * MicroPostController constructor.
     * @param \Twig_Evironment $twig
     */
    private $twig;

    /**
     * MicroPostController constructor.
     * @param MicroPostRepository $microPostRepository
     */
    private $microPostRepository;

    public function __construct(\Twig_Environment $twig, MicroPostRepository $microPostRepository)
    {
        $this->twig = $twig;
        $this->microPostRepository = $microPostRepository;
    }

    /**
     * @Route("/", name="micro_post_index")
     */
    public function index()
    {
        $html = $this->twig->render('micro-post/index.html.twig', [
           'posts' => $this->microPostRepository->findAll()
        ]);

        return new Response($html);
    }
}