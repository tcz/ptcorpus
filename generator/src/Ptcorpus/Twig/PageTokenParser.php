<?php

namespace Ptcorpus\Twig;

class PageTokenParser extends \Twig_TokenParser
{
	public function parse(\Twig_Token $token)
    {
    	$lineno = $token->getLine();
    	$stream = $this->parser->getStream();

    	$title = $this->parser->getExpressionParser()->parseExpression();
    	$stream->expect(\Twig_Token::BLOCK_END_TYPE);

    	$body = $this->parser->subparse(array($this, 'decidePageEnd'));
    	$stream->next();
    	$stream->expect(\Twig_Token::BLOCK_END_TYPE);

    	return new PageNode($title, $body, $lineno);
    }

    public function decidePageEnd(\Twig_Token $token)
    {
        return $token->test('endpage');
    }

    public function getTag()
    {
        return 'page';
    }
}