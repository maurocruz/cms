<?php
declare(strict_types=1);
namespace Plinct\Cms\View\Fragment\Form;

use Plinct\Cms\Controller\App;
use Plinct\Cms\Controller\Response\Fragment\ElementDecorator;
use Plinct\Cms\Controller\Request\Server\ServerFactory;
use Plinct\Web\Element\ElementFactory;
use Plinct\Web\Element\Form\FormInterface;

class FormDecorator extends ElementDecorator implements FormInterface
{
  /**
   * @var FormInterface
   */
  protected FormInterface $form;

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
   * @param string $value
   * @param string $type
   * @param array|null $attributes
   * @return FormInterface
   */
  public function input(string $name, string $value, string $type = 'text', array $attributes = null): FormInterface
  {
     $this->form->input($name, $value, $type, $attributes);
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
      $this->form->fieldsetWithInput($name, $value, $legend, $type, $attributes, $attributesInput);
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
      $this->form->fieldsetWithSelect($name, $value, $list, $legend, $attributes);
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
      $this->form->fieldsetWithTextarea($name, $value, $legend, $attributesFieldset, $attributesTextarea);
      return $this;
  }

  /**
   * GET A DATA FROM SOLOINE SERVER
   * @param array $params
   * @return mixed
   */
  protected static function getData(array $params) {
      $params = array_merge(['subClass'=>'true','format'=>'hierarchyText'], $params);
      return json_decode((ServerFactory::soloine())->get($params), true);
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
  public function setEditor(string $id, string $editorName = 'editor')
  {
      if(App::getRichTextEditor())   $this->form->setEditor($id, $editorName, App::getStaticFolder());
  }

  /**
   * @param array|null $attributes
   * @return FormInterface
   */
  public function submitButtonSend(array $attributes = null): FormInterface
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
}
