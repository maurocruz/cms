<?php
namespace Plinct\Cms\Http\View\Template;

abstract class TemplateAbstract
{
	protected array $HTML = ['tag'=>'html'];
	private array $HEAD = ['tag'=>'head'];
	private array $BODY = ['tag'=>'body'];
	private array $CONTENT = ['tag'=>'div', 'attributes'=>['class'=>'content']];
	private array $HEADER = ['tag'=>'header', 'attributes'=>['class'=>'header']];
	private array $MAIN = ['tag'=>'main', 'attributes'=>['class'=>'main']];
	private array $FOOTER = ['tag'=>'footer', 'attributes'=>['class'=>'footer']];

	/**
	 * @param $content
	 * @return void
	 */
	protected function addHTML($content): void
	{
		$this->HTML['content'][] = $content;
	}

	/**
	 * @param $content
	 * @return void
	 */
	public function addHead($content): void
	{
		$this->HEAD['content'][] = $content;
	}

	/**
	 * @param $content
	 * @return TemplateAbstract
	 */
	public function addHeader($content): static
	{
		$this->HEADER['content'][] = $content;
		return $this;
	}

	/**
	 * @param $content
	 * @return void
	 */
	public function addMain($content): void
	{
		$this->MAIN['content'][] = $content;
	}

	/**
	 * @return array|string[]
	 */
	protected function getHTML(): array
	{
		return $this->HTML;
	}

	/**
	 * @return array
	 */
	protected function getBODY(): array
	{
		$this->CONTENT['content'][] = [
			$this->HEADER,
			$this->MAIN,
			$this->FOOTER
		];
		$this->BODY['content'][] = $this->CONTENT;
		return $this->BODY;
	}

	/**
	 * @return array
	 */
	protected function getHEAD(): array
	{
		return $this->HEAD;
	}
}
