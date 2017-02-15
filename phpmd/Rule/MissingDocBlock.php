<?php

namespace EASIR\PHPMD\Rule;

use PHPMD\AbstractNode;
use PHPMD\AbstractRule;
use PHPMD\Rule\MethodAware;

class MissingDocBlock extends AbstractRule implements MethodAware
{
    /**
     * @param AbstractNode $node
     * @return void
     */
    public function apply(AbstractNode $node)
    {
        if (empty($node->getDocComment())) {
            $this->addViolation($node);
        }
    }
}
