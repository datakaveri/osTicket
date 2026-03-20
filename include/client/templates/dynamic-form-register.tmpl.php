<?php
/**
 * Registration layout: pairs non–block-level fields in two columns (2×2 for four fields).
 * Block-level fields (e.g. large textarea) stay full width.
 */
global $thisclient;
if (!$form->hasAnyVisibleFields($thisclient))
    return;

$isCreate = (isset($options['mode']) && $options['mode'] == 'create');

$registerFields = array();
foreach ($form->getFields() as $field) {
    try {
        if (!$field->isEnabled())
            continue;
    } catch (Exception $e) {
    }

    if ($isCreate) {
        if (!$field->isVisibleToUsers() && !$field->isRequiredForUsers())
            continue;
    } elseif (!$field->isVisibleToUsers()) {
        continue;
    }
    $registerFields[] = $field;
}
?>
<tr>
    <td colspan="2">
        <div class="form-header">
            <h3 style="display: none;"><?php echo Format::htmlchars($form->getTitle()); ?></h3>
            <?php
            $instructions = $form->getInstructions();
            if ($instructions && trim($instructions) && $instructions !== 'Please Describe Your Issue'): ?>
                <div><?php echo Format::display($instructions); ?></div>
            <?php endif; ?>
        </div>
    </td>
</tr>
<?php
$i = 0;
$n = count($registerFields);
while ($i < $n) {
    $field = $registerFields[$i];

    if ($field->isBlockLevel()) { ?>
<tr class="maha-register__field-row maha-register__field-row--full">
    <td colspan="2" class="maha-register__grid-cell maha-register__grid-cell--full" style="padding-top:10px;">
        <?php
        if ($field->isEditableToUsers() || $isCreate) {
            $field->render(array('client' => true));
            foreach ($field->errors() as $e) { ?>
        <div class="error"><?php echo $e; ?></div>
            <?php }
            $field->renderExtras(array('client' => true));
        } else {
            $val = '';
            if ($field->value)
                $val = $field->display($field->value);
            elseif (($a = $field->getAnswer()))
                $val = $a->display();
            echo $val;
        }
        ?>
    </td>
</tr>
        <?php
        $i++;
        continue;
    }

    $next = ($i + 1 < $n) ? $registerFields[$i + 1] : null;
    if ($next && !$next->isBlockLevel()) { ?>
<tr class="maha-register__field-row maha-register__grid-row">
    <td class="maha-register__grid-cell" style="padding-top:10px;">
        <?php if (!$field->isBlockLevel()) { ?>
        <label for="<?php echo $field->getFormName(); ?>"><span class="<?php
            if ($field->isRequiredForUsers()) echo 'required'; ?>">
                <?php echo Format::htmlchars($field->getLocal('label')); ?>
                <?php if (
                    $field->isRequiredForUsers() &&
                    ($field->isEditableToUsers() || $isCreate)
                ) { ?>
                <span class="error">*</span>
                <?php } ?>
            </span><?php
            if ($field->get('hint')) { ?>
            <br /><em style="color:gray;display:inline-block"><?php
                echo Format::viewableImages($field->getLocal('hint')); ?></em>
            <?php } ?>
            <br />
        <?php }
        if ($field->isEditableToUsers() || $isCreate) {
            $field->render(array('client' => true));
            ?></label><?php
            foreach ($field->errors() as $e) { ?>
        <div class="error"><?php echo $e; ?></div>
            <?php }
            $field->renderExtras(array('client' => true));
        } else {
            $val = '';
            if ($field->value)
                $val = $field->display($field->value);
            elseif (($a = $field->getAnswer()))
                $val = $a->display();
            echo sprintf('%s </label>', $val);
        }
        ?>
    </td>
    <td class="maha-register__grid-cell" style="padding-top:10px;">
        <?php if (!$next->isBlockLevel()) { ?>
        <label for="<?php echo $next->getFormName(); ?>"><span class="<?php
            if ($next->isRequiredForUsers()) echo 'required'; ?>">
                <?php echo Format::htmlchars($next->getLocal('label')); ?>
                <?php if (
                    $next->isRequiredForUsers() &&
                    ($next->isEditableToUsers() || $isCreate)
                ) { ?>
                <span class="error">*</span>
                <?php } ?>
            </span><?php
            if ($next->get('hint')) { ?>
            <br /><em style="color:gray;display:inline-block"><?php
                echo Format::viewableImages($next->getLocal('hint')); ?></em>
            <?php } ?>
            <br />
        <?php }
        if ($next->isEditableToUsers() || $isCreate) {
            $next->render(array('client' => true));
            ?></label><?php
            foreach ($next->errors() as $e) { ?>
        <div class="error"><?php echo $e; ?></div>
            <?php }
            $next->renderExtras(array('client' => true));
        } else {
            $val = '';
            if ($next->value)
                $val = $next->display($next->value);
            elseif (($a = $next->getAnswer()))
                $val = $a->display();
            echo sprintf('%s </label>', $val);
        }
        ?>
    </td>
</tr>
        <?php
        $i += 2;
    } else {
        $mahaPhoneGrid = ($field instanceof PhoneField)
            && ($field->isEditableToUsers() || $isCreate);
        if ($mahaPhoneGrid) {
            $_phoneCfg = $field->getConfiguration();
            $mahaPhoneGrid = !empty($_phoneCfg['ext']);
        }
        ?>
<tr class="maha-register__field-row maha-register__field-row--full">
    <td colspan="2" class="maha-register__grid-cell maha-register__grid-cell--full" style="padding-top:10px;">
        <?php if (!$field->isBlockLevel()) { ?>
        <label for="<?php echo $field->getFormName(); ?>"><?php
            if (!$mahaPhoneGrid) { ?>
            <span class="<?php
                if ($field->isRequiredForUsers()) echo 'required'; ?>">
                <?php echo Format::htmlchars($field->getLocal('label')); ?>
                <?php if (
                    $field->isRequiredForUsers() &&
                    ($field->isEditableToUsers() || $isCreate)
                ) { ?>
                <span class="error">*</span>
                <?php } ?>
            </span><?php
            }
            if ($field->get('hint')) { ?>
            <br /><em style="color:gray;display:inline-block"><?php
                echo Format::viewableImages($field->getLocal('hint')); ?></em>
            <?php } ?>
            <?php if (!$mahaPhoneGrid) { ?><br /><?php } ?>
        <?php }
        if ($field->isEditableToUsers() || $isCreate) {
            $renderOpts = array('client' => true);
            if ($mahaPhoneGrid) {
                $renderOpts['maha_register_phone_grid'] = true;
                $renderOpts['mode'] = $isCreate ? 'create' : null;
            }
            $field->render($renderOpts);
            ?></label><?php
            foreach ($field->errors() as $e) { ?>
        <div class="error"><?php echo $e; ?></div>
            <?php }
            $field->renderExtras(array('client' => true));
        } else {
            $val = '';
            if ($field->value)
                $val = $field->display($field->value);
            elseif (($a = $field->getAnswer()))
                $val = $a->display();
            echo sprintf('%s </label>', $val);
        }
        ?>
    </td>
</tr>
        <?php
        $i++;
    }
}
