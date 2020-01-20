<?php
    namespace App\Services;

    use App\Entity\DiscountRules;
    use App\Entity\Products;
    use App\Repository\DiscountRulesRepository;
    use App\Repository\ProductsRepository;
    use Doctrine\ORM\EntityManagerInterface;
    use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

    class CalculateDiscount
    {
        private $discountRulesRepository;
        private $productsRepository;
        private $em;

        public function __construct(DiscountRulesRepository $discountRulesRepository, ProductsRepository $productsRepository, EntityManagerInterface $entityManagerInterface)
        {
            $this->discountRulesRepository = $discountRulesRepository;
            $this->productsRepository = $productsRepository;
            $this->em = $entityManagerInterface;
        }
        
        public function calculate() {
            $discountedProducts = array();
            $el = new ExpressionLanguage();
            $rules = $this->discountRulesRepository
                ->findAll();
            $products = $this->productsRepository
                ->findAll();
            foreach ($rules as $rule) {
                $discount = $rule->getDiscountPercent() / 100;
                $rule = $rule->getRuleExpression();
                $rule = str_replace('product.type', 'product.getType()', $rule);
                $rule = str_replace('product.price', 'product.getPrice()', $rule);
                $rule = str_replace('=', '==', $rule);
                foreach ($products as $product) {
                    if ($el->evaluate($rule, ['product' => $product])) {
                        $product->setDiscountedPrice($product->getPrice() - $product->getPrice() * $discount);
                        $discountedProducts[] = $product;
                    }
                }
            }
            $this->em->flush();
            return $discountedProducts;
        }
        
        public function calculateDQL()
        {
            $discountedProducts = array();
            $rules = $this->discountRulesRepository
                ->findAll();
            foreach ($rules as $rule) {
                $discount = $rule->getDiscountPercent() / 100;
                $products = $this->productsRepository->getProducts($rule->getRuleExpression());
                foreach ($products as $product) {
                    $product->setDiscountedPrice($product->getPrice() - $product->getPrice() * $discount);
                    $discountedProducts[] = $product;
                }
            }
            $this->em->flush();
            return $discountedProducts;
        }
    }