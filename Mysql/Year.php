<?php

/*
 * Mysql YEAR Function Adaption
 */

namespace Hasheado\BlogBundle\Mysql;

use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;

/**
 * "YEAR" MYSQL function adaption
 * @author      Emiliano Viada <emjovi@gmail.com>
 */
class Year extends FunctionNode
{
    public $date;

    /**
     * getSql()
     */
    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {
        return 'YEAR(' . $sqlWalker->walkArithmeticPrimary($this->date) . ')';
    }

    /**
     * parse()
     */
    public function parse(\Doctrine\ORM\Query\Parser $parser)
    {
        $lexer = $parser->getLexer();

        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        $this->date = $parser->ArithmeticPrimary();

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}
