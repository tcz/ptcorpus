<?php

namespace Ptcorpus\Twig;

class VaryTokenParser extends \Twig_TokenParser
{
	public function parse(\Twig_Token $token)
    {
    	$lineno = $token->getLine();
    	$stream = $this->parser->getStream();

        $seed = $this->parser->getExpressionParser()->parseExpression();
        $stream->expect(\Twig_Token::BLOCK_END_TYPE);

        $firstItem = $this->parser->subparse(array($this, 'decideForFork'));
        $items = array(
            array(
                'item' => $firstItem,
                'weight' => null,
            ),
        );

        while (true)
        {
            if (($val = $stream->next()->getValue()) == 'v') {
                $weight = $this->parser->getExpressionParser()->parseExpression();
                $stream->expect(\Twig_Token::BLOCK_END_TYPE);
                $item = $this->parser->subparse(array($this, 'decideForFork'));

                $items[] = array(
                    'item' => $item,
                    'weight' => $weight,
                );
            } else {
                $stream->expect(\Twig_Token::BLOCK_END_TYPE);
                break;
            }
        }

    	return new VaryNode($seed, $items, $lineno);
    }

    public function decideForFork(\Twig_Token $token)
    {
        return $token->test(array('endvary', 'v'));
    }

    public function getTag()
    {
        return 'vary';
    }
}