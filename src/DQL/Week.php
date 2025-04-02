<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\DQL;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\Query\SqlWalker;

class Week extends FunctionNode
{
    public $date;

    public $mode;

    /**
     * @throws QueryException
     */
    public function getSql(SqlWalker $sqlWalker): string
    {
        $sql = 'WEEK('.$sqlWalker->walkArithmeticPrimary($this->date);
        if (null !== $this->mode) {
            $sql .= ', '.$sqlWalker->walkLiteral($this->mode);
        }

        return $sql.')';
    }

    /**
     * @throws QueryException
     */
    public function parse(Parser $parser): void
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        $this->date = $parser->ArithmeticPrimary();

        if (Lexer::T_COMMA === $parser->getLexer()->lookahead['type']) {
            $parser->match(Lexer::T_COMMA);
            $this->mode = $parser->Literal();
        }

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}
