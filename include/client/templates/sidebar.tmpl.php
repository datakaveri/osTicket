<?php
$BUTTONS = isset($BUTTONS) ? $BUTTONS : true;
?>
    <div >
<?php if ($BUTTONS) { ?>
        <div style= "width: 16rem ">
<p>
<?php
    if ($cfg->getClientRegistrationMode() != 'disabled'
        || !$cfg->isClientLoginRequired()) { ?>
            <a href="open.php" style="display:block; margin-bottom: 30px; padding: 1rem !important;" class="blue button"><?php
                echo __('Open a New Ticket');?></a>
</p>
<?php } ?>
<p>
            <a href="view.php" style="display:block; padding: 1rem !important;" class="green button"><?php
                echo __('Check Ticket Status');?></a>
</p>
        </div>
<?php } ?>
        <div class="content"><?php
    if ($cfg->isKnowledgebaseEnabled()
        && ($faqs = FAQ::getFeatured()->select_related('category')->limit(5))
        && $faqs->all()) { ?>
            <section><div class="header"><?php echo __('Featured Questions'); ?></div>
<?php   foreach ($faqs as $F) { ?>
            <div><a href="<?php echo ROOT_PATH; ?>kb/faq.php?id=<?php
                echo urlencode($F->getId());
                ?>"><?php echo $F->getLocalQuestion(); ?></a></div>
<?php   } ?>
            </section>
<?php
    }
    $resources = Page::getActivePages()->filter(array('type'=>'other'));
    if ($resources->all()) { ?>
            <section><div class="header"><?php echo __('Other Resources'); ?></div>
<?php   foreach ($resources as $page) { ?>
            <div><a href="<?php echo ROOT_PATH; ?>pages/<?php echo $page->getNameAsSlug();
            ?>"><?php echo $page->getLocalName(); ?></a></div>
<?php   } ?>
            </section>
<?php
    }
        ?></div>
    </div>

