<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

/**
 * @Route("/user")
 */
class UserController extends FOSRestController
{
    /**
     * @Rest\Get("/index", name="user_index")
     * 
     * @SWG\Response(
     *      response=200,
     *      description="Usuarios encontrados"
     * )
     * 
     * @SWG\Response(
     *      response=500,
     *      description="Usuarios no encontrados"
     * )
     * 
     */
    public function index(UserRepository $userRepository): Response
    {
        $index = $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);

        return new Response($index,  Response::HTTP_OK, ['content-type' => 'application/json']);
    }

    /**
     * @Rest\Post("/new", name="user_new")
     * 
     * @SWG\Response(
     *      response=200,
     *      description="Registro realizado"
     * )
     * 
     * @SWG\Response(
     *      response=500,
     *      description="El registro no se ha podido completar"
     * )
     * 
     * @SWG\Parameter(
     *      name="_name",
     *      in="body",
     *      type="string",
     *      description="Nombre de usuario"
     * )
     * @SWG\Parameter(
     *      name="_lastname",
     *      in="body",
     *      type="string",
     *      description="Apellidos de usuario"
     * )
     * 
     * @SWG\Parameter(
     *      name="_phone",
     *      in="body",
     *      type="string",
     *      description="Telefono de usuario"
     * )
     * 
     * @SWG\Parameter(
     *      name="_direccion",
     *      in="body",
     *      type="string",
     *      description="Direccion de usuario"
     * )
     * 
     * @SWG\Parameter(
     *      name="_email",
     *      in="body",
     *      type="string",
     *      description="Correo de usuario"
     * )
     * 
     * @SWG\Tag(name="User")
     */
    public function registroUsuario (Request $request){
        $user = new User();
        $em = $this->getDoctrine()->getManager();

        $name = $request->request->get('_name');
        $lastname = $request->request->get('_lastname');
        $phone = $request->request->get('_phone');
        $direction = $request->request->get('_direccion');
        $email = $request->request->get('_email');

        $user->setName($name);
        $user->setLastname($lastname);
        $user->setPhone($phone);
        $user->setDireccion($direction);
        $user->setEmail($email);

        $em->persist($user);
        $em->flush();

        $usuario = array(
            'nombre' => $user->getName(), 
            'apellidos' => $user->getLastname(), 
            'telefono' => $user->getPhone(),
            'direccion' => $user->getDireccion(),
            'correo' => $user->getEmail() 
        );

        return new Response(json_encode($usuario), Response::HTTP_OK, ['content-type' => 'application/json']);
    }


    /**
     * @Rest\Delete("/{id}", name="user_delete")
     * 
     * @SWG\Response(
     *      response=200,
     *      description="Registro realizado"
     * )
     * 
     * @SWG\Response(
     *      response=500,
     *      description="El registro no se ha podido completar"
     * )
     * 
     * @SWG\Tag(name="User")
     */
    public function delete(Request $request, User $user): Response{

        //var_dump($user->getId() !== null);

        if($user->getId() !== null){
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);

            $entityManager->flush();
            $message = "Usuario eliminado exitosamente";
        }else{
            $message = "El usuario no existe";    
        }
        //var_dump($user->getId()); die;

        return new Response(json_encode($message), Response::HTTP_OK, ['content-type' => 'application/json']);
    }
}
