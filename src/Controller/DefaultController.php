<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\EmployeeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// AbstractController es un controlador de Symfony
// que pone a disposición nuestra multitud de características.
class DefaultController extends AbstractController
{

    /**
     * @Route("/default", name="default_index")
     * 
     * La clase ruta debe estar precedida en los comentario por una arroba.
     * El primer parámetro de Route es la URL a la que queremos asociar la acción.
     * El segundo parámetro de Route es el nombre que queremos dar a la ruta.
     */
    public function index(Request $request,EmployeeRepository $employeeRepository): Response
    {
        if($request->query->has('term')) {
            $people = $employeeRepository->findByTerm($request->query->get('term'));

            return $this->render('default/index.html.twig', [
                'people' => $people
             ]);
     

        }

        // Una acción siempre debe devolver una respesta.
        // Por defecto deberá ser un objeto de la clase,
        // Symfony\Component\HttpFoundation\Response

        // render() es un método hereado de AbstractController
        // que devuelve el contenido declarado en una plantillas de Twig.
        // https://twig.symfony.com/doc/3.x/templates.html
        
        // symfony console
        // es un comando equivalente a 
        // php bin/console

        // Mostrar las rutas disonibles en mi navegador:
        // - symfony console debug:router
        // - symfony console debug:router default_index
        // - symfony console router --help
        // - symfony console router:match /
        
        // Acceso y propiedades del objeto Request.
        // https://symfony.com/doc/current/controller.html#the-request-and-response-object
        // echo '<pre>query: '; var_dump($request->query); echo '</pre>'; // Equivalente a $_GET, pero supervitaminado.
        // echo '<pre>post: '; var_dump($request->request); echo '</pre>'; // Equivalente a $_POST, pero supervitaminado.
        // echo '<pre>server: '; var_dump($request->server); echo '</pre>'; // Equivalente a $_SERVER, pero supervitaminado.
        // echo '<pre>files: '; var_dump($request->files); echo '</pre>'; // Equivalente a $_FILES, pero supervitaminado.
        // echo '<pre>idioma prefererido: '; var_dump($request->getPreferredLanguage()); echo '</pre>';
        
        //Metodo 1: accedienco al repositorio a traves de AbstractController    
        // $orm = $this->getDoctrine();
        // $repo = $orm ->getRepository(Employee::class); //Employee::class = ->App\Entity\Employee
        // $people = $repo->findAll();

        $order = [];

        if($request->query->has('orderBy')) {
            $order[$request->query->get('orderBy')] = $request->query->get('orderDir', 'ASC');
        }        
        
        // Metodo 2: creando un parametro indicando el tipo (type hint)
        $people = $employeeRepository->findBy([], $order);        
        
        return $this->render('default/index.html.twig', [
           'people' => $people
        ]);
    }

    /**
     * @Route("/hola", name="default_hola")
     */
    public function hola(): Response {
        return new Response('<html><body>hola</body></html>');
    }

    /**
     * @Route(
     *      "/default.{_format}",
     *      name="default_index_json",
     *      requirements = {
     *          "_format": "json"
     *      }
     * )
     * 
     * El comando:
     * symfony console router:match /default.json
     * buscará la acción coincidente con la ruta indicada
     * y mostrará la información asociada.
     */
    public function indexJsonRequest(Request $request, EmployeeRepository $employeeRepository): JsonResponse {
        // var_dump($request->query->has('id')); die();
        // $data = $request->query->has('id') ? [][$request->query->get('id')] : []; 
        $data = $request->query->has('id') ?
        $employeeRepository->find($request->query->get('id')) :
        $employeeRepository->findAll();

            return $this->json($data);
    }

    // public function userJson(int $id, EmployeeRepository $employeeRepository): JsonResponse {
    //     $data = $employeeRepository->find($id);
    //     return $this->json($data);


    /**
     * @Route(
     *      "/default/{id}",
     *      name="default_show",
     *      requirements = {
     *          "id": "\d+"
     *      }
     * )
     */

    // /**
    //  * @Route(
    //  *      "/default/{id}",
    //  *      name="default_show",
    //  *      requirements = {
    //  *          "id": "\d+"
    //  *      }
    //  * )
    //  */
    // // La técnica ParamConverte inyecta directamente,
    // // un objeto del tipo indicado como parámetro
    // // intentando hacer un match del parámetro de la ruta
    // // con alguna de las propiedades del objeto requerido.
    // public function show(Employee $employee): Response {
    //     return $this->render('default/show.html.twig', [
    //         'person' => $employee
    //     ]);
    // }



    public function show(int $id, EmployeeRepository $employeeRepository): Response {
        $data = $employeeRepository->find($id);

        return $this->render('default/show.html.twig', [
            'id' => $id,
            'person' => $data
        ]);
    }

    /**
     * @Route("/redirect-to-home", name="default_redirect_to_home")
     */
    public function redirectToHome(): RedirectResponse {
        // Redirigir a la URL /
        // return $this->redirect('/');

        // Redirigir a una ruta utilizando su nombre.
        // return $this->redirectToRoute('default_show', ['id' => 1]);

        // Devolver directamente un objeto RedirectResponse.
        return new RedirectResponse('/', Response::HTTP_TEMPORARY_REDIRECT);
    }

    // EJERCICIO
    // Crear la el recurso para obtener una representación
    // de "UN" empleado en formato JSON.
}
