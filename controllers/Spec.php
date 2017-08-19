<?php
class Spec extends Controller {

	public function __construct()
	{
		parent::__construct();
	}

	// トップページ
	public function index()
	{
		load_template(__FUNCTION__);
	}

	// エンティティ表示
	public function table($actor, $class)
	{
		load_template(__FUNCTION__, ['actor' => $actor, 'class' => $class]);
	}

	// DBからモデルを生成
	public function compile()
	{
		load_library('db');
		$table_list = "'".implode("', '", config('uri')['table_list'])."'";
		$actor_list = "'".implode("', '", array_reverse(array_keys(config('uri')['actor_hash'])))."'";
		$action_list = "'".implode("', '", config('uri')['action_list'])."'";
		$alias_list = "'".implode("', '", array_merge(config('uri')['table_list'], config('uri')['alias_list']))."'";
		$result = library('db')->query("SELECT * FROM `directive` ORDER BY
`directive_table` IS NULL DESC, FIELD(`directive_table`, {$table_list}),
`directive_alias` IS NULL DESC, FIELD(`directive_alias`, {$alias_list}),
`directive_action` IS NULL DESC, FIELD(`directive_action`, {$actor_list}),
`directive_actor` IS NULL DESC, FIELD(`directive_actor`, {$actor_list}),
`directive_method` IS NULL DESC, FIELD(`directive_method`, 'overwrite', 'append', 'remove'),
`directive_directive` IS NULL DESC, FIELD(`directive_directive`, 'primary_key', 'display', 'use_card', 'has_hash', 'action_list', 'row_action_hash', 'select_hash', 'hidden_list', 'set_list', 'fixed_hash', 'success_hash', 'join_hash', 'where_list', 'where_hash', 'order_by_hash', 'limit_list'),
`directive_id` ASC
");
		while (($row = $result->fetch_assoc()))
		{
			// エイリアス終了
			if ((isset($last_row) && $row['directive_alias'] !== $last_row['directive_alias']) && $last_row['directive_alias'] !== NULL)
			{
				$indent = substr($indent, 0, strlen($indent) - 1);
				echo "{$indent}}\n";
			}

			// アクター終了
			if ((isset($last_row) && $row['directive_action'] !== $last_row['directive_action']) && $last_row['directive_action'] !== NULL)
			{
				$indent = substr($indent, 0, strlen($indent) - 1);
				echo "{$indent}}\n";
			}

			// アクター終了
			if ((isset($last_row) && $row['directive_actor'] !== $last_row['directive_actor']) && $last_row['directive_actor'] !== NULL)
			{
				$indent = substr($indent, 0, strlen($indent) - 1);
				echo "{$indent}}\n";
			}

			// テーブル終了
			if (isset($last_row) && $row['directive_table'] !== $last_row['directive_table'])
			{
				echo "\t}\n}\n";
			}

			// テーブル開始
			if ( ! isset($last_row) OR $row['directive_table'] !== $last_row['directive_table'])
			{
				$indent = "\t\t";
				echo "<?php\nclass ".ucfirst($row['directive_table'])."_model extends Crud_model {\n\n\tpublic function __construct(\$params)\n\t{\n\t\tparent::__construct(\$params);\n\n";
			}

			// アクター開始
			if (( ! isset($last_row) OR $row['directive_actor'] !== $last_row['directive_actor']) && $row['directive_actor'] !== NULL)
			{
				echo "\n{$indent}if (\$this->actor === '{$row['directive_actor']}')\n{$indent}{\n";
				$indent .= "\t";
			}

			// アクション開始
			if (( ! isset($last_row) OR $row['directive_action'] !== $last_row['directive_action']) && $row['directive_action'] !== NULL)
			{
				echo "\n{$indent}if (\$this->actor === '{$row['directive_action']}')\n{$indent}{\n";
				$indent .= "\t";
			}

			// エイリアス開始
			if (( ! isset($last_row) OR $row['directive_alias'] !== $last_row['directive_alias']) && $row['directive_alias'] !== NULL)
			{
				echo "\n{$indent}if (\$this->alias === '{$row['directive_alias']}')\n{$indent}{\n";
				$indent .= "\t";
			}

			switch ($row['directive_method'])
			{
			case 'overwrite':
				switch ($row['directive_directive'])
				{
				case 'primary_key':
				case 'display':
				case 'use_card':
					$value = $row['directive_value'];
					break;
				default:
					show_500("Illigal key for method '{$row['directive_method']}' ('{$row['directive_directive']}')");
					break;
				}
				echo "{$indent}\$this->{$row['directive_method']}('{$row['directive_directive']}', {$value});\n";
				break;
			case 'append':
				switch ($row['directive_directive'])
				{
				case 'action_list':
				case 'hidden_list':
				case 'set_list':
				case 'where_list':
				case 'limit_list':
					$value = $row['directive_value'];
					break;
				case 'has_hash':
				case 'row_action_hash':
				case 'select_hash':
				case 'fixed_hash':
				case 'success_hash':
				case 'join_hash':
				case 'where_hash':
				case 'order_by_hash':
					$value = "'{$row['directive_key']}', {$row['directive_value']}";
					break;
				default:
					show_500("Illigal key for method '{$row['directive_method']}' ('{$row['directive_directive']}')");
					break;
				}
				echo "{$indent}\$this->{$row['directive_method']}('{$row['directive_directive']}', {$value});\n";
				break;
			case 'remove':
				switch ($row['directive_directive'])
				{
				case 'action_list':
				case 'hidden_list':
				case 'set_list':
				case 'where_list':
				case 'limit_list':
					$value = $row['directive_value'];
					break;
				case 'has_hash':
				case 'row_action_hash':
				case 'select_hash':
				case 'fixed_hash':
				case 'success_hash':
				case 'join_hash':
				case 'where_hash':
				case 'order_by_hash':
					$value = $row['directive_value'];
					break;
				default:
					show_500("Illigal key for method '{$row['directive_method']}' ('{$row['directive_directive']}')");
					break;
				}
				echo "{$indent}\$this->{$row['directive_method']}('{$row['directive_directive']}', {$value});\n";
				break;
			default:
				show_500("Unknown Method ('{$row['directive_method']}')");
				break;
			}
			$last_row = $row;
		}

		// エイリアス終了
		if ($last_row['directive_alias'] !== NULL)
		{
			$indent = substr($indent, 0, strlen($indent) - 1);
			echo "{$indent}}\n";
		}

		// アクター終了
		if ($last_row['directive_action'] !== NULL)
		{
			$indent = substr($indent, 0, strlen($indent) - 1);
			echo "{$indent}}\n";
		}

		// アクター終了
		if ($last_row['directive_actor'] !== NULL)
		{
			$indent = substr($indent, 0, strlen($indent) - 1);
			echo "{$indent}}\n";
		}

		// テーブル終了
		echo "\t}\n}\n";
	}
}
