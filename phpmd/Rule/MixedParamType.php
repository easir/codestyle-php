<?php

namespace EASIR\PHPMD\Rule;

use PHPMD\AbstractNode;
use PHPMD\AbstractRule;
use PHPMD\Rule\MethodAware;

class MixedParamType extends AbstractRule implements MethodAware
{
    /**
     * @param AbstractNode $node
     * @return void
     */
    public function apply(AbstractNode $node)
    {
        preg_match_all('/@param\s(.*)/i', $node->getDocComment(), $matches);

        if (!empty(array_filter($matches))) {
            foreach ($matches[1] as $match) {
                $parts = explode(' ', $match);

                if ($parts[0] == 'mixed') {
                    $this->addViolation($node);
                }
            }
        }
    }
}
