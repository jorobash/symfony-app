<?php

namespace App\Controller;

use App\Entity\MicroPost;
use App\Entity\User;
use App\Form\MicroPostType;
use App\Repository\MicroPostRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;


/**
 * @Route("/micro-post")
 */

class MicroPostController
{
	/**
	 * @var \Twig_Evironment
	 */

	private $twig;

	/**
	 * @var MicroPostRepository
	 */

	private $microPostRepository;

    /**
     * @var FormFactoryInterface $formFactory
     */
    private $formFactory;

    /**
     * @var EntityManagerInterface $entityManager
     */
    private $entityManager;

    /**
     * @var RouterInterface $router
     */

    private $router;
    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * @param AuthorizationChecker $authorizationChecker
     */
    private $authorizationChecker;

    public function __construct(\Twig_Environment $twig, MicroPostRepository $microPostRepository,
                                FormFactoryInterface $formFactory,
                                EntityManagerInterface $entityManager,
                                RouterInterface $router,
                                FlashBagInterface $flashBag,
                                AuthorizationCheckerInterface $authorizationChecker){
		$this->twig                 = $twig;
		$this->microPostRepository  = $microPostRepository;
		$this->formFactory          = $formFactory;
		$this->entityManager        = $entityManager;
		$this->router               = $router;
        $this->flashBag             = $flashBag;
        $this->authorizationChecker = $authorizationChecker;
    }

	/**
	 * @Route("/", name="micro_post_index")
	 */

	public function index(TokenStorageInterface $tokenStorage, UserRepository $userRepository)
	{
        $currentUser = $tokenStorage->getToken()->getUser();

        $userToFollow = [];

        if($currentUser instanceof User){
            $posts = $this->microPostRepository->findAllByUsers($currentUser->getFollowing());

            $userToFollow = count($posts) === 0 ? $userRepository->findAllWithMoreThan5PostsExeptUser($currentUser) : [];

        }else{
            $posts= $this->microPostRepository->findBy(
                [],
                ['time' => 'DESC']
            );
        }

        $html = $this->twig->render('micro-post/index.html.twig',[
           'posts' => $posts,
            'usersToFollow' => $userToFollow,
        ]);

        return new Response($html);
	}

    /**
     * @param MicroPost $microPost
     * @param Request $request
     * @Route("/edit/{id}", name="micro_post_edit")
     * @Security("is_granted('edit', microPost)", message="Access denied")
     */
	public function edit(MicroPost $microPost, Request $request)
    {
//        if etends base controller we can use this method
//        $this->denyUnlessGranted('edit', $microPost);

//        other way with AuthorizationCheckerInterface
//        if(!$this->authorizationChecker->isGranted('edit', $microPost)){
//            throw new UnauthorizedHttpException('Acess Denied');
//        }

        $form = $this->formFactory->create(MicroPostType::class, $microPost);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->entityManager->persist($microPost);
            $this->entityManager->flush();

            return new RedirectResponse($this->router->generate('micro_post_index'));
        }
        return new Response(
            $this->twig->render('micro-post/add.html.twig',
                ['form'=> $form->createView()]
            )
        );
    }

    /**
     * @Route("/delete/{id}", name="micro_post_delete")
     * @Security("is_granted('delete', microPost)", message="Access denied")
     */
    public  function delete(MicroPost $microPost)
    {

//        other way with AuthorizationCheckerInterface
//        if(!$this->authorizationChecker->isGranted('delete', $microPost)){
//            throw new UnauthorizedHttpException('Acess Denied');
//        }

        $this->entityManager->remove($microPost);
        $this->entityManager->flush();

        $this->flashBag->add('notice', 'Micro post was deleted');

        return new RedirectResponse(
            $this->router->generate('micro_post_index')
        );
    }

	/**
     * @Route("/add", name="micro_post_add")
     * @Security("is_granted('ROLE_USER')")
     */
	public function add(Request $request, TokenStorageInterface $tokenStorage)
    {
        $user = $tokenStorage->getToken()->getUser();
        $microPost = new MicroPost();
        $microPost->setUser($user);
//        $microPost->setTime(new \DateTime());

        $form = $this->formFactory->create(MicroPostType::class, $microPost);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->entityManager->persist($microPost);
            $this->entityManager->flush();

            return new RedirectResponse($this->router->generate('micro_post_index'));
        }
        return new Response(
            $this->twig->render('micro-post/add.html.twig',
                ['form'=> $form->createView()]
            )
        );
    }


    /**
     * @Route("/user/{username}", name="micro_post_user")
     */
    public function userPosts(User $userWithPosts){
        $html = $this->twig->render(
          'micro-post/user-posts.html.twig',
          [
              'posts' => $this->microPostRepository->findBy(
                  ['user' => $userWithPosts],
                  ['time' => 'DESC']
              ),
              'user' => $userWithPosts,
          ]
        );

        return new Response($html);
    }

    /**
     * @Route("/{id}", name="micro_post_post")
     */
    public function post(MicroPost $post)
    {
//        $post = $this->microPostRepository->find($id);

        return new Response(
            $this->twig->render('micro-post/post.html.twig',[
                 'post' => $post
                ]
            )
        );
    }

    /**
     * @return mixed
     */
    public function getAuthorizationChecker()
    {
        return $this->authorizationChecker;
    }


    /**
     * @Route("/loop/", name="loop_array")
     */
    public function loopArrays()
    {
//        $nestedArr = [
//            ["india","Afganistan"],
//            ["mexico", "America"],
//            ["Indonesia", "Malaysia"],
//            ["uk","Spain"]
//        ];
        for ($letter = ord("A"); $letter <= ord("Z"); $letter++)
        {
            echo chr($letter) . ", ";
        }

        return new Response(
            $this->twig->render('loops/loop.html.twig')
        );
    }

}