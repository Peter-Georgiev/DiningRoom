<?php

namespace App\Controller;

use App\Entity\ClassTable;
use App\Entity\Student;
use App\Entity\Teacher;
use App\Form\StudentType;
use App\Repository\StudentRepository;
use Doctrine\DBAL\Driver\AbstractDB2Driver;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

class StudentController extends AbstractController
{
    /**
     * @Route("/student", name="student")
     ** @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function index()
    {
        $students = $this->getDoctrine()->getRepository(Student::class)->findAll();
        return $this->render('student/index.html.twig', ['students' => $students
        ]);
    }

    /**
     * @param Request $request
     * @Route("/student/create", name="student_create")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function createAction(Request $request)
    {
        try {
            $student = new Student();
            $form = $this->createForm(StudentType::class, $student);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $classTable = $this->getDoctrine()->getRepository(ClassTable::class)
                    ->find($request->get('class_table')['id']);
                $teacher = $this->getDoctrine()->getRepository(Teacher::class)
                    ->find($request->get('teacher')['id']);
                $student->setClass($classTable);
                $student->setTeacher($teacher);
                // Uppercase the first character of each word in a string
                $fullName = $this->strToLoweAndUcFirst($student->getFullName());
                $student->setFullName($fullName);

                $this->findExistingStudent($student);

                try {
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($student);
                    $em->flush();
                    return $this->redirectToRoute('student');
                } catch (\Exception $e) {
                    throw new \Exception('Възникна грешка при запис на ученик ' . $student->getFullName() . '!');
                }
            }

            $students = $this->getDoctrine()->getRepository(Student::class)->findAll();
            $classTables = $this->getDoctrine()->getRepository(ClassTable::class)->findAll();
            $teachers = $this->getDoctrine()->getRepository(Teacher::class)->findAll();
            return $this->render('student/create.html.twig', ['form' => $form->createView(),
                'students' => $students, 'classTables' => $classTables, 'teachers' => $teachers
            ]);
        } catch (\Exception $e) {
            $students = $this->getDoctrine()->getRepository(Student::class)->findAll();
            return $this->render('student/index.html.twig', [
                'students' => $students, 'danger' => $e->getMessage()
            ]);
        }
    }

    /**
     * @Route("/student/edit/{id}", name="student_edit")
     ** @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function editAction($id, Request $request)
    {
        try {
            $student = $this->getDoctrine()->getRepository(Student::class)->find($id);
            $name = $student->getFullName();
            if (!$student) {
                return $this->redirectToRoute("student");
            }

            $form = $this->createForm(StudentType::class, $student);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $classTable = $this->getDoctrine()->getRepository(ClassTable::class)
                    ->find($request->get('class_table')['id']);
                $teacher = $this->getDoctrine()->getRepository(Teacher::class)
                    ->find($request->get('teacher')['id']);
                $student->setClass($classTable);
                $student->setTeacher($teacher);
                // Uppercase the first character of each word in a string
                $fullName = $this->strToLoweAndUcFirst($student->getFullName());
                $student->setFullName($fullName);

               $this->findExistingStudent($student, $name);

                try {
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($student);
                    $em->flush();
                    return $this->redirectToRoute('student');
                } catch (\Exception $e) {
                    throw new \Exception('Възникна грешка при промяна на ученик '. $student->getFullName() .'!');
                }
            }
            $classTables = $this->getDoctrine()->getRepository(ClassTable::class)->findAll();
            $teachers = $this->getDoctrine()->getRepository(Teacher::class)->findAll();
            $students = $this->getDoctrine()->getRepository(Student::class)->findAll();
            return $this->render('student/edit.html.twig', ['form' => $form->createView(),
                'students' => $students, 'classTables' => $classTables, 'teachers' => $teachers,
                'student' => $student
            ]);
        } catch (\Exception $e) {
            $students = $this->getDoctrine()->getRepository(Student::class)->findAll();
            return $this->render('student/index.html.twig', [
                'students' => $students, 'danger' => $e->getMessage()
            ]);
        }
    }

    /**
     * @Route("/student/delete/{id}", name="student_delete")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function deleteAction($id)
    {
        $student = $this->getDoctrine()->getRepository(Student::class)->find($id);
        $message = '';
        if (!$student) {
            return $this->redirectToRoute("student");
        }

        try {
            $em = $this->getDoctrine()->getManager();
            $em->remove($student);
            $em->flush();
            $message = 'Ученика "' . $student->getFullName() . '" е успешно изтрит.';
        } catch (\Exception $e) {
            $message = '"' . $student->getFullName() . '" не може да бъде изтрит/а, защото има заявки.';
        }

        $students = $this->getDoctrine()->getRepository(Student::class)->findAll();
        return $this->render('student/index.html.twig', ['students' => $students, 'danger' => $message]);
    }


    /**
     * @Route("/student/pagination", name="student_pagination")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function paginationData(StudentRepository $repository, Request $request, PaginatorInterface $paginator)
    {
        $queryBuilder = $this->getDoctrine()->getRepository(Student::class)->findAll();

        $pagination = $paginator->paginate(
            $queryBuilder, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );

        //$student = new Student();
        //$form = $this->createForm(StudentType::class, $student);
        //$form->handleRequest($request);

        $students = $this->getDoctrine()->getRepository(Student::class)->findAll();
        dd($students);

        return $this->render('student/pagination-test.html.twig', [//'form' => $form->createView(),
            'pagination' => $pagination,
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

    private function findExistingStudent(Student $student, string $name = null)
    {
        $existingArrayStudent = $this->getDoctrine()->getRepository(Student::class)
            ->findByFullName($student);

        if ($existingArrayStudent) {
            /** @var Student $existingStudent */
            $existingStudent = $existingArrayStudent[0];
            $isExist = false;
            if (!$name) {
                $isExist = true;
            } elseif ($existingStudent->getId() !== $student->getId()) {
                $student->setFullName($name);
                $isExist = true;
            }

            if ($isExist) {
                throw new \Exception('"' . $existingStudent->getFullName() . '" вече съществува от "' .
                    $existingStudent->getClass()->getName() . '" клас!');
            }
        }
        return false;
    }
}
