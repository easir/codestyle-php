<?php

namespace EASIR\PHPMD\Rule;

use PHPMD\AbstractNode;
use PHPMD\Node\AbstractTypeNode;
use PHPMD\Node\ASTNode;
use PHPMD\Rule\AbstractLocalVariable;
use PHPMD\Rule\ClassAware;
use PHPMD\Rule\InterfaceAware;

class MissingTrailingCommaForArrays extends AbstractLocalVariable implements ClassAware, InterfaceAware
{
    /**
     * @param AbstractNode $node
     * @return void
     */
    public function apply(AbstractNode $node)
    {
        foreach ($this->collectViolations($node) as $violation) {
            $this->addViolation($violation);
        }
    }

    /**
     * @param AbstractTypeNode $class
     * @return array
     */
    private function collectViolations(AbstractTypeNode $class) : array
    {
        $declarations = $this->mergeDeclarations($class);

        if ($this->fileHasNoArrays($declarations)) {
            return [];
        }

        $file = $this->loadFile($class);
        $violations = [];

        foreach ($declarations as $declaration) {
            foreach ($this->mergeArrayNodes($declaration) as $array) {
                $start = $array->getBeginLine();
                $end = $array->getEndLine();
                $length = $end - $start;

                if (!$length) {
                    continue;
                }

                $lines = $this->getLinesAsArray($start, $end, $file);

                if ($this->isArray($lines, $length)
                    && !$this->arrayFormattingIsOk($this->getLastCharacterInArray($lines, $length))
                ) {
                    $violations[] = $array;
                }
            }
        }

        return $violations;
    }

    /**
     * @param array $declarations
     * @return bool
     */
    private function fileHasNoArrays(array $declarations) : bool
    {
        return empty($declarations);
    }

    /**
     * @param AbstractNode $class
     * @return array
     */
    private function loadFile(AbstractNode $class) : array
    {
        return file($class->getFileName());
    }

    /**
     * @param int $start
     * @param int $end
     * @param array $file
     * @return array
     */
    private function getLinesAsArray(int $start, int $end, array $file) : array
    {
        $range = range($start - 1, $end - 1);
        $lines = array_values(
            array_intersect_key($file, array_flip($range))
        );

        return array_map('trim', $lines);
    }

    /**
     * @param array $lines
     * @param int $length
     * @return bool
     */
    private function isArray(array $lines, int $length) : bool
    {
        return mb_strpos($lines[$length], '];') !== false;
    }

    /**
     * @param array $lines
     * @param int $length
     * @return string
     */
    private function getLastCharacterInArray(array $lines, int $length) : string
    {
        $line = $lines[$length - 1];
        $line = trim(preg_replace('@(?<!")(?:^|[\t\r\n]|([ ;}\r\n]))(/\*(?:([^*]|\*(?=[^/]))*\*/)|//.*)@','$1', $line));

        return mb_substr($line, -1);
    }

    /**
     * @param string $lastChar
     * @return bool
     */
    private function arrayFormattingIsOk(string $lastChar) : bool
    {
        return $lastChar === ',';
    }

    /**
     * @param AbstractNode $class
     * @return array
     */
    private function mergeDeclarations(AbstractNode $class) : array
    {
        return array_merge(
            $class->findChildrenOfType('FieldDeclaration'),
            $class->findChildrenOfType('AssignmentExpression'),
            $class->findChildrenOfType('ConstantDefinition')
        );
    }

    /**
     * @param ASTNode $declaration
     * @return array
     */
    private function mergeArrayNodes(ASTNode $declaration) : array
    {
        return array_merge(
            $declaration->findChildrenOfType('Array'),
            $declaration->findChildrenOfType('VariableDeclarator'),
            $declaration->findChildrenOfType('ConstantDeclarator')
        );
    }
}
