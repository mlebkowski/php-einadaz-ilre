<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/getDifferingPairs.php';

use PHPUnit\Framework\TestCase;

final class DifferingPairsCalculatorTest extends TestCase 
{
	public function test() 
	{
		$sut = getDifferingPairs($this->getLines('1-small'), $this->getLines('2-small'));
		$actual = iterator_to_array($sut);
		$expected = include __DIR__ . '/expected-result-small.php';
		self::assertSame($actual, $expected);
	}

	private function getLines(string $sourceName): \Generator
	{
		$handle = fopen(sprintf('%s/%s.jsonl', __DIR__, $sourceName), 'r');
		while (!feof($handle)) {
			$line = fgets($handle);
			if ($line) yield json_decode($line, true);
		}
	}
}
