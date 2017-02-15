<?php

namespace EASIR\PHPMD\Rule;

use PHPMD\AbstractNode;
use PHPMD\AbstractRule;
use PHPMD\Rule\MethodAware;

class MissingReturnType extends AbstractRule implements MethodAware
{
    /**
     * @param AbstractNode $node
     * @return void
     */
    public function apply(AbstractNode $node)
    {
        $docBlock = $node->getDocComment();

        if ($node->getImage() == '__construct' || empty($docBlock)) {
            return;
        }

        preg_match_all('/@return\s*([^\s]+)/i', $docBlock, $matches);

        if (empty(array_filter($matches))) {
            $this->addViolation($node);
        }
    }
}
