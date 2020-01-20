<?php
    namespace App\Controller;

    use App\Entity\DiscountRules;
    use App\Repository\DiscountRulesRepository;
    use App\Services\CalculateDiscount;
    use Doctrine\ORM\EntityManagerInterface;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\Form\Extension\Core\Type\IntegerType;
    use Symfony\Component\Form\Extension\Core\Type\SubmitType;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;

    class DiscountsController extends AbstractController
    {
        /**
         * @Route("/discounts")
         */
        public function discounts(DiscountRulesRepository $discountRulesRepository)
        {
            $rules = $discountRulesRepository
                ->findAll();
            return $this->render('discounts.html.twig', [
                'rules' => $rules
            ]);
        }

        /**
         * @Route("/discounts/add-new-discount")
         */
        public function addNewRule(Request $request)
        {
            $rule = new DiscountRules();

            $form = $this->createFormBuilder($rule)
                ->add('ruleExpression', TextType::class)
                ->add('discountPercent', IntegerType::class)
                ->add('save', SubmitType::class, ['label' => 'Ajouter'])
                ->getForm();

                $form->handleRequest($request);
                if ($form->isSubmitted() && $form->isValid()) {
                    $rule = $form->getData();
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($rule);
                    $em->flush();

                    return $this->redirectToRoute('/discounts');
                }

                return $this->render('newRuleForm.html.twig', [
                    'form' => $form->createView()
                ]);
        }

        /**
         * @Route("/calculate-discounts")
         */
        public function Test(CalculateDiscount $calcule)
        {
            $products = $calcule->calculate();
            if(!$calcule -> sendMessage($products)) {
                $this->addFlash("error", "Impossible d'envoyer le message.");
            }
            return $this->render('calculateDiscount.html.twig', [
                'products' => $products
            ]);
        }
    }