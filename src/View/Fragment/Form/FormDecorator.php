<?php
namespace Plinct\Cms\View\Fragment\Form;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\Controller\App;
use Plinct\Cms\Controller\Request\Server\ServerFactory;
use Plinct\Cms\View\Fragment\ElementDecorator;
use Plinct\Web\Element\ElementFactory;
use Plinct\Web\Element\Form\FormInterface;

class FormDecorator extends ElementDecorator implements FormInterface
{
  /**
   * @var FormInterface
   */
  protected FormInterface $form;
	/**
	 * @var string|null
	 */
	protected ?string $idform = null;
	/**
	 * @var array
	 */
	protected array $mandatories = array();

  /**
   * @param array|null $attributes
   */
  public function __construct(array $attributes = null)
  {
    $this->form = ElementFactory::form($attributes);
    $this->element = $this->form;
  }

  /**
   * @param string $url
   * @return FormInterface
   */
  public function action(string $url): FormInterface
  {
    $this->form->action($url);
    return $this;
  }

  /**
   * @param string $method
   * @return FormInterface
   */
  public function method(string $method): FormInterface
  {
		$this->form->method($method);
		return $this;
  }

  /**
   * @param string $name
   * @param string|int $value
   * @param string $type
   * @param array|null $attributes
   * @return FormInterface
   */
  public function input(string $name, $value, string $type = 'text', array $attributes = null): FormInterface
  {
    $this->form->input($name,(string) $value, $type, $attributes);
    return $this;
  }

  /**
   * @param $content
   * @param string|null $label
   * @param array|null $attributes
   * @return FormInterface
   */
  public function fieldset($content, string $label = null, array $attributes = null): FormInterface
  {
    $fieldset = ElementFactory::element('fieldset',$attributes);
    $fieldset->content("<legend>$label</legend>");
    $fieldset->content($content);
    $this->form->content($fieldset->ready());
    return $this;
  }

  /**
   * @param string $name
   * @param string|null $value
   * @param string|null $legend
   * @param string $type
   * @param array|null $attributes
   * @param array|null $attributesInput
   * @return FormInterface
   */
  public function fieldsetWithInput(string $name, string $value = null, string $legend = null, string $type = 'text', array $attributes = null, array $attributesInput = null): FormInterface
  {
    $this->form->fieldsetWithInput($name, $value, self::writeLegend($name, $legend), $type, self::setAttr($attributes, $name), $attributesInput);
    return $this;
  }
  /**
   * @param string $name
   * @param array $value
   * @param array $list
   * @param string|null $legend
   * @param array|null $attributes
   * @return FormInterface
   */
  public function fieldsetWithSelect(string $name, $value, array $list, string $legend = null, array $attributes = null): FormInterface
  {
    $this->form->fieldsetWithSelect($name, $value, $list, self::writeLegend($name, $legend), self::setAttr($attributes, $name));
    return $this;
  }

  /**
   * @param string $name
   * @param string|null $value
   * @param string|null $legend
   * @param array|null $attributesFieldset
   * @param array $attributesTextarea
   * @return FormInterface
   */
  public function fieldsetWithTextarea(string $name, string $value = null, string $legend = null, array $attributesFieldset = null, array $attributesTextarea = []): FormInterface
  {
    $this->form->fieldsetWithTextarea($name, $value, self::writeLegend($name, $legend), self::setAttr($attributesFieldset, $name), $attributesTextarea);
    return $this;
  }

	/**
	 * @param string $name
	 * @param array $items
	 * @param $valueChecked
	 * @param string|null $legend
	 * @param array|null $attributes
	 * @return FormInterface
	 */
	public function fieldsetWithRadio(string $name, array $items, $valueChecked, string $legend = null, array $attributes = null): FormInterface
	{
		$this->form->fieldsetWithRadio($name, $items, $valueChecked, self::writeLegend($name, $legend), self::setAttr($attributes, $name));
		return $this;
	}

	/**
   * GET A DATA FROM SOLOINE SERVER
   * @param array $params
   * @return mixed
   */
  protected static function getData(array $params): mixed
  {
    $params = array_merge(['subClass'=>'true','format'=>'hierarchyText'], $params);
    return json_decode((ServerFactory::soloine())->get($params), true);
  }

