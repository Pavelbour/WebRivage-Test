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
         * @Route("/discounts", name="discounts")
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
         * @Route("/discounts/add-new-discount", name="add-new-discount")
         */
        public function addNewRule(Request $request)
        {
            $rule = new DiscountRules();
            $ruleString = "product.type = '";

            $form = $this->createFormBuilder($rule)
                ->add('type', TextType::class, ['mapped' => false, 'label' => 'Category', 'required' => true])
                ->add('minPrice', IntegerType::class, ['mapped' => false, 'label' => 'Le coût minimal', 'required' => false])
                ->add('maxPrice', IntegerType::class, ['mapped' => false, 'label' => 'Le coût maximal', 'required' => false])
                ->add('discountPercent', IntegerType::class)
                ->add('save', SubmitType::class, ['label' => 'Ajouter'])
                ->getForm();

                $form->handleRequest($request);
                if ($form->isSubmitted() && $form->isValid()) {
                    $ruleString .= $form->get('type')->getData();
                    $ruleString .= "'";
                    if ($form->get('minPrice')->getData()) {
                        $ruleString .= " and product.price >= ";
                        $ruleString .= $form->get('minPrice')->getData();
                    }
                    if ($form->get('maxPrice')->getData()) {
                        $ruleString .= " and product.price <= ";
                        $ruleString .= $form->get('maxPrice')->getData();
                    }
                    $rule->setRuleExpression($ruleString);
                    $rule->setDiscountPercent($form->get('discountPercent')->getData());
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($rule);
                    $em->flush();

                    return $this->redirectToRoute('discounts');
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