<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Form\CategoryType;
use AppBundle\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends Controller
{
    /**
     * @Route("/categories", name="categorias_listar")
     */
    public function categoryListarAction(CategoryRepository $categoryRepository)
    {
        $todoscategorias = $categoryRepository->findAll();

        return $this->render('category/listar.html.twig', [
            'categorias' => $todoscategorias
        ]);
    }
    /**
     * @Route("/categories/nueva", name="categoria_nueva")
     */
    public function formNuevaCategoria(Request $request)
    {
        $categoria = New Category();

        $this->getDoctrine()->getManager()->persist($categoria);

        return $this->formCategoriaAction($request, $categoria);
    }

    /**
     * @Route("/categories/{id}", name="categoria_editar",
     *     requirements={"id":"\d+"})
     */
    public function formCategoriaAction(Request $request, Category $category)
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $file stores the uploaded PDF file

            /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
            $file = $category->getPhoto();

            $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();

            // Move the file to the directory where brochures are stored
            try {
                $file->move(
                    "uploads/categories_photo",
                    $fileName
                );
            } catch (FileException $e) {

            }

            // updates the 'brochure' property to store the PDF file name
            // instead of its contents
            $category->setPhoto($fileName);

            // ... persist the $product variable or any other work
            $this->getDoctrine()->getManager()->flush();
            return $this->redirect($this->generateUrl('categorias_listar'));
        }


        return $this->render('category/form.html.twig', [
            'form' => $form->createView()
        ]);
    }
    private function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid());
    }
}
