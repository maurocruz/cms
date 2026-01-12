<?php
namespace Plinct\Cms\View\Fragment\Form;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\Controller\App;
use Plinct\Tool\ToolBox;
use Plinct\Web\Element\ElementFactory;
use Plinct\Web\Element\Form\Form as WebForm;
use Plinct\Web\Element\Form\FormInterface;

class Form extends FormDecorator implements RelationshipInterface
{
	/**
	 * @var string
	 */
	private string $tableHasPart;
	/**
	 * @var ?string
	 */
	private ?string $idHasPart = null;
	/**
	 * @var string
	 */
	private string $tableIsPartOf;

  /**
   * @param array|null $attributes
   * @return WebForm
   */
  public function create(array $attributes = null): WebForm
  {
    $this->form->attributes($attributes);
    return $this->form;
  }

	/**
	 * @param string $idform
	 */
	public function setIdform(string $idform): void
	{
		$this->form->attributes(['id'=>$idform]);
		$this->idform = $idform;
	}

	/**
	 * @return string|null
	 */
	public function getIdform(): ?string
	{
		return $this->idform;
	}

	/**
	 * @param mixed ...$mandatories
	 * @return Form
	 */
	public function addMandatories(...$mandatories): Form
	{
		if (!$this->getIdform()) {
			$this->setIdform($this->formName);
		}
		$this->mandatories = array_merge($this->mandatories, $mandatories);
		return $this;
	}

	/**
   * WRITE <SELECT> AN ELEMENT TO CHOOSE THE 'ADDITIONAL TYPE' OF A 'TYPE'
   *
   * @param string $class
   * @param string|null $value
   * @return array
   */
  public function selectAdditionalType(string $class = "thing", string $value = null): array
  {
      return parent::selectReady('additionalType', parent::getData(['class'=>$class]), $value);
  }

	/**
	 * WRITE <SELECT> AN ELEMENT TO CHOOSE THE 'CATEGORY' OF A 'TYPE'
	 *
	 * @param string $class
	 * @param string|null $value
	 * @return WebForm|FormInterface
	 */
  public function selectCategory(string $class = "thing", string $value = null): WebForm|FormInterface
  {
      $this->form->fieldset(self::selectReady('category', self::getData(['class'=>$class,'source'=>'category']), $value), _("Category"));
			return $this->form;
  }

  /**
   * WRITE <FORM> WITH SEARCH <INPUT> ELEMENT
   *
   * @param string $action
   * @param string $name
   * @param string|null $value
   * @return array
   */
  public function search(string $action, string $name, string $value = null): array
  {
    $form = ElementFactory::form(['class'=>'form']);
    // ACTION AND METHOD
    $form->action($action)->method('get');
    $form->content('<fieldset>');
    // CAPTION
    $form->content("<legend>"._("Search")."</legend>");
    // URI
    $queryString = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
    if ($queryString) {
      parse_str($queryString, $queryArray);
      if ($queryArray) {
        foreach ($queryArray as $nameQuery => $valueQuery) {
          $form->input($nameQuery, $valueQuery, "hidden");
        }
      }
    }
    // INPUT SEARCH
    $form->input($name, $value ?? '');
    // SUBMIT
    $form->input('', _("Submit") , 'submit');
    $form->content('</fieldset>');
    return $form->ready();
  }

	/**
	 * relationship one to many
	 * @param array|null $value
	 * @param string|null $orberBy
	 * @return array
	 */
	private function oneToMany(array $value = null, string $orberBy = null): array
	{
		$apiHost = CmsFactory::controller()->getApiHost();
		$table = lcfirst($this->tableIsPartOf);
		// items exist
		if ($value) {
			foreach ($value as $item) {
				$tb = ToolBox::typeBuilder($item);
				$idevent = $tb->getId();
				$idthing = $tb->getIdthing();
				$form = CmsFactory::view()->fragment()->form("form-relationshio",["class" => "form-basic form-relationship"])
					->action("/admin/$table/edit")->method("post");
				$form->input("typeHasPart", $this->tableHasPart, "hidden")
					->input("idHasPart", (string) $this->idHasPart, "hidden")
					->input("typeIsPartOf", $this->tableIsPartOf, "hidden")
					->input("idIsPartOf", $idthing, "hidden")
					->fieldsetWithInput("name", $item['name'], _($item['@type']) . " <a href=\"/admin/$table/edit/$idevent\">".("edit this")."</a>", "text", null, ["disabled"])
					->submitButtonDelete("/admin/$table/erase");
				$return[] = $form->ready();
			}
		}
		// new item
		$this->form->attributes(["class" => "form-basic form-relationship"]);
		$this->form->action("/admin/" . lcfirst($this->tableIsPartOf) . "/new")->method("post");
		$this->form->content("<fieldset><legend>"._('New')."</legend>");
		$this->form->content(['tag'=>'div','attributes'=>[
			'class'=>'plinct-shell',
			'data-action'=>'getItemType',
			'data-typeHasPart'=>$this->tableHasPart,
			'data-idHasPart'=>$this->idHasPart,
			'data-typeIsPartOf'=>$this->tableIsPartOf,
			'data-orderBy'=>$orberBy,
			'data-apihost'=>$apiHost,]]
		);
		$this->form->content("</fieldset>");
		$this->form->submitButtonSend(['class'=>'form-submit-button-send']);

		$return[] = $this->form->ready();

		return $return;
	}

	/**
	 * DEPRECATED
	 * @param string $tableHasPart
	 * @param int $idHasPart
	 * @param string $tableIsPartOf
	 * @param array|null $value
	 * @param string|null $orberBy
	 * @return array
	 */
    public function relationshipOneToMany(string $tableHasPart, int $idHasPart, string $tableIsPartOf, array $value = null, string $orberBy = null): array
    {
	    $this->tableHasPart = $tableHasPart;
	    $this->idHasPart = $idHasPart;
	    $this->tableIsPartOf = $tableIsPartOf;
			return $this->oneToMany($value, $orberBy);

    }

    /**
     * @param string $id
     * @param ?array $array
     * @return string
     */
    public function datalist(string $id, ?array $array): string
    {
        $content = null;
				if ($array) {
					foreach ($array as $value) {
						$content .= "<option value='$value'>";
					}
				}
        return "<datalist id='$id'>$content</datalist>";
    }

	/**
	 * @param string $legend
	 * @param string $property
	 * @param array|string $typesForChoose
	 * @param array|string|null $value
	 * @param array $attributes
	 */
	public function chooseType(string $legend, string $property, array|string $typesForChoose, array|string|null $value, array $attributes = []): void
	{
		if (is_array($value)) {
			$typeBuilder = ToolBox::typeBuilder($value);
			$idthing = $typeBuilder->getIdthing();
		} else {
			$idthing = $value;
		}
		$attributes['class'] = "plinct-shell";
		$attributes['data-action'] = "getItemType";
		$attributes['data-type'] = is_array($typesForChoose) ? implode(",",$typesForChoose) : $typesForChoose;
		$attributes['data-property'] = $property;
		$attributes['data-ispartof'] = $idthing;
		$attributes['data-legend'] = $legend;
		$attributes['data-apihost'] = App::getApiHost();
		$this->fieldset([ "tag" => "div", "attributes" => $attributes ], parent::writeLegend($property, $legend),['class'=>$property]);
	}
}
