<?php

namespace App\Controller;

use App\Entity\ClassTable;
use App\Form\ClassTableType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ClassTableController extends AbstractController
{
    /**
     * @Route("/class", name="class")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function index()
    {
        $classTables = $this->getDoctrine()->getRepository(ClassTable::class)->findAll();
        return $this->render('class_table/index.html.twig', ['classTables' => $classTables,
        ]);
    }

    /**
     * @Route("/class/create", name="class_create")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function createAction(Request $request)
    {
        try {
            $classTable = new ClassTable();
            $form = $this->createForm(ClassTableType::class, $classTable);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $existingClass = $this->getDoctrine()->getRepository(ClassTable::class)
                    ->findByNameClass($classTable);
                if ($existingClass) {
                    throw new \Exception('"' . $classTable->getName() . '" вече съществува!');
                }

                try {
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($classTable);
                    $em->flush();
                    return $this->redirectToRoute('class');
                } catch (\Exception $e) {
                    throw new \Exception('Възникана грешка при запис на клас!');
                }
            }

            $classTables = $this->getDoctrine()->getRepository(ClassTable::class)->findAll();
            return $this->render('class_table/create.html.twig', ['form' => $form->createView(),
                'classTables' => $classTables,
            ]);
        } catch (\Exception $e) {
            $classTables = $this->getDoctrine()->getRepository(ClassTable::class)->findAll();
            return $this->render('class_table/index.html.twig', ['classTables' => $classTables,
                'danger' => $e->getMessage()
            ]);
        }
    }

    /**
     * @Route("/class/edit/{id}", name="class_edit")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function editAction($id, Request $request)
    {
        try {
            $classTable = $this->getDoctrine()->getRepository(ClassTable::class)->find($id);
            $name = $classTable->getName();
            if (!$classTable) {
                return $this->redirectToRoute("class");
            }

            $form = $this->createForm(ClassTableType::class, $classTable);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                /** @var ClassTable $existingClass */
                $existingClass = $this->getDoctrine()->getRepository(ClassTable::class)
                    ->findByNameClass($classTable)[0];
                if ($existingClass && $existingClass->getId() !== $classTable->getId()) {
                    $classTable->setName($name);
                    throw new \Exception('"' . $existingClass->getName() . '" вече съществува!');
                }

                try {
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($classTable);
                    $em->flush();
                    return $this->redirectToRoute('class');
                } catch (\Exception $e) {
                    throw new \Exception( 'Възникана грешка при запис на клас!');
                }
            }

            $classTables = $this->getDoctrine()->getRepository(ClassTable::class)->findAll();
            return $this->render('class_table/edit.html.twig', ['form' => $form->createView(),
                'classTables' => $classTables,
            ]);
        } catch (\Exception $e) {
            $classTables = $this->getDoctrine()->getRepository(ClassTable::class)->findAll();
            return $this->render('class_table/index.html.twig', ['classTables' => $classTables,
                'danger' => $e->getMessage()
            ]);
        }
    }

    /**
     * @Route("/class/delete/{id}", name="class_delete")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function deleteAction($id)
    {
        $classTable = $this->getDoctrine()->getRepository(ClassTable::class)->find($id);
        $message = '';
        if (!$classTable) {
            return $this->redirectToRoute("class");
        }

        try {
            $em = $this->getDoctrine()->getManager();
            $em->remove($classTable);
            $em->flush();
            $message = '"' . $classTable->getName() . '" клас е успешно изтрит.';
        } catch (\Exception $e) {
            $message = '"' . $classTable->getName() . '" клас не може да бъде изтрит, защото е свързан с ' .
                $classTable->getStudents()->count() . ' учаник/а.';
        }

        $classTables = $this->getDoctrine()->getRepository(ClassTable::class)->findAll();
        return $this->render('class_table/index.html.twig', ['classTables' => $classTables, 'danger' => $message
        ]);
    }
}
