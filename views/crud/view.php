<?php $table = $vars['model']->table; ?>
<?php $alias = $vars['model']->alias; ?>
<?php $row = $vars['model']->row; ?>
<?php $id = $row[$vars['model']->primary_key]; ?>
    <div class="container">
      <h1><?php h($row[$vars['model']->display]); ?></h1>
<?php if (fallback('pre_view.php', "views/{$table}") !== NULL) load_view('pre_view', $vars, $table); ?>
      <div class="text-right" style="margin-top:-46px">
            <button type="button" class="btn" style="visibility:hidden;">&nbsp;</button>
<?php foreach ($vars['model']->action_hash as $key => $row_action): ?>
<?php if ($row_action !== 'ajax') continue; ?>
            <button onclick="$.ajax({url: '<?php href("{$table}/{$key}/{$id}"); ?>', success: function(){<?php if (isset($vars['model']->success_hash[$key])) echo $vars['model']->success_hash[$key]; ?>},});" class="btn btn-default"><?php l("crud_{$key}"); ?></button>
<?php endforeach; ?>
<?php foreach ($vars['model']->action_hash as $key => $row_action): ?>
<?php if ($row_action !== 'row') continue; ?>
            <a href="<?php href("{$table}/{$key}/{$id}"); ?>" class="btn btn-default"><?php l("crud_{$key}"); ?></a>
<?php endforeach; ?>
<?php foreach ($vars['model']->action_hash as $key => $row_action): ?>
<?php if ($row_action !== 'edit') continue; ?>
            <button type="button" class="btn btn-default" data-toggle="modal" id="<?php h($alias); ?><?php h(ucfirst($key)); ?>Show<?php h($id); ?>" data-target="#<?php h($alias); ?><?php h(ucfirst($key)); ?>" onclick="<?php h($alias); ?>Pre<?php h(ucfirst($key)); ?>('<?php h($id); ?>');"><?php l("crud_{$key}"); ?></button>
<?php endforeach; ?>
<?php foreach ($vars['model']->action_hash as $key => $row_action): ?>
<?php if ($row_action !== 'delete') continue; ?>
            <button type="button" class="btn btn-default" data-toggle="modal" id="<?php h($alias); ?><?php h(ucfirst($key)); ?>Show<?php h($id); ?>" data-target="#<?php h($alias); ?><?php h(ucfirst($key)); ?>" onclick="<?php h($alias); ?>Pre<?php h(ucfirst($key)); ?>('<?php h($id); ?>');"><?php l("crud_{$key}"); ?></button>
<?php endforeach; ?>
      </div>
      <table class="table">
<?php foreach ($vars['model']->select_hash as $key => $select): ?>
<?php if (in_array($key, $vars['model']->hidden_list)) continue; ?>
<?php if ($key === $vars['model']->display) continue; ?>
        <tr>
          <th style="white-space:nowrap; width:0; text-align:right;"><?php l($key); ?></th>
          <td><?php load_view('col', ['row' => $row, 'key' => $key], $vars['model']->table); ?></td>
        </tr> 
<?php endforeach; ?>
      </table>
<?php if (fallback('post_view.php', "views/{$table}") !== NULL) load_view('post_view', $vars, $table); ?>
    </div>
<?php load_view('row_action', $vars, $table); ?>
<div class="container">
  <!-- Nav tabs -->
  <ul class="nav nav-tabs" role="tablist">
<?php $active = TRUE; ?>
<?php foreach ($vars['model']->has_hash as $key => $has): ?>
<?php if ( ! in_array('index', array_keys($has['model']->action_hash))) continue; ?>
    <li role="presentation"<?php if ($active === TRUE): ?> class="active"<?php endif; ?>><a href="#<?php h($key); ?>" role="tab" data-toggle="tab"><?php l($key); ?></a></li>
<?php $active = FALSE; ?>
<?php endforeach; ?>
  </ul>
  <!-- Tab panes -->
  <div class="tab-content">
<?php $active = TRUE; ?>
<?php foreach ($vars['model']->has_hash as $key => $has): ?>
<?php if ( ! in_array('index', array_keys($has['model']->action_hash))) continue; ?>
    <div role="tabpanel" class="tab-pane<?php if ($active === TRUE): ?> active<?php endif; ?>" id="<?php h($key); ?>">
<?php load_view('index', $has, $has['model']->table); ?>
    </div>
<?php $active = FALSE; ?>
<?php endforeach; ?>
  </div>
</div>
