<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DiscountRulesRepository")
 */
class DiscountRules
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ruleExpression;

    /**
     * @Assert\NotBlank
     * @Assert\Range(min = 1, max = 50)
     * @Assert\Type("integer")
     * @ORM\Column(type="smallint")
     */
    private $discountPercent;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRuleExpression(): ?string
    {
        return $this->ruleExpression;
    }

    public function setRuleExpression(string $ruleExpression): self
    {
        $this->ruleExpression = $ruleExpression;

        return $this;
    }

    public function getDiscountPercent(): ?int
    {
        return $this->discountPercent;
    }

    public function setDiscountPercent(int $discountPercent): self
    {
        $this->discountPercent = $discountPercent;

        return $this;
    }
}
