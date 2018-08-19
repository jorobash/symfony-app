<?php


namespace App\Controller;

use App\Service\Greeting;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Tests\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;



/**
 * Matches /blog exactly
 *
 * @Route("/serve")
 */

class BlogController extends AbstractController
{



    /*
     * @var twig
     */
    private $twig;

    /*
     * @var session
     */
    private $session;

    /*
     * @var router
     */
    private $router;
    public function __construct(\Twig_Environment $twig, SessionInterface $session,
                                RouterInterface $router)
    {
        $this->twig = $twig;
        $this->session = $session;
        $this->router = $router;
    }

    /**
     * Matches /blog exactly
     *
     * @Route("/blog", name="blog_list")
     */

    public function index()
    {
        $html = $this->twig->render(
            'blog/index.html.twig',
            [
                'posts' => $this->session->get('posts')
            ]
        );
        return new Response($html);
    }

    /**
     * @Route("/add", name="blog_add")
     */
    public function add()
    {
     $posts = $this->session->get('posts');
     $posts[uniqid()] = [
        'title' => 'A random title' .rand(1, 400),
         'text' => 'Some random text nr' .rand(1, 500),
         'date' => new \DateTime(),
     ];

     $this->session->set('posts', $posts);

     return new RedirectResponse($this->router->generate('blog_list'));
    }


    /**
     * @Route("/show/{id}", name="blog_show")
     */
    public function show($id)
    {
        $posts = $this->session->get('posts');
        if(!$posts || !isset($posts[$id])){
            throw new NotFoundHttpException("Posgt not found");
        }

        $html = $this->twig->render(
            "blog/post.html.twig",
            [
                'id' => $id,
                'post' => $posts[$id]
            ]
        );

        return new Response($html);
    }
}