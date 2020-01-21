<?php
    namespace App\Command;

    use App\Entity\DiscountRules;
    use App\Entity\Products;
    use App\Repository\DiscountRulesRepository;
    use App\Repository\ProductsRepository;
    use Doctrine\ORM\EntityManagerInterface;
    use Symfony\Component\Console\Command\Command;
    use Symfony\Component\Console\Input\InputInterface;
    use Symfony\Component\Console\Output\OutputInterface;
    use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
    use Symfony\Component\Mailer\Mailer;
    use Symfony\Component\Mailer\Exception\TransportException;
    use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
    use Symfony\Component\Mime\Email;

    class Calculate extends Command
    {
        protected static $defaultName = 'discounts:calculate';
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
            parent::__construct();
        }

        protected function execute(InputInterface $input, OutputInterface $output)
        {
            $output->writeln(["Processing the products..."]);
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
            $html = '';
            foreach ($discountedProducts as $product) {
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
                return 0;
            }
            return 0;
        }
    }