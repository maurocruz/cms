<?php
declare(strict_types=1);
namespace Plinct\Cms\View\Fragment\Form;

use Plinct\Cms\CmsFactory;
use Plinct\Tool\ArrayTool;
use Plinct\Web\Element\ElementFactory;
use Plinct\Web\Element\ElementInterface;
use Plinct\Web\Element\Form\Form as WebForm;
use Plinct\Web\Element\Form\FormInterface;

class Form extends FormDecorator implements FormInterface, RelationshipInterface, ElementInterface
{
	/**
	 * @var string
	 */
	private string $tableHasPart;
	/**
	 * @var int
	 */
	private int $idHasPart;
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
   * WRITE <SELECT> ELEMENT TO CHOOSE THE 'ADDITIONAL TYPE' OF A 'TYPE'
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
	 * WRITE <SELECT> ELEMENT TO CHOOSE THE 'CATEGORY' OF A 'TYPE'
	 *
	 * @param string $class
	 * @param string|null $value
	 * @return WebForm|FormInterface
	 */
  public function selectCategory(string $class = "thing", string $value = null)
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
	 * For print forms of the relationships tables
	 * @param string $tableHasPart
	 * @param int $idHasPart
	 * @param string $tableIsPartOf
	 * @return RelationshipInterface
	 */
	public function relationship(string $tableHasPart, int $idHasPart, string $tableIsPartOf): RelationshipInterface
	{
		$this->tableHasPart = $tableHasPart;
		$this->idHasPart = $idHasPart;
		$this->tableIsPartOf = $tableIsPartOf;
		return $this;
	}

	/**
	 * Relationship one to one
	 * @param string $propertyName
	 * @param array|null $value
	 * @param string|null $orberBy
	 * @return array
	 */
	public function oneToOne(string $propertyName, array $value = null, string $orberBy = null): array
	{
		$table = lcfirst($this->tableIsPartOf);
		$this->attributes(["class" => "formPadrao form-relationship"]);
		$this->action("/admin/$this->tableHasPart/edit")->method("post");

		if ($value) {
			$value = array_key_exists(0,$value) ? $value[0] : $value;
			$id = ArrayTool::searchByValue($value['identifier'], "id")['value'];

			$this->input("id$this->tableHasPart", (string) $this->idHasPart, "hidden")
				->fieldsetWithInput('name',$value['name'],_($value['@type']) . " <a href=\"/admin/$table/edit/$id\">"._("Edit")."</a>", "text", null, [ "disabled" ])
				->input($propertyName, '', 'hidden')
				->submitButtonDelete("/admin/$this->tableHasPart/edit");
		} else {
			$this->content("<div class='add-existent' data-type='$table' data-propertyName='$propertyName' data-tableHasPart='$this->tableHasPart' data-idHasPart='$this->idHasPart'  data-orderBy='$orberBy'></div>");
			CmsFactory::view()->addBundle('relationship');
		}

		return $this->ready();
	}

	/**
	 * relationship one to many
	 * @param array|null $value
	 * @param string|null $orberBy
	 * @return array
	 */
	public function oneToMany(array $value = null, string $orberBy = null): array
	{
		$table = lcfirst($this->tableIsPartOf);

		if ($value) {
			foreach ($value as $item) {
				$id = ArrayTool::searchByValue($item['identifier'], "id")['value'];
				$form = CmsFactory::view()->fragment()->form(["class" => "formPadrao"])
					->action("/admin/$table/edit")->method("post");
				$form->input("tableHasPart", $this->tableHasPart, "hidden")
					->input("idHasPart", (string) $this->idHasPart, "hidden")
					->input("tableIsPartOf", $this->tableIsPartOf, "hidden")
					->input("idIsPartOf", $id, "hidden")
					->fieldsetWithInput("name", $item['name'], _($item['@type']) . " <a href=\"/admin/$table/edit/$id\">".("edit this")."</a>", "text", null, ["disabled"])
					->submitButtonDelete("/admin/$table/erase");
				$return[] = $form->ready();
			}
		}
		$this->form->attributes(["class" => "formPadrao form-relationship"]);
		$this->form->action("/admin/" . lcfirst($this->tableIsPartOf) . "/new")->method("post");
		$this->form->input("tableHasPart", $this->tableHasPart, "hidden")
			->input("idHasPart", (string) $this->idHasPart, "hidden")
			->content([ "tag" => "div", "attributes" => [ "class" => "add-existent", "data-type" => $table, "data-idHasPart" => $this->idHasPart, "data-orderBy" => $orberBy  ] ]);

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
}
