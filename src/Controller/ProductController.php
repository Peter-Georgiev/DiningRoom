<?php

namespace App\Controller;

use App\Entity\ClassTable;
use App\Entity\Product;
use App\Entity\Student;
use App\Entity\User;
use App\Form\ClassTableType;
use App\Form\ProductType;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Migrations\Tools\Console\Command\StatusCommand;
use phpDocumentor\Reflection\Types\This;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\NameConverter\MetadataAwareNameConverter;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ProductController extends AbstractController
{
    /**
     * @Route("/product")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @throws \Exception
     */
    public function index(Request $request)
    {
        //Only active students!!!
        $products = $this->getDoctrine()->getRepository(Product::class)->findAllActiveStudents();
        $classTables = $this->getDoctrine()->getRepository(ClassTable::class)->findAllActiveStudents();

        if (!$products || !$classTables) {
            throw new \Exception('Грешка в jsonAction');
        }

        if ($request->isXmlHttpRequest() || $request->query->get('showJson') == 1) {

            $jsonData = $this->jsonData($products, $classTables);

            return new JsonResponse($jsonData);
        } else {
            return $this->render('product/index.html.twig');
        }
        /*
                $data = json_encode(['students' => $arrStudent, 'products' => $arrProduct], JSON_UNESCAPED_UNICODE);
                return $this->render('product/index.html.twig');
                return new JsonResponse($data,200, [], JSON_UNESCAPED_UNICODE);
        */
    }

    /**
     * @Route("/product/create", name="product_create")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Exception
     */
    public function createAction(Request $request)
    {
        try {
            $product = new Product();
            $form = $this->createForm(ProductType::class, $product);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $student = $this->getDoctrine()->getRepository(Student::class)
                    ->find($request->get('student')['id']);

                if ($student) {
                    $product->setStudent($student);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($product);
                    $em->flush();
                    return $this->redirectToRoute('app_product_index');
                }
            }
            return $this->render('product/create.html.twig', ['form' => $form->createView()]);
        } catch (\Exception $e) {
            return $this->render('product/index.html.twig', ['danger' => $e->getMessage()]);
        }
    }


    /**
     * @Route("/product/edit/{id}", name="product_edit")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Exception
     */
    public function editAction(Request $request, $id)
    {
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);
        if (!$product) {
            return $this->redirectToRoute('app_product_index');
        }
        $form = $this->createForm(ProductType::class, $product);
        /*$form = $this->createFormBuilder($product)
            ->add('price')
            ->add('forMonth')
            ->add('feeInDays')
            ->add('student', EntityType::class, [
                'label' => 'USERRR',
                'class' => 'App\Entity\Student',
                'property' => 'id', // Mapped property name (default is 'id'), not required
               // 'multiple' => false, // support for an array of entities, not required
                'data' => $product->getStudent()->getClass()->getId(), // Field value by default, not required
                'block_name' => function(Student $student, $key, $value) {
                    return $student->getClass()->getId();
                },
                'attr' => array(
                    'class' => 'hidden'
                )
            ])
            ->getForm();*/
       //dd($product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $student = $this->getDoctrine()->getRepository(Student::class)
                ->find($request->get('student')['id']);
            $product->setLastEdit(new \DateTime('now'));

            if ($student) {
                $product->setStudent($student);
                $em = $this->getDoctrine()->getManager();
                $em->persist($product);
                $em->flush();
                return $this->redirectToRoute('app_product_index');
            }
        }
        return $this->render('product/edit.html.twig', ['form' => $form->createView(),
            'classID' => $product->getStudent()->getClass()->getId()
            ]);
    }

    /**
     * @Route("/product/delete/{id}", name="product_delete")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     */
    public function daleteAction($id)
    {
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);
        if (!$product) {
            return $this->redirectToRoute('product');
        }

        try {
            $em = $this->getDoctrine()->getManager();
            $em->remove($product);
            $em->flush();
        } catch (\Exception $e) {
            throw new \Exception('Плащане за месец ' . $product->getForMonth() . ' от ' .
                $product->getStudent()->getFullName() . ' ' . $product->getStudent()->getClass()->getName() .
                ' не може да бъде изтрито.'
            );
        }
        return $this->redirectToRoute('app_product_index');
    }

    /**
     * @param array|Product $products
     * @param array|ClassTable $classTables
     * @return array
     */
    private function jsonData($products, $classTables)
    {
        $i = 0;
        $arrClasses = array();
        foreach ($classTables as $c) {
            $i++;
            $arrClasses[$i] = array(
                'id' => $c->getId(),
                'name' => $c->getName(),
                'students' => array(),
            );
            foreach ($c->getStudents() as $s) {
                /** @var Student| $s */
                $arrClasses[$i]['students'][] = array(
                    'studentId' => $s->getId(),
                    'student' => $s->getFullName(),
                    'teacherId' => $s->getTeacher()->getId(),
                    'teacher' => $s->getTeacher()->getFullName(),
                );
            }
        }

        $arrProduct = array();
        foreach($products as $p) {
            $arrProduct[] = array(
                'id' => $p->getId(),
                'studentId' => $p->getStudent()->getId(),
                'student' => $p->getStudent()->getFullName(),
                'classId' => $p->getStudent()->getClass()->getId(),
                'class' => $p->getStudent()->getClass()->getName(),
                'price' => $p->getPrice(),
                'isPaid' => $p->getIsPaid(),
                'forMonth' => $p->getForMonth()->format('m.Y'),
                'feeInDays' => $p->getFeeInDays(),
                'dateCreate' => $p->getDateCreate()->format('d.m.Y H:i'),
                'lastEdit' => $p->getLastEdit()->format('d.m.Y H:i'),
            );
        }

        return ['products' => $arrProduct, 'classes' => $arrClasses];
    }
}
