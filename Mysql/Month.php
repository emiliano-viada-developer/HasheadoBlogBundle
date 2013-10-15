<?php

/*
 * Mysql Function MONTH adaption
 */

namespace Hasheado\BlogBundle\Mysql;

use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;

/**
 * "MONTH" MYSQL function adaption
 *
 * @author      Emiliano Viada <emjovi@gmail.com>
 */
class Month extends FunctionNode
{
    public $date;

    /**
     * getSql()
     */
    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {
        return 'MONTH(' . $sqlWalker->walkArithmeticPrimary($this->date) . ')';
    }

    /**
     * parse()
     */
    public function parse(\Doctrine\ORM\Query\Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        $this->date = $parser->ArithmeticPrimary();

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}
