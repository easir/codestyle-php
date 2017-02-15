<?php

namespace EASIR\PHPMD\Rule;

use PHPMD\AbstractNode;
use PHPMD\AbstractRule;
use PHPMD\Rule\MethodAware;

class MixedReturnType extends AbstractRule implements MethodAware
{
    /**
     * @param AbstractNode $node
     * @return void
     */
    public function apply(AbstractNode $node)
    {
        preg_match_all('/@return\s*([^\s]+)/i', $node->getDocComment(), $matches);

        if (!empty(array_filter($matches)) && $matches[1][0] == 'mixed') {
            $this->addViolation($node);
        }
    }
}
