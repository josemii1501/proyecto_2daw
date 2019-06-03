<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Form\Type\CategoryType;
use AppBundle\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
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
        if(null === $category) {
            $category = new $category();
            $new = true;
        } else {
            $new = false;
        }
        $form = $this->createForm(CategoryType::class, $category, [
            'new' => $new
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $file stores the uploaded PDF file

            try {
                /** @var File $filename */
                $file = $form->get('photo')->getData();

                if ($file) {
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
                    $category->setPhoto("uploads/categories_photo/" . $fileName);
                }


                $this->getDoctrine()->getManager()->flush();
                $this->addFlash('exito', 'Cambios guardados correctamente.');
                return $this->redirectToRoute('categorias_listar');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Ha ocurrido un error al guardar los cambios');
            }
            return $this->render('category/form.html.twig', [
                'form' => $form->createView(),
                'categoria' => $category,
                'es_nueva' => $category->getId() === null
            ]);
        }


        return $this->render('category/form.html.twig', [
            'form' => $form->createView(),
            'categoria' => $category,
            'es_nueva' => $category->getId() === null
        ]);
    }
    /**
     * @Route("/categories/eliminar/{id}", name="category_eliminar")
     */
    public function eliminarAction(Request $request, Category $category)
    {
        if ($request->get('borrar') === '') {
            try {
                $this->getDoctrine()->getManager()->remove($category);
                $this->getDoctrine()->getManager()->flush();
                $this->addFlash('exito', 'Categoría Borrada Con Éxito');
                return $this->redirectToRoute('categorias_listar');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Ha ocurrido un error al guardar los cambios');
            }
        }
        return $this->render('category/eliminar.html.twig', [
            'categoria' => $category
        ]);
    }

    private function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid());
    }

}
