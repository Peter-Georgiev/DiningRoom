<?php

namespace App\Controller;

use App\Entity\ClassTable;
use App\Entity\Teacher;
use App\Form\TeacherType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TeacherController extends AbstractController
{
    /**
     * @Route("/teacher", name="teacher")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function index()
    {
        $teachers = $this->getDoctrine()->getRepository(Teacher::class)->findAll();
        return $this->render('teacher/index.html.twig', ['teachers' => $teachers
        ]);
    }

    /**
     * @Route("/teacher/create", name="teacher_create")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function createAction(Request $request)
    {
        try {
            $teacher = new Teacher();
            $form = $this->createForm(TeacherType::class, $teacher);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                // Uppercase the first character of each word in a string
                $fullName = $this->strToLoweAndUcFirst($teacher->getFullName());
                $teacher->setFullName($fullName);

                $this->findExistingTeacher($teacher);

                try {
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($teacher);
                    $em->flush();
                    return $this->redirectToRoute('teacher');
                } catch (\Exception $e) {
                    throw new \Exception('Възникана грешка при запис на учител!');
                }
            }

            $teachers = $this->getDoctrine()->getRepository(Teacher::class)->findAll();
            return $this->render('teacher/create.html.twig', ['form' => $form->createView(),
                'teachers' => $teachers,
            ]);

        } catch (\Exception $e) {
            $teachers = $this->getDoctrine()->getRepository(Teacher::class)->findAll();
            return $this->render('teacher/index.html.twig', ['teachers' => $teachers,
                'danger' => $e->getMessage()
            ]);
        }
    }

    /**
     * @Route("/teacher/edit/{id}", name="teacher_edit")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function editAction($id, Request $request)
    {
        try {
            $teacher = $this->getDoctrine()->getRepository(Teacher::class)->find($id);
            $name = $teacher->getFullName();
            if (!$teacher) {
                return $this->redirectToRoute("teacher");
            }

            $form = $this->createForm(TeacherType::class, $teacher);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                // Uppercase the first character of each word in a string
                $fullName = $this->strToLoweAndUcFirst($teacher->getFullName());
                $teacher->setFullName($fullName);

                $this->findExistingTeacher($teacher, $name);

                try {
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($teacher);
                    $em->flush();
                    return $this->redirectToRoute('teacher');
                } catch (\Exception $e) {
                    throw new \Exception( 'Възникана грешка при запис на учител!');
                }
            }

            $teachers = $this->getDoctrine()->getRepository(Teacher::class)->findAll();
            return $this->render('teacher/edit.html.twig', ['form' => $form->createView(),
                'teachers' => $teachers
            ]);
        } catch (\Exception $e) {
            $teachers = $this->getDoctrine()->getRepository(Teacher::class)->findAll();
            return $this->render('teacher/index.html.twig', ['teachers' => $teachers,
                'danger' => $e->getMessage()
            ]);
        }
    }

    /**
     * @Route("/teacher/delete/{id}", name="teacher_delete")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function deleteAction($id)
    {
        $teacher = $this->getDoctrine()->getRepository(Teacher::class)->find($id);
        $message = '';
        if (!$teacher) {
            return $this->redirectToRoute("teacher");
        }

        try {
            $em = $this->getDoctrine()->getManager();
            $em->remove($teacher);
            $em->flush();
            $message = '"' . $teacher->getFullName() . '" учител е успешно изтрит.';
        } catch (\Exception $e) {
            $message = '"' . $teacher->getFullName() . '" учител не може да бъде изтрит, защото е свързан с ' .
                $teacher->getStudents()->count() . ' учаник/а.';
        }

        $teachers = $this->getDoctrine()->getRepository(Teacher::class)->findAll();
        return $this->render('teacher/index.html.twig', ['teachers' => $teachers, 'danger' => $message
        ]);
    }

    private function strToLoweAndUcFirst(string $str)
    {
        $string = '';
        $token = array_map('trim', explode(' ', $str));
        for ($i = 0; $i < count($token); $i++) {
            if ($token[$i] !== '') {
                $string .= mb_convert_case($token[$i], MB_CASE_TITLE,  'UTF-8');
                if ($i < count($token) - 1) {
                    $string .= ' ';
                }
            }
        }
        return $string;
    }

    private function findExistingTeacher(Teacher $teacher, string $name = null)
    {
        $existingArrayTeacher = $this->getDoctrine()->getRepository(Teacher::class)
            ->findByFullName($teacher);

        if ($existingArrayTeacher) {
            /** @var Teacher $existingTeacher */
            $existingTeacher = $existingArrayTeacher[0];
            $isExist = false;
            if (!$name) {
                $isExist = true;
            } elseif ($existingTeacher->getId() !== $teacher->getId()) {
                $teacher->setFullName($name);
                $isExist = true;
            }

            if ($isExist) {
                throw new \Exception('"' . $existingTeacher->getFullName() . '" вече съществува!');
            }
        }
        return false;
    }
}
