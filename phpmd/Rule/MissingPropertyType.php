<?php

namespace EASIR\PHPMD\Rule;

use PHPMD\AbstractNode;
use PHPMD\AbstractRule;
use PHPMD\Rule\ClassAware;

class MissingPropertyType extends AbstractRule implements ClassAware
{
    /**
     * @param AbstractNode $node
     * @return void
     */
    public function apply(AbstractNode $node)
    {
        foreach ($node->findChildrenOfType('FieldDeclaration') as $property) {
            if (empty($comment = $property->getComment())) {
                continue;
            }

            if ($this->propertyFails($comment)) {
                $this->addViolation($property);
            }
        }
    }

    /**
     * @param string $comment
     * @return bool
     */
    private function propertyFails($comment)
    {
        preg_match_all('/@var\s(.*)/i', $comment, $matches);

        // This is not very pretty, but it works. Sorry
        // Here's a picture of a stingray
        // https://c1.staticflickr.com/3/2286/1902675442_6049c17d30_b.jpg
        $varNotFound = empty(array_filter($matches));
        $varEmpty = isset($matches[1], $matches[1][0]) && trim($matches[1][0]) == '*/';
        return $varNotFound || $varEmpty;
    }
}
