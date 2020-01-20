<?php
    namespace App\Services;

    use App\Entity\DiscountRules;
    use App\Entity\Products;
    use App\Repository\DiscountRulesRepository;
    use App\Repository\ProductsRepository;
    use Doctrine\ORM\EntityManagerInterface;
    use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
    use Symfony\Component\Mailer\Mailer;
    use Symfony\Component\Mailer\Exception\TransportException;
    use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
    use Symfony\Component\Mime\Email;

    class CalculateDiscount
    {
        private $discountRulesRepository;
        private $productsRepository;
        private $em;
        private $mailer;

        public function __construct(DiscountRulesRepository $discountRulesRepository, ProductsRepository $productsRepository, EntityManagerInterface $entityManagerInterface)
        {
            $this->discountRulesRepository = $discountRulesRepository;
            $this->productsRepository = $productsRepository;
            $this->em = $entityManagerInterface;
            $transport = new EsmtpTransport("localhost");
            $this->mailer = new Mailer($transport);
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

        public function sendMessage($products)
        {
            $html = '';
            foreach ($products as $product) {
                $html .= "<div>{{ product.name }}</div>
                            <div>{{ product.price }}</div>
                            <div>{{ product.discountedPrice }}</div>";
            }
            $email = (new Email())
                    ->from("e-shop@example.com")
                    ->to("you@example.com")
                    ->subject("Les nuveaux bons plans du")
                    ->html($html);
            try {
                $this->mailer->send($email);
            } catch (TransportException $e) {
                return false;
            }
            return true;
        }
    }