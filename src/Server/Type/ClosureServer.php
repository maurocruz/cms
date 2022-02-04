<?php

declare(strict_types=1);

namespace Plinct\Cms\Server\Type;

use Plinct\PDO\PDOConnect;

class ClosureServer
{
    private string $table;

    private string $action;

    private array $params;

    public function __construct($params)
    {
        $this->table = $params['table'] ?? null;
        $this->action = $params['action'] ?? null;
        unset($params['table']);
        unset($params['action']);

        $this->params = $params;
    }

    private function new()
    {
        $columns = [];
        $values = [];
        $sql = "INSERT INTO `$this->table` ";
        foreach ($this->params as $key => $item) {
            $columns[] = "`$key`";
            $values[] = "'$item'";
        }
        $sql .= "(" . implode(',',$columns) . ")";
        $sql .= " VALUES ";
        $sql .= "(" . implode(',',$values) . ");";

        PDOConnect::run($sql);
    }

    private function edit()
    {
        $setValues = [];
        $id = $this->params['id'] ?? $this->params["id$this->table"] ?? null;

        foreach ($this->params as $key => $value) {
            $setValues[] = "`$key`='$value'";
        }

        PDOConnect::run("UPDATE `$this->table` SET " . implode(',',$setValues) . " WHERE `id$this->table` = '$id';");
    }

    public function getReturn()
    {
        if ($this->action == 'new') {
            $this->new();
        }
        if ($this->action == "edit") {
            $this->edit();
        }
        return filter_input(INPUT_SERVER, 'HTTP_REFERER');
    }
}