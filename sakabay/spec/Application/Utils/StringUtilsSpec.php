<?php

namespace spec\App\Application\Utils;

use App\Application\Utils\StringUtils;
use PhpSpec\ObjectBehavior;
use voku\helper\StopWords;

class StringUtilsSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(StringUtils::class);
    }

    public function it_should_get_snake()
    {
        $this::fromCamelCaseToSnakeCase('deadLine')->shouldReturn('dead_line');
        $this::fromCamelCaseToSnakeCase('DeadLine')->shouldReturn('dead_line');
        $this::fromCamelCaseToSnakeCase('FaitMarquant')->shouldReturn('fait_marquant');
        $this::fromCamelCaseToSnakeCase('FaitMarquant', 's_')->shouldReturn('faits_marquant');
        $this::fromCamelCaseToSnakeCase('DeadLine', 's_')->shouldReturn('deads_line');
    }

    public function it_should_get_camel()
    {
        $this::fromSnakeCaseToCamelCase('lb_nom_')->shouldReturn('lbNom');
        $this::fromSnakeCaseToCamelCase('je_suis_ton_père')->shouldReturn('jeSuisTonPère');
        $this::fromSnakeCaseToCamelCase('papa_!')->shouldReturn('papa!');
        $this::fromSnakeCaseToCamelCase('è_é')->shouldReturn('èÉ');
    }

    public function it_should_get_namefilecsvforload()
    {
        $this::getNameFileCsvForLoad('FixFaitMarquant')->shouldReturn('faits_marquants.csv');
        $this::getNameFileCsvForLoad('FixDateComiteCrti')->shouldReturn('dates_comites_crtis.csv');
    }

    public function it_should_list_of_keywords()
    {
        $this::listOfKeywords(null)->shouldEqual(['']);
        $this::listOfKeywords('')->shouldEqual(['']);
        $this::listOfKeywords('lorem')->shouldEqual(['lorem']);
        $this::listOfKeywords('lorem ipsum')->shouldEqual(['lorem', 'ipsum']);
        $this::listOfKeywords('lOrem ipsUm')->shouldEqual(['lorem', 'ipsum']);
        $this::listOfKeywords('Lorem Ipsum')->shouldEqual(['lorem', 'ipsum']);
        $this::listOfKeywords('LOREM IPSUM')->shouldEqual(['lorem', 'ipsum']);
        $this::listOfKeywords('LOREM IPSUM DOLOREM')->shouldEqual(['lorem', 'ipsum', 'dolorem']);
    }

    public function it_should_return_a_list_of_keywords_and_expressions()
    {
        $this::listOfKeywordsAndExpressions(null)->shouldEqual([]);
        $this::listOfKeywordsAndExpressions('')->shouldEqual([]);
        $this::listOfKeywordsAndExpressions('lorem')->shouldEqual(['lorem']);
        $this::listOfKeywordsAndExpressions('lorem ipsum')->shouldEqual(['lorem', 'ipsum']);
        $this::listOfKeywordsAndExpressions('lOrem ipsUm')->shouldEqual(['lorem', 'ipsum']);
        $this::listOfKeywordsAndExpressions('Lorem Ipsum')->shouldEqual(['lorem', 'ipsum']);
        $this::listOfKeywordsAndExpressions('LOREM IPSUM')->shouldEqual(['lorem', 'ipsum']);
        $this::listOfKeywordsAndExpressions('LOREM "IPSUM DOLOREM"')->shouldEqual(['lorem', '"ipsum dolorem"']);
        $this::listOfKeywordsAndExpressions('"LOREM IPSUM" DOLOREM')->shouldEqual(['"lorem ipsum"', 'dolorem']);
        $this::listOfKeywordsAndExpressions('sous-marin')->shouldEqual(['sous-marin']);
    }

    public function it_should_truncate_a_given_string()
    {
        $string = 'tést 1234567890';
        $this::truncate($string, 4)->shouldReturn('tést...');
    }

    /**
     * getFrenchStopWords() test.
     */
    public function it_should_get_french_stop_word()
    {
        $expectedStopWords = new StopWords();
        $this::getFrenchStopWords()->shouldReturn($expectedStopWords->getStopWordsFromLanguage('fr'));
    }

    /**
     * test of it_should_mb_lcfirst($string).
     */
    public function it_should_mb_lcfirst()
    {
        $this::mb_lcfirst('É بنمه mĄkA ')->shouldReturn('é بنمه mĄkA ');
    }
}
