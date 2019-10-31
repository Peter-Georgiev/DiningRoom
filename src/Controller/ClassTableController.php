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
                //Transliterate cyr text - Uppercase the first character of each word in a string
                $classTable->setName(
                    $this->strTransToLoweAndUcFirst($classTable->getName())
                );

                $this->findExistingClass($classTable);

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
                //Transliterate cyr text - Uppercase the first character of each word in a string
                $classTable->setName(
                    $this->strTransToLoweAndUcFirst($classTable->getName())
                );

                $this->findExistingClass($classTable, $name);

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

    private function strTransToLoweAndUcFirst(string $str)
    {
        $string = '';
        $token = array_map('trim', explode(' ', $str));
        for ($i = 0; $i < count($token); $i++) {
            if ($token[$i] !== '') {
                $t  = $this->transliterate(null,$token[$i]);
                $string .= mb_convert_case($t, MB_CASE_TITLE,  'UTF-8');
            }
        }
        return $string;
    }

    private function transliterate($textcyr = null, $textlat = null) {
        $cyr = array(
            'ж',  'ч',  'щ',   'ш',  'ю',  'а', 'б', 'в', 'г', 'д', 'е', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п',
            'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ъ', 'ь', 'я', 'Ж',  'Ч',  'Щ',   'Ш',  'Ю',  'А', 'Б', 'В', 'Г', 'Д',
            'Е', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ъ', 'Ь', 'Я');
        $lat = array(
            'zh', 'ch', 'sht', 'sh', 'yu', 'a', 'b', 'v', 'g', 'd', 'e', 'z', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p',
            'r', 's', 't', 'u', 'f', 'h', 'c', 'y', 'x', 'q', 'Zh', 'Ch', 'Sht', 'Sh', 'Yu', 'A', 'B', 'V', 'G', 'D',
            'E', 'Z', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'c', 'Y', 'X', 'Q');
        if($textcyr) {
            return str_replace($cyr, $lat, $textcyr);
        } else if($textlat) {
            return str_replace($lat, $cyr, $textlat);
        }
        else return null;
    }

    private function findExistingClass(ClassTable $classTable, string $name = null)
    {
        $existingArrayClass = $this->getDoctrine()->getRepository(ClassTable::class)
            ->findByClassName($classTable);

        if ($existingArrayClass) {
            /** @var ClassTable $existingClass */
            $existingClass = $existingArrayClass[0];
            $isExist = false;
            if (!$name) {
                $isExist = true;
            } elseif ($existingClass->getId() !== $classTable->getId()) {
                $classTable->setName($name);
                $isExist = true;
            }

            if ($isExist) {
                throw new \Exception('"' . $existingClass->getName() . '" вече съществува!');
            }
        }
        return false;
    }
}
