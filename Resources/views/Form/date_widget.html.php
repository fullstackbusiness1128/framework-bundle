<?php if ($widget == 'text'): ?>
    <input type="text"
        <?php echo $view['form']->attributes() ?>
        name="<?php echo $name ?>"
        value="<?php echo $value ?>"
        <?php if ($read_only): ?>disabled="disabled"<?php endif ?>
        <?php if ($required): ?>required="required"<?php endif ?>
        <?php if ($class): ?>class="<?php echo $class ?>"<?php endif ?>
        <?php if ($max_length): ?>maxlength="<?php echo $max_length ?>"<?php endif ?>
    />
<?php else: ?>
    <div<?php echo $view['form']->attributes() ?>>
        <?php echo str_replace(array('{{ year }}', '{{ month }}', '{{ day }}'), array(
            $view['form']->widget($form['year']),
            $view['form']->widget($form['month']),
            $view['form']->widget($form['day']),
        ), $date_pattern) ?>
    </div>
<?php endif ?>