	/**
	 * @param string $type
	 * @param string $legend
	 * @param string $propertyName
	 * @param int|null $value
	 * @return FormInterface
	 */
	public function relationshipOneToOne(string $type, string $legend, string $propertyName, int $value = null): FormInterface
	{
		$this->content([
			"<fieldset><legend>$legend</legend>",
			CmsFactory::view()->fragment()->reactShell($type)->getItemType($legend, $propertyName, $value)->ready(),
			"</fieldset>"
		]);
		return $this;
	}

  /**
   * WRITE A <SELECT> ELEMENT
   *
   * @param string $property
   * @param $data
   * @param null $value
   * @return array
   */
  protected static function selectReady(string $property, $data, $value = null): array
  {
    if (isset($data['status']) && $data['status'] == 'fail') {
      $element = ElementFactory::element('input',[ 'name'=>$property, 'type'=>'text', 'value'=>$value]);
    } else {
      $element = ElementFactory::element('select', ['class' => 'select-soloine', 'name' => $property]);
      if ($value) {
        $element->content("<option value='$value'>$value</option>");
      }
      if (isset($data['@graph'])) {
        $element->content("<option value=''>" . _("Select $property") . "</option>");
        foreach ($data['@graph'] as $key => $item) {
          $element->content("<option value='$key'>$item</option>");
        }
      } elseif (Isset($data['message'])) {
        $element->content("<option value=''>{$data['message']}</option>");
      } else {
        $element->content("<option value=''>" ._('Not available!')."</option>");
      }
    }
    return $element->ready();
  }

	/**
	 * @param string $id
	 * @param string $editorName
	 * @return void
	 */
  public function setEditor(string $id, string $editorName = 'editor'): void
  {
    if(App::getRichTextEditor()) {
			$this->form->content("<script>const $editorName = new RichTextEditor('#$id', config );</script>");
    }
  }

	/**
	 * @param string $name
	 * @param ?string $legend
	 * @return ?string
	 */
	protected function writeLegend(string $name, ?string $legend = null): ?string
	{
		if ($legend && in_array($name, $this->mandatories)) {
			$this->mandatories[$name] = $legend;
			unset($this->mandatories[array_search($name, $this->mandatories)]);
			$legend = $legend. " <span class='mandatory-field'>*</span>";
		}
		return $legend;
	}

	/**
	 * @return void
	 */
	private function writeMandatory(): void
	{
		$mandat = json_encode($this->mandatories);
		$formId = $this->idform;
		$this->content("<script>
document.getElementById('$formId').addEventListener('submit', function(e) {
  const mandatories = JSON.parse('$mandat');
  const elements = e.target.elements;
  for (let i=0; i < elements.length; i++) {
    const element = elements[i];
    const name = element.name;
    const value = element.value;
    if (Object.keys(mandatories).includes(name)) {
      if (value === '') {   
        e.preventDefault();
        element.focus();
        alert('Mandatory fields \'' + mandatories[name] + '\' is blank!');
        break;
      }
    }
  }  
});
</script>");
	}

	/**
	 * @param array $attributes
	 * @return FormInterface
	 */
  public function submitButtonSend(array $attributes = ['class'=>'form-submit-button-send']): FormInterface
  {
    $this->form->submitButtonSend($attributes);
    return $this;
  }

  /**
   * @param string|null $formaction
   * @param array $attributes
   * @return FormInterface
   */
  public function submitButtonDelete(string $formaction = null, array $attributes = ['class'=>'form-submit-button-delete']): FormInterface
  {
    $this->form->submitButtonDelete($formaction, $attributes);
    return $this;
  }

	/**
	 * @param ?array $attributes
	 * @param string $name
	 * @return array
	 */
	private static function setAttr(?array $attributes, string $name): array
	{
		if ($attributes) {
			if (array_key_exists('class', $attributes)) {
				$attributes['class'] = $name .' ' . $attributes['class'];
			}
			return  array_merge(['class'=>$name],$attributes);
		} else {
			return ['class'=> $name];
		}
	}

	/**
	 * @return array
	 */
	public function ready(): array
	{
		if(!empty($this->mandatories) && $this->idform) {
			$this->writeMandatory();
		}
		return $this->form->ready();
	}
}
