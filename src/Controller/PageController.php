<?php

namespace App\Controller;

use App\Entity\Contacto;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{


    private $contactos = [
        1 => ["nombre" => "Juan Pérez", "telefono" => "524142432", "email" => "juanp@ieselcaminas.org"],
        2 => ["nombre" => "Ana López", "telefono" => "58958448", "email" => "anita@ieselcaminas.org"],
        5 => ["nombre" => "Mario Montero", "telefono" => "5326824", "email" => "mario.mont@ieselcaminas.org"],
        7 => ["nombre" => "Laura Martínez", "telefono" => "42898966", "email" => "lm2000@ieselcaminas.org"],
        9 => ["nombre" => "Nora Jover", "telefono" => "54565859", "email" => "norajover@ieselcaminas.org"]
    ];

    #[Route('/contacto/insertar', name: 'insertar_contacto')]
    public function insertar(ManagerRegistry $doctrine)
    {
        $entityManager = $doctrine->getManager();

        foreach ($this->contactos as $c){
            $contacto = new Contacto();
            $contacto->setNombre($c["nombre"]);
            $contacto->setTelefono($c["telefono"]);
            $contacto->setEmail($c["email"]);
            $entityManager->persist($contacto);
        }

        try {
            $entityManager->flush();
            return new Response("Contactos insertados");
        }catch (\Exception $e){
            return new Response("Error insertando objetos");
        }
    }

    #[Route('/page', name: 'app_page')]
    public function index(): Response
    {
        return $this->render('page/index.html.twig', [
            'controller_name' => 'PageController',
        ]);
    }

    #[Route('/', name: 'inicio')]
    public function inicio(): Response
    {
        return $this->render('inicio.html.twig');
    }

    /*base de datos*/

    #[Route('/contacto/{codigo}', name: 'ficha_contacto')] /* { } <-- significa opcional*/
    public function ficha(int $codigo): Response
    {
        $contacto = ($this->contactos[$codigo] ?? null);

        if($contacto){
            return $this->render('ficha_contacto.html.twig',["contacto" => $contacto]);
        }

        return new Response("<html><body>Contacto $codigo no encontrado</body>");
    }

    /*buscar*/

    #[Route('/contacto/buscar/{texto}', name: 'buscar_contacto')]
    public function buscar(ManagerRegistry $doctrine,$texto): Response
    {
        //Filtrar aquello que tengan el texto en el nombre
        $resultados = array_filter($this->contactos, function ($contacto) use ($texto) {
            return strpos($contacto["nombre"], $texto) !== FALSE;
        }
        );

        return $this->render('lista_contactos.html.twig',['contactos'=>$resultados]);
    }
}
