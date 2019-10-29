<?php

namespace App\Controller;

use App\Entity\Payment;
use App\Entity\Product;
use App\Form\PaymentType;
use mysql_xdevapi\Exception;
use phpDocumentor\Reflection\Types\This;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
// Include Dompdf required namespaces
use Dompdf\Dompdf;
use Dompdf\Options;

class PaymentController extends AbstractController
{
    /**
     * @Route("/payment", name="payment")
     * @@Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @throws \Exception
     */
    public function index(Request $request)
    {
        $payments = $this->getDoctrine()->getRepository(Payment::class)->findAll();

        if (!$payments) {
            throw new \Exception('Грешка в jsonAction');
        }

        if ($request->isXmlHttpRequest() || $request->query->get('showJson') == 1) {

            $jsonData = $this->jsonData($payments);

            return new JsonResponse($jsonData);
        } else {
            return $this->render('payment/index.html.twig');
        }
        /*
                $data = json_encode(['students' => $arrStudent, 'products' => $arrProduct], JSON_UNESCAPED_UNICODE);
                return $this->render('product/index.html.twig');
                return new JsonResponse($data,200, [], JSON_UNESCAPED_UNICODE);
        */
    }

    /**
     * @Route("/payment/pdf/{id}", name="payment_pdf")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */

    public function pdftAction($id, Request $request)
    {
        $payment = $this->getDoctrine()->getRepository(Payment::class)->find($id);
        if (!$payment) {
            return $this->redirectToRoute('payment');
        }
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('payment/pdf-temp.html.twig', [
            'payment' => $payment
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (inline view)
        $dompdf->stream("mypdf.pdf", [
            "Attachment" => false
        ]);
    }

    /**
     * @Route("/payment/product/{id}", name="payment_product")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function paymentAction($id, Request $request)
    {
        if ($this->getUser()->isUser()) {
            trigger_error('Нямате права за тази операция.');
            //throw new Exception('Нямате права за тази операция.');
            //return $this->redirectToRoute('/product');
        }

        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);
        if (!$product) {
            return $this->redirectToRoute('app_product_index');
        }

        $payment = new Payment();
        $payment->setProducts($product);
        $payment->setPrice($product->getPrice());
        $payment->setSeller($this->getUser()->getFullName());
        $payment->setLastEditUser($this->getUser()->getFullName());
        $form = $this->createForm(PaymentType::class, $payment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productPrice = floatval ($product->getPrice());
            if (floatval ($payment->getPayment()) !== $productPrice ||
                floatval ($payment->getPrice()) !== $productPrice) {
                $message ='Полето "Цена за плащане '. $product->getPrice() .
                    '" трябва да съвпада с полето "Въведи сума в лева".';
                return $this->render('payment/product-paid.html.twig', [ 'form' => $form->createView(),
                    'payment', $payment, 'product' => $product, 'danger' => $message
                ]);
            }

            // update is_paid
            $this->getDoctrine()->getRepository(Product::class)
                ->updateIsPaidInProduct($product->getId(),true);
            $em = $this->getDoctrine()->getManager();
            $em->persist($payment);
            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('app_product_index');
        }

        return $this->render('payment/product-paid.html.twig', [ 'form' => $form->createView(),
            'payment', $payment, 'product' => $product
        ]);
    }

    /**
     * @Route("/payment/edit/{id}", name="payment_edit")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, $id)
    {
        $payment = $this->getDoctrine()->getRepository(Payment::class)->find($id);
        if (!$payment) {
            return $this->redirectToRoute('payment');
        }

        $product = $this->getDoctrine()->getRepository(Product::class)
            ->find($payment->getProducts()->getId());
        if (!$product) {
            return $this->redirectToRoute('payment');
        }

        $form = $this->createForm(PaymentType::class, $payment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($payment);
            $em->flush();
            return $this->redirectToRoute('payment');
        }

        return $this->render('payment/edit.html.twig', ['form' => $form->createView(),
            'payment', $payment, 'product' => $product
            ]);
    }

    /**
     * @Route("/payment/delete/{id}", name="payment_delete")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     */
    public function daleteAction($id)
    {
        $payment = $this->getDoctrine()->getRepository(Payment::class)->find($id);
        if (!$payment) {
            return $this->redirectToRoute('payment');
        }
        $product = $this->getDoctrine()->getRepository(Product::class)
            ->find($payment->getProducts()->getId());
        if (!$product) {
            return $this->redirectToRoute('payment');
        }

        try {
            $product->setIsPaid(false);
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->remove($payment);
            $em->flush();
        } catch (\Exception $e) {
            throw new \Exception('Плащане за месец ' . $payment->getProducts()->getForMonth() . ' от ' .
                $payment->getProducts()->getStudent()->getFullName() . ' ' .
                $payment->getProducts()->getStudent()->getClass()->getName() . ' не може да бъде изтрито.'
            );
        }
        return $this->redirectToRoute('payment');
    }


    /**
     * @param array|Payment $payments
     * @return array
     */
    private function jsonData($payments)
    {
        $arrPayment = array();
        foreach ($payments as $payment) {
            $arrPayment[] = array(
                'id' => $payment->getId(),
                'price' => $payment->getPrice(),
                'payment' => $payment->getPayment(),
                'datePurchases' => $payment->getDatePurchases()->format('d.m.Y H:i'),
                'lastEdit' => $payment->getLastEdit()->format('d.m.Y H:i'),
                'namePayer' => $payment->getNamePayer(),
                'student' => $payment->getProducts()->getStudent()->getFullName(),
                'class' => $payment->getProducts()->getStudent()->getClass()->getName(),
                'teacher' => $payment->getProducts()->getStudent()->getTeacher()->getFullName(),
                'forMonth' => $payment->getProducts()->getForMonth()->format('m.Y'),
                'user' => $this->getUser()->getFullName(),
                'userRole' => $this->getUser()->getRoles()[0],
                'lastEditUser' =>$payment->getLastEditUser(),
            );
        }

        return $arrPayment;
    }
}
