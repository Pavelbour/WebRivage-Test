<?php
    namespace App\Controller;

    use App\Repository\DiscountRulesRepository;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    }